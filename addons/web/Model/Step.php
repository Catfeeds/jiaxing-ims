<?php namespace Aike\Web\Model;

use Aike\Web\Index\BaseModel;

use DB;
use Auth;
use Input;

class Step extends BaseModel
{
    protected $table = 'model_step';

    public function model()
    {
        return $this->belongsTo('Aike\Web\Model\Model');
    }

    // 检查转入条件
    public function checkCondition()
    {
        $gets = Input::get();

        // 获取用户职位
        $position = DB::table('user_position')->pluck('title', 'id');

        $start_user = Step::getMacroUser($gets['created_by']);
        $start_user['position_name'] = $position[$start_user['position']];

        // 当前经办工作用户信息
        $current_user = Step::getMacroUser(Auth::id());
        $current_user['position_name'] = $position[$current_user['position']];

        $form_data = [];
        $form_data['[start_user]']       = $start_user['user_name'];
        $form_data['[start_position]']   = $start_user['position_name'];
        $form_data['[start_group]']      = $start_user['group_name'];
        $form_data['[start_role]']       = $start_user['role_name'];
        $form_data['[start_department]'] = $start_user['department_name'];

        $form_data['[edit_user]']       = $current_user['user_name'];
        $form_data['[edit_position]']   = $current_user['position_name'];
        $form_data['[edit_group]']      = $current_user['group_name'];
        $form_data['[edit_role]']       = $current_user['role_name'];
        $form_data['[edit_department]'] = $current_user['department_name'];

        // $form_data['[步骤号]'] = "\$post['setp_id']";
        // $form_data['[流程设计步骤号]'] = $gets['step_number'];

        // 未完成
        //$form_data['[公共附件名称]'] = "\$post['attachment']";
        //$form_data['[公共附件个数]'] = "\$post['attachment']";

        $step = Step::where('model_id', $gets['model_id'])
        ->where('number', $gets['number'])
        ->first();

        if ($step['join']) {
            $join  = explode(',', $step['join']);
            $steps = Step::where('model_id', $gets['model_id'])
            ->whereIn('number', $join)
            ->get()->toArray();
        }

        if (is_array($steps)) {

             // 流程转交条件检查
            $conditions = json_decode($step['condition'], true);

            $step_condition = [];
            $null_condition = 0;

            foreach ($steps as $_step) {
                $_conditions = $conditions[$_step['number']];

                if (empty($_conditions)) {
                    $null_condition ++;
                    continue;
                }
                // 存在条件
                if (sizeof($_conditions)) {
                    $wheres = $values = [];

                    foreach ($_conditions as $condition) {
                        $f = $condition['f'];

                        if (isset($form_data[$f])) {
                            $condition['f'] = "\$form_data['".$f."']";
                        } else {
                            // 分割字段名称
                            list($p, $k) = explode('.', $f);
                            // 把变量名称作为字符串赋值
                            $condition['f'] = "\$gets['".$p."']['".$k."']";
                        }
                        $wheres[] = join(' ', $condition);
                    }

                    // 检查主表条件
                    $where = join(' ', $wheres);
                    $test = eval("return $where;");

                    // 条件满足记录步骤数组
                    if ($test) {
                        $step_condition[] = $_step;
                    }

                    // 子表条件检查是否必要，暂时未实现
                    /*
                    foreach ($childrens as $children) {
                        $gets[$children];
                        foreach ($childrens as $children) {
                        }
                    }
                    */
                }
            }
            // 有一种情况，多个转入步骤都是空条件, 或单转入步骤条件为空
            if (count($steps) == $null_condition) {
                $step_condition = $steps;
            }
        }
        return $step_condition;
    }

    public function getMacroUser($user_id)
    {
        if (empty($user_id)) {
            return null;
        }

        return DB::table('user')
        ->LeftJoin('role', 'role.id', '=', 'user.role_id')
        ->LeftJoin('user_group', 'user_group.id', '=', 'user.group_id')
        ->LeftJoin('department', 'department.id', '=', 'user.department_id')
        ->where('user.id', $user_id)
        ->first([
            'user.post as position',
            'user.nickname as user_name',
            'role.title as role_name',
            'department.title as department_name',
            'user_group.name as group_name'
        ]);
    }

    public function getType($step, $row = [])
    {
        $id   = 0;
        $type = 'user';

        switch ($step->type) {
            // 指定办理人
            case 'user':
                $id = $step->type_value;
                break;

            // 指定角色
            case 'role':
                $role_id = $step->type_value;
                $users = DB::table('user')
                ->where('role_id', $role_id)
                ->get();
                break;

            // 直属领导
            case 'leader':
                $id = Auth::user()->leader_id;
                break;

            // 部门负责人
            case 'manager':
                $id = Auth::user()->department->manager;
                break;

            // 主表字段值
            case 'field':
                $id = $row[$step->type_value];
                if ($step->type_value == 'supplier_id') {
                    $supplier = DB::table('supplier')->find($id);
                    $user_id  = $supplier['user_id'];
                    $type     = 'supplier';
                }
                break;
            
            // 客户负责人
            case 'customer_owner':
                $customer = DB::table('user')->find($row['customer_id']);
                $id  = $customer['salesman_id'];
                break;
            
            // 单据创建者
            case 'created_by':
                $id = $row['created_by'];
                break;

            // 自定义
            case 'custom':
                $id = 0;
                break;
        }
        $res['id']   = $id;
        $res['type'] = $type;
        return $res;
    }
}
