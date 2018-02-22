<?php namespace Aike\Web\Model\Controllers;

use DB;
use Auth;
use Input;
use Request;
use Validator;
use Config;
use Schema;

use Aike\Web\Model\Step;
use Aike\Web\Model\Model;
use Aike\Web\Model\Field;

use Aike\Web\Index\Attachment;

use Dialog;
use Notify;
use User;
use AES;

use Aike\Web\Index\Controllers\DefaultController;

class ProcessController extends DefaultController
{
    public $permission = ['turn', 'log', 'draft', 'step', 'user'];

    /**
     * 创建审批
     */
    public function turnAction()
    {
        $key  = Input::get('key');
        $keys = AES::decrypt($key, config('app.key'));
        list($table, $id) = explode('.', $keys);

        // 新建流程时编号为0
        if ($id) {
            $row = DB::table($table)->find($id);
        } else {
            $row['step_number'] = 1;
        }

        if (Request::method() == 'POST') {
            $gets = Input::get();

            if (!$gets['step_number']) {
                return $this->json('审批进程必须选择。');
            }

            $model = Model::with('fields', 'children.fields')->where('table', $table)->first();

            // 主表和子表字段
            $fields[$table] = $model->fields->toArray();
            $slaves = [];
            foreach ($model->children as $children) {
                $slaves[$children->table] = $children->table;
                $fields[$children->table] = $children->fields->toArray();
            }

            // 转到步骤条件检查
            if ($gets['step_status'] == 'next') {
                $check = Step::where('number', $row['step_number'])
                ->where('model_id', $model->id)
                ->first();

                // 检查字段检查
                $validates = json_decode($check->field, true);
                if ($validates) {
                    $rules = $names = [];
                    $regex = Model::regular();

                    // 验证规则生成
                    foreach ($validates as $t => $validate) {
                        if ($t == '@attachment') {
                            continue;
                        }
                        
                        $split = isset($slaves[$t]) ? '*.' : '';
                        
                        foreach ((array)$fields[$t] as $v) {
                            // 是子表
                            $v['field'] = $split.$v['field'];
                            $names[$t.'.'.$v['field']] = $v['name'];
                        }
                        
                        foreach ($validate as $k => $v) {
                            if ($v && $v['v']) {
                                // 是子表
                                $k = $split.$k;
                                $rules[$t.'.'.$k] = $regex[$v['v']]['regex'];
                            }
                        }
                    }

                    if (sizeof($rules)) {
                        $v = Validator::make($gets, $rules, [], $names);
                        if ($v->fails()) {
                            // 字段验证错误
                            $errors = $v->errors()->all();
                            return $this->json($errors[0]);
                        }
                    }
                }
            }

            // 获取转到步骤数据
            $step = Step::where('number', $gets['step_number'])
            ->where('model_id', $model->id)
            ->first();

            // 设置步骤完成字段状态
            $status = $step['type'] == 'done' ? 1 : (int)$row['status'];
            $gets[$table]['status'] = $status;

            // 保存数据
            $this->store($model, $gets, $id);

            // 写入审核日志
            DB::table('model_step_log')->insert([
                'model_id'    => $model->id,
                'table'       => $table,
                'table_id'    => $id,
                'status'      => $status,
                'step_number' => $row['step_number'],
                'step_status' => $gets['step_status'],
                'description' => $gets['description'],
                'created_id'  => Auth::id(),
                'created_by'  => Auth::user()->nickname,
            ]);

            // 通知
            $this->notify($model, $step, $row, $gets);

            session()->flash('message', '审批成功。');
            
            return $this->json($gets['step_referer'], true);
        }

        $model = Model::where('table', $table)->first();

        $step = Step::where('model_id', $model->id)
        ->where('number', $row['step_number'])
        ->first();

        $join  = explode(',', $step->join);
        $steps = Step::where('model_id', $model->id)
        ->whereIn('number', $join)
        ->get();

        $notify = (array)json_decode($step->notify, true);

        return $this->render([
            'steps'  => $steps,
            'step'   => $step,
            'notify' => $notify,
            'key'    => $key,
        ]);
    }

