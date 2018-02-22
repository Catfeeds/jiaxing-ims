<?php namespace Aike\Web\Model\Controllers;

use DB;
use Auth;
use Input;
use Request;
use Validator;

use Aike\Web\Model\Model;
use Aike\Web\Model\Field;

use Aike\Web\Index\Controllers\DefaultController;

class FieldController extends DefaultController
{
    public function indexAction()
    {
        $model_id = Input::get('model_id');

        if (Request::method() == 'POST') {
            $sorts = Input::get('sort');
            foreach ($sorts as $id => $sort) {
                $field = Field::find($id);
                $field->sort = $sort;
                $field->save();
            }
            return $this->success('index', ['model_id'=>$model_id], '恭喜你，操作成功。');
        }

        $rows = Model::with(['fields' => function ($q) {
            $q->orderBy('sort', 'asc')->orderBy('id', 'asc');
        }])->find($model_id);

        //$rows->fields = $rows->fields->sortBy('sort');

        return $this->display([
            'rows'     => $rows,
            'model_id' => $model_id,
        ]);
    }

    public function createAction()
    {
        $model_id = Input::get('model_id');
        $id       = Input::get('id');

        $row      = Field::find($id);
        $model    = Model::find($model_id);

        if (Request::method() == 'POST') {
            $gets = Input::get();

            $rules = [
                'name'      => 'required',
                'field'     => 'required|unique:model_field,field,'.$id.',id,model_id,'.$model_id,
                'form_type' => 'required',
                'type'      => 'required',
            ];

            $v = Validator::make($gets, $rules);
            if ($v->fails()) {
                return $this->back()->withErrors($v)->withInput();
            }

            // 字段管理
            $templates = [
                'BIGINT'    => ['bigint', '10', 'NOT NULL', '0'],
                'INT'       => ['int', '10', 'NOT NULL', '0'],
                'MEDIUMINT' => ['mediumint', '8', 'NOT NULL', '0'],
                'SMALLINT'  => ['smallint', '5', 'NOT NULL', '0'],
                'TINYINT'   => ['tinyint', '3', 'NOT NULL', '0'],
                'DECIMAL'   => ['decimal', '10,2', 'NOT NULL', '0.00'],
                'DATE'      => ['date', '', 'NOT NULL', '0000-00-00'],
                'DATETIME'  => ['datetime', '', 'NOT NULL', '0000-00-00 00:00:00'],
                'CHAR'      => ['char', '50', 'NOT NULL', ''],
                'VARCHAR'   => ['varchar', '255', 'NOT NULL', ''],
                'TEXT'      => ['text', '0', 'NOT NULL', '']
            ];

            $template = $templates[$gets['type']];
            $field    = $template[0];
            $length   = $gets['length'] ? $gets['length'] : $template[1];
            $null     = ' '.$template[2];
            $default  = ' default '."'".$template[3]."'";
            $comment  = ' COMMENT '."'".$gets['name']."'";
            $after    = ' '.$gets['after'];

            $length = $length == '' ? '' : '('.$length.')';

            $table = $model->getAttribute('table');
            $sql = [];

            $columns = DB::select('SHOW COLUMNS FROM '.$table);
            $columns = array_by($columns, 'Field');

            if ($gets['id'] && isset($columns[$row['field']])) {
                // 修改字段
                if ($row['field'] != $gets['field'] || $row['type'] != $gets['type'] || $row['length'] != $gets['length']) {
                    $sql[] = 'ALTER TABLE '.$table.' CHANGE `'.$row['field'].'` `'.$gets['field'].'` '.$field.$length.$null.$default.$comment.$after;
                }

                if ($row['field'] != $gets['field']) {
                    // 删除旧索引
                    if ($row['index']) {
                        $sql[] = 'ALTER TABLE '.$table.' DROP INDEX idx_'.$row['field'];
                    }
                    // 添加新索引
                    if ($gets['index']) {
                        $sql[] = 'ALTER TABLE '.$table.' ADD '.$gets['index'].' idx_'.$gets['field'].' (`'.$gets['field'].'`)';
                    }
                }
            } else {
                // 字段不存在
                if (!isset($columns[$gets['field']])) {
                    // 添加字段
                    $sql[] = 'ALTER TABLE '.$table.' ADD `'.$gets['field'].'` '.$field.$length.$null.$default.$comment.$after;
                    // 添加字段索引
                    if ($gets['index']) {
                        $sql[] = 'ALTER TABLE '.$table.' ADD '.$gets['index'].' idx_'.$gets['field'].' (`'.$gets['field'].'`)';
                    }
                }
            }

            // 操作数据表
            foreach ($sql as $_sql) {
                DB::statement($_sql);
            }
 
            // 写入模型数据
            $gets['setting'] = json_encode($gets['setting']);

            $model = Field::findOrNew($gets['id']);
            $model->fill($gets);
            $model->save();

            return $this->success('index', ['model_id' => $gets['model_id']], '恭喜你，操作成功。');
        }

        return $this->display(array(
            'row'      => $row,
            'model'    => $model,
            'model_id' => $model_id,
        ));
    }

    /**
     * 获取字段类型
     */
    public function typeAction()
    {
        $type = Input::get('type');
        if ($type) {
            $act = 'form_'.$type;
            return Field::$act();
        }
    }

    public function deleteAction()
    {
        $id = Input::get('id');
        $id = is_array($id) ? $id : [$id];

        Field::whereIn('id', $id)->delete();
        
        return $this->success('index', '恭喜你，操作成功。');
    }
}
