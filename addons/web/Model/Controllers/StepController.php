<?php namespace Aike\Web\Model\Controllers;

use DB;
use Auth;
use Input;
use Request;
use Validator;

use Aike\Web\Model\Step;
use Aike\Web\Model\Model;
use Aike\Web\Model\Field;

use Aike\Web\Index\Controllers\DefaultController;

class StepController extends DefaultController
{
    public $permission = ['condition'];

    /**
     * 步骤列表
     */
    public function indexAction()
    {
        $model_id = Input::get('model_id');

        $model = Model::find($model_id);
        $rows  = Step::where('model_id', $model->id)->orderBy('number', 'asc')->get()->keyBy('number');

        foreach ($rows as $number => $row) {
            $joins = explode(',', $row->join);
            $links = [];
            foreach ($joins as $join) {
                if ($join) {
                    $links[$join] = $rows[$join]->name;
                }
            }
            $rows[$number]['links'] = $links;
        }

        if (Request::method() == 'POST') {
            return response()->json($rows);
        } else {
            return $this->display(array(
                'rows'  => $rows,
                'model' => $model,
            ));
        }
    }

    /**
     * 节点条件
     */
    public function conditionAction()
    {
        $id       = Input::get('id');
        $model_id = Input::get('model_id');

        if (Request::method() == 'POST') {
            $gets = Input::get();
            $step = Step::find($id);
            $step->condition = json_encode($gets['condition'], JSON_UNESCAPED_UNICODE);

            $step->save();
            return $this->success('step/index', ['model_id'=>$model_id], '恭喜你，流程步骤操作成功。');
        }

        $row = Step::findOrNew($id);

        if (empty($row->join)) {
            return $this->error('很抱歉，此进程没有下一步节点。');
        }

        $join = explode(',', $row->join);
        $condition = json_decode($row->condition, true);

        $steps = Step::where('model_id', $model_id)->whereIn('number', $join)->orderBy('number', 'ASC')->get();
        $model = Model::with('fields', 'children.fields')->where('id', $model_id)->first();
        
        $fields = [
            ['name' => '[创建人姓名]', 'field' => '[start_user]', 'auto' => 1],
            ['name' => '[创建人职位]', 'field' => '[start_position]', 'auto' => 1],
            ['name' => '[创建人群组]', 'field' => '[start_group]', 'auto' => 1],
            ['name' => '[创建人岗位]', 'field' => '[start_role]', 'auto' => 1],
            ['name' => '[创建人部门]', 'field' => '[start_department]', 'auto' => 1],
            ['name' => '[经办人姓名]', 'field' => '[edit_user]', 'auto' => 1],
            ['name' => '[经办人职位]', 'field' => '[edit_position]', 'auto' => 1],
            ['name' => '[经办人群组]', 'field' => '[edit_group]', 'auto' => 1],
            ['name' => '[经办人岗位]', 'field' => '[edit_role]', 'auto' => 1],
            ['name' => '[经办人部门]', 'field' => '[edit_department]', 'auto' => 1],
        ];

        $fields = array_merge($fields, $model->fields->toArray());
        $columns[$model->table] = [
            'master' => 1,
            'data'   => $fields
        ];

        foreach ($model->children as $children) {
            $columns[$children->table] = [
                'master' => 0,
                'data'   => $children->fields->toArray(),
            ];
        }
        
        return $this->display([
            'model'     => $model,
            'condition' => $condition,
            'columns'   => $columns,
            'steps'     => $steps,
            'row'       => $row,
        ]);
    }

    /**
     * 创建步骤
     */
    public function createAction()
    {
        $id       = Input::get('id');
        $model_id = Input::get('model_id');

        if (Request::method() == 'POST') {
            $gets = Input::get();

            $gets['join']      = join(',', (array)$gets['join']);
            $gets['condition'] = json_encode($gets['condition']);
            $gets['field']     = json_encode($gets['field']);
            $gets['notify']    = json_encode($gets['notify']);

            $gets['type_value'] = (string)$gets['type_value'][$gets['type']];

            $rules = array(
                'name'   => 'required',
                'number' => 'required|numeric',
            );
            $v = Validator::make($gets, $rules);

            if ($v->fails()) {
                return $this->back()->withErrors($v)->withInput();
            }

            $flow = Step::findOrNew($gets['id']);
            $flow->fill($gets);
            $flow->save();

            return $this->success('index', ['model_id'=>$model_id], '恭喜你，流程步骤操作成功。');
        }

        $row  = Step::findOrNew($id);

        $row->join      = explode(',', $row->join);
        $row->condition = json_decode($row->condition, true);
        $row->field     = json_decode($row->field, true);
        $row->notify    = json_decode($row->notify, true);

        $steps = Step::where('model_id', $model_id)->orderBy('number', 'ASC')->get();

        $model = Model::with('fields', 'children.fields')->where('id', $model_id)->first();
        $columns[$model->table]['master'] = 1;
        $columns[$model->table]['fields'] = $model->fields;

        foreach ($model->children as $children) {
            $columns[$children->table]['master'] = 0;
            $columns[$children->table]['fields'] = $children->fields;
        }

        $colors = [
            'default',
            'primary',
            'success',
            'info',
            'warning',
            'danger',
            'dark',
        ];
        
        $regulars = Model::regular();

        return $this->display(array(
            'model'    => $model,
            'columns'  => $columns,
            'regulars' => $regulars,
            'steps'    => $steps,
            'colors'   => $colors,
            'row'      => $row,
        ));
    }

    /**
     * 删除步骤
     */
    public function deleteAction()
    {
        $id = Input::get('id');
        if ($id > 0) {
            Step::where('id', $id)->delete();
            return $this->back('恭喜你，流程步骤删除成功。');
        }
    }
}