    /**
     * 保存草稿
     */
    public function draftAction()
    {
        $key  = Input::get('key');
        $keys = AES::decrypt($key, config('app.key'));
        list($table, $id) = explode('.', $keys);

        if (Request::method() == 'POST') {
            $gets = Input::get();

            if ($id == 0) {
                $gets['step_number'] = 1;
                $gets['step_status'] = '';
            }

            $model = Model::with('fields', 'children.fields')->where('table', $table)->first();
            
            // 保存数据
            $this->store($model, $gets, $id);

            session()->flash('message', '草稿保存成功。');
            return $this->json('草稿保存成功。', true);
        }
    }

    public function store($model, $gets, $id)
    {
        // 主表名
        $table = $model->table;

        // 主表数据
        $master = $gets[$table];

        if ($gets['attachment']) {
            $master['attachment'] = join(',', $gets['attachment']);
        }

        if ($gets['step_number']) {
            $master['step_number'] = $gets['step_number'];
        }

        if ($gets['step_status']) {
            $master['step_status'] = $gets['step_status'];
        }

        $datas = $deleteds = [];

        foreach ($model->children as $children) {
            $deleteds[$children->table] = array_pull($gets, $children->table.'.deleted');

            $datas[] = [
                'table'    => $children->table,
                'type'     => $children->type,
                'relation' => $children->relation,
                'data'     => (array)$gets[$children->table]
            ];
        }

        foreach ($model->fields as $column) {
            if ($column['form_type'] == 'checkbox') {
                $field = $column['field'];
                $value = $master[$field];
                if ($value) {
                    $master[$field] = join(',', $value);
                }
            }
        }

        // 临时解决方案，增加过滤器用于模块数据解耦
        if ($gets['step_filter']['master']) {
            $master = $gets['step_filter']['master']($master);
        }

        // 更新主表
        if ($id) {
            DB::table($table)->where('id', $id)->update($master);
        } else {
            $id = DB::table($table)->insertGetId($master);
        }

        foreach ($datas as $data) {
            $rows = $data['data'];

            // 多行子表
            if ($data['type']) {
                // 删除列表数据
                $deleted = $deleteds[$data['table']];
                if (sizeof($deleted)) {
                    DB::table($data['table'])->whereIn('id', $deleted)->delete();
                }

                foreach ($rows as $row) {
                    // 关联键编号
                    $row[$data['relation']] = $id;

                    if ($row['id']) {
                        DB::table($data['table'])->where('id', $row['id'])->update($row);
                    } else {
                        DB::table($data['table'])->insert($row);
                    }
                }
            
                // 附表暂时无用
            } else {
                /*
                if($rows['id']) {
                    DB::table($data['table'])->where('id', $rows['id'])->update($rows);
                } else {
                    DB::table($data['table'])->insert($rows);
                }
                */
            }
        }

        // 附件发布
        Attachment::publish();

        return true;
    }

    /**
     * 获取办理步骤
     */
    public function stepAction()
    {
        $type     = Input::get('type');
        $model_id = Input::get('model_id');
        $number   = Input::get('number');

        $model = Model::find($model_id);

        $step = Step::where('model_id', $model_id)
        ->where('number', $number)
        ->first();

        if ($type == 'next') {
            $steps = Step::checkCondition();
        } else {
            $steps = Step::where('model_id', $model_id)
            ->where('number', $number - 1)
            ->get();
        }

        $e = '';
        foreach ($steps as $step) {
            $e .= '<div class="radio"><label class="i-checks i-checks-sm">';
            $e .= '<input type="radio" onclick="get_step_user(this);" name="step_number" value="'.$step['number'].'"><i></i> 第 <span class="badge bg-primary">'.$step['number'].'</span> '.$step['name'];
            $e .= '</label></div>';
        }
        return response($e);
    }

    /**
     * 审批用户
     */
    public function userAction()
    {
        $key  = Input::get('key');
        $keys = AES::decrypt($key, config('app.key'));
        list($table, $id) = explode('.', $keys);

        $number = Input::get('step_number');

        $model = Model::where('table', $table)->first();

        $step = Step::where('model_id', $model->id)
        ->where('number', $number)
        ->first();

        $users = [];

        switch ($step->type) {
            // 指定办理人
            case 'user':
                $users = explode(',', $step->type_value);
                break;

            // 负责人
            case 'owner':
                $users = [$auth->salesman_id];
                break;

            // 指定角色办理人
            case 'role':
                $roles = explode(',', $step->type_value);
                $users = DB::table('user')->whereIn('role_id', $roles)->pluck('id');
                break;

            // 单据创建者
            case 'created_by':
                $users = [$data->created_by];
                break;

            // 直属领导
            case 'leader':
                $users = [$auth->leader_id];
                break;

            // 部门负责人
            case 'manager':
                $users = [$auth->department->manager];
                break;

            // 主表字段值
            case 'field':
                if ($step->type_value == 'supplier_id') {
                    $supplier = DB::table('supplier')->find($data->supplier_id);
                    $users  = [$supplier['user_id']];
                }
                break;

            // 自定义
            case 'custom':
                $users = [];
                break;
        }

        $_users = DB::table('user')->whereIn('id', $users)->pluck('nickname', 'id');

        if ($_users->isEmpty()) {
            return $this->json('无主办人');
        }

        $e = '<select class="form-control input-sm" name="step_user_id" id="process-user-select" data-placeholder="请选择办理人"><option value=""></option>';
        foreach ($_users as $key => $step) {
            $e .= '<option value="'.$key.'">'.$step.'</option>';
        }
        $e .= '</select>';
        return $this->json($e, true);
    }

    /**
     * 审批记录
     */
    public function logAction()
    {
        $key  = Input::get('key');
        $keys = AES::decrypt($key, config('app.key'));
        list($table, $id) = explode('.', $keys);

        $rows = [];

        if ($id) {
            $rows = DB::table('model_step_log')
            ->where('table', $table)
            ->where('table_id', $id)
            ->get();
    
            $steps = DB::table('model_step')
            ->leftJoin('model', 'model.id', '=', 'model_step.model_id')
            ->where('model.table', $table)
            ->get(['model_step.*']);
            $steps = array_by($steps, 'number');
        }

        return $this->render([
            'rows'  => $rows,
            'steps' => $steps,
        ]);
    }

    public function notify($model, $step, $data, $gets)
    {
        $user_id = 0;

        switch ($step->type) {
            // 指定办理人
            case 'user':
                $user_id = $step->type_value;
                break;

            // 单据创建者
            case 'created_by':
                $user_id = $data->created_by;
                break;

            // 直属领导
            case 'leader':
                $user_id = Auth::user()->leader_id;
                break;

            // 部门负责人
            case 'manager':
                $user_id = Auth::user()->department->manager;
                break;

            // 主表字段值
            case 'field':
                // 供应商首选联系人
                if ($step->type_value == 'supplier_id') {
                    $supplier = \Aike\Web\Supplier\Supplier::with('contact')
                    ->where('id', $data['supplier_id'])
                    ->first();
                    $user_id = $supplier->contact->user_id;
                }
                break;

            // 自定义
            case 'custom':
                $user_id = 0;
                break;
        }

        if ($user_id && $gets['notify_sms']) {
            $user = DB::table('user')->find($user_id, ['mobile']);
            if ($user['mobile']) {
                $users[] = $user['mobile'];

                $notify_text = $gets['notify_text'] ? $gets['notify_text'] : '有新的单据等待审批。';
                
                $find    = array('{name}');
                $replace = array($data['name']);

                $notify_text = ' - '.str_replace($find, $replace, $notify_text);

                Notify::sms($users, $model->name, $notify_text);
            }
        }
    }
}
