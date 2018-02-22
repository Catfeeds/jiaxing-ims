<?php namespace Aike\Web\Flow;

use DB;
use Request;
use Input;
use Validator;
use URL;
use Auth;
use AES;
use Hook;
use Module;

use Aike\Web\Flow\Model;
use Aike\Web\Flow\Field;
use Aike\Web\Flow\Permission;
use Aike\Web\Flow\Template;
use Aike\Web\Flow\Step;
use Aike\Web\Flow\StepLog;
use Aike\Web\Index\Attachment;
use Aike\Web\Index\Access;

class Form
{
    // 构建表单
    public function make($table, $options = [])
    {
        if (Request::method() == 'POST') {
            return \Aike\Web\Flow\Form::store();
        }

        $gets = Input::get();

        // 标记提醒已读
        $unread = Input::get('unread');
        if ($unread) {
            DB::table('notify_message')->where('id', $unread)->update(['status'=>1]);
        }

        $quick = $gets['quick'];
        if ($gets['quick']) {
            $row = [];
            $row[$quick] = $gets[$quick];
            $id  = 0;
        } else {
            $id  = (int)Input::get('id');
            $row = DB::table($table)->find($id);
        }

        if ($gets['relation'] && $gets['relation_id']) {
            $row[$gets['relation']] = $gets['relation_id'];
        }

        $auth = Auth::user();

        $assets = Access::getRoleAuthorise($auth->role_id);

        $master = DB::table('flow')
        ->where('table', $table)
        ->first();

        $fields = DB::table('flow_field')
        ->where('model_id', $master['id'])
        ->orderBy('sort', 'asc')
        ->get();

        $fields = array_by($fields, 'field');

        // 操作权限
        $permission = [];
        $step = [];

        // 权限查询类型
        $type_sql = '('.join(' or ', [db_instr('type', 'create'), db_instr('type', 'edit')]).')';

        // 固定流程
        if ($master['is_flow']) {

            // 读取审批记录
            $step_log = DB::table('flow_step_log')
            ->where('model_id', $master['id'])
            ->where('table_id', $row['id'])
            ->where('user_id', $auth['id'])
            ->where('updated_id', 0)
            ->first();

            if ($step_log) {
                $step_sn = $step_log['step_sn'];
            } else {
                $step_sn = 1;
            }

            if ($master['is_flow'] == 1) {
                $step = DB::table('flow_step')->where('model_id', $master['id'])
                ->where('sn', $step_sn)
                ->first();

                $_permission = DB::table('flow_permission')
                ->permission('receive_id')
                ->whereRaw($type_sql)
                ->where('id', $step['permission_id'])
                ->first();
            } else {
                $_permission = DB::table('flow_permission')
                ->permission('receive_id')
                ->whereRaw($type_sql)
                ->where('model_id', $master['id'])
                ->first();
            }
        } else {
            $_permission = DB::table('flow_permission')
            ->permission('receive_id')
            ->whereRaw($type_sql)
            ->where('model_id', $master['id'])
            ->first();
        }
        $permission = json_decode($_permission['data'], true);

        $key = AES::encrypt($master['table'].'.'.(int)$row['id'], config('app.key'));

        $template = DB::table('flow_template')
        ->permission('receive_id')
        ->whereRaw($type_sql)
        ->where('model_id', $master['id'])
        ->first();
        $views = json_decode($template['tpl'], true);

        $_data = Hook::fire($table.'.onBeforeForm', ['model' => $model, 'fields' => $fields, 'views' => $views]);
        extract($_data);

        $col = 0;
        $_replace = [];
        $sublist = [];
        $form_groups = [];

        foreach ($views as $k => $view_group) {
            $tpl = '';

            foreach ($view_group['fields'] as $view) {

                // 是多行子表
                if ($view['type'] == 1) {
                    $sublist[] = $view;
                }

                if ($col == 0) {
                    $tpl .= '<div class="row">';
                }

                $col += $view['col'];
                
                if ($view['type'] == 0) {
                    $tpl .= '<label class="col-sm-2 control-label" for="">{'.$view['name'].'}</label>';
                }

                if ($view['type'] == 1) {
                    $right_col = $view['col'];
                } else {
                    $right_col = $view['col'] - 2;
                }

                $tpl .= '<div class="col-sm-'.$right_col.' control-text">{'.$view['field'].'}</div>';
                
                if ($col == 12) {
                    $col = 0;
                    $tpl .= '</div>';
                }

                if (isset($fields[$view['field']])) {
                    $field = $fields[$view['field']];

                    $attribute = [];

                    $p = $permission[$master['table']][$field['field']];
                    $field['is_read'] = $p['w'] == 1 ? 0 : 1;
                    $field['is_auto'] = $p['m'] == 1 ? 1 : 0;
                    $field['is_hide'] = $p['s'] == 1 ? 1 : $field['is_hide'];

                    // 单据编码规则
                    $field['data_sn_rule'] = $master['data_sn_rule'];
                    $field['data_sn']      = $master['data_sn'];

                    $validate = (array)$p['v'];

                    $required = '';
                    if (in_array('required', $validate)) {
                        $required = '<span class="red">*</span> ';
                        $attribute['required'] = 'required';
                    }

                    $field['verify']    = $validate;
                    $field['attribute'] = $attribute;
                    $field['table']     = $table;

                    $tooltip = $field['tips'] ? ' <a class="hinted" href="javascript:;" title="'.$field['tips'].'"><i class="fa fa-question-circle"></i></a>' : '';

                    $_replace['{'. $field['name'].'}'] = $required.$field['name'].$tooltip;
                    $_replace['{'. $field['field'].'}'] = Field::{'content_'.$field['form_type']}($field, $row[$field['field']]);
                }
            }

            $form_groups[$k] = [
                'tpl'   => $tpl,
                'title' => $view_group['title'],
            ];
        }

        $buttons = '<input type="hidden" name="master[key]" id="master_key" value="'.$key.'">';
        $buttons .= '<input type="hidden" name="master[permission_id]" value="'.$_permission['id'].'">';
        $buttons .= '<input type="hidden" name="master[id]" id="master_id" value="'.$row['id'].'">';
        $buttons .= '<input type="hidden" name="master[table]" id="master_table" value="'.$table.'">';

        // 记住跳转url
        $url = session()->get('referer_'.Request::module().'_'.Request::controller().'_index');
        $referer = $url ? $url : URL::previous();

        if ($quick == '') {
            $buttons .= '<div class="pull-left"><div class="btn-group">
                <button type="button" onclick="history.back();" class="btn btn-sm btn-default"><i class="fa fa-arrow-left"></i> 返回</button>
                <a class="btn btn-sm btn-default" data-toggle="dropdown" aria-expanded="false"><span class="caret"></span></a>
                <ul class="dropdown-menu">
                    <li><a href="'.$referer.'"><i class="fa fa-navicon"></i> 回到列表</a></li>
                </ul>
                </div>';
        }
        
        $buttons .= '<input type="hidden" name="step_referer" value="'.$referer.'">';
        
        $_actions = $options['actions'];

        if ($master['is_flow']) {
            $buttons .= '<input type="hidden" name="step_log_id" value="'.$step_log['id'].'">
            <input type="hidden" name="current_step_id" value="'.$step['id'].'">';
            
            $turn = $master['is_flow'] == 1 ? 'turn' : 'freeturn';

            /*
            if($step_sn == 1) {
                $buttons .= '<a class="btn btn-sm btn-info" href="javascript:;" onclick="flow.'.$turn.'(\''.$table.'\');"><i class="fa fa-check"></i> 保存</a> ';
            } else {
                $buttons .= '<a class="btn btn-sm btn-info" href="javascript:;" onclick="flow.'.$turn.'(\''.$table.'\');"><i class="fa fa-check"></i> 保存</a> ';
            }
            */

            if ($_actions) {
                foreach ($_actions as $action) {
                    if ($action['access'] && $assets[$action['url']]) {
                        $params = [];
                        foreach ($action['query'] as $k => $v) {
                            if (is_numeric($k)) {
                                $params[] = $v;
                            } else {
                                $params[] = $k.'='.$row[$v];
                            }
                        }
                        $query = $params ? '?'.join('&', $params) : '';
                        $url = url($action['url']).$query;
                        $buttons .= '<a class="btn btn-default" href="javascript:;" onclick="quickForm(\''.$action['table'].'\',\''.$action['label'].'\', \''.$url.'\');"> '.$action['label'].' </a> ';
                    }
                }
            }

            $buttons .= '<a class="btn btn-sm btn-info" href="javascript:;" onclick="flow.'.$turn.'(\''.$table.'\');"><i class="fa fa-check"></i> 保存</a> ';
            $buttons .= '<a class="btn btn-sm btn-dark" href="javascript:;" onclick="flow.draft(\''.$table.'\');"><i class="icon icon-coffee-cup"></i> 草稿</a> ';
        } else {
            if ($quick == '') {
                $buttons .= '<a class="btn btn-sm btn-info" href="javascript:;" onclick="flow.store(\''.$table.'\');"><i class="fa fa-check"></i> 保存</a>';
            }
        }

        if ($quick == '') {
            $buttons .= '<div class="btn-group">
            <a class="btn btn-sm btn-default" data-toggle="dropdown" aria-expanded="false">动作 <span class="caret"></span></a>
            <ul class="dropdown-menu">
                <li><a href="javascript:;" onclick="flow.turnlog(\''.$key.'\');"><i class="fa fa-file-text-o"></i> 审核记录</a></li>
            </ul>
            </div> ';
        }

        if ($id > 0) {
            $pageTurns = session()->get('page_'.Request::module().'_'.Request::controller().'_index');
            $$last_page_id = $next_page_id = -1;
            $current_page_index = 1;
            foreach ($pageTurns as $k => $v) {
                if ($v == $id) {
                    $current_page_index = $k + 1;
                    $last_page_id = $pageTurns[$k - 1];
                    $next_page_id = $pageTurns[$k + 1];
                    break;
                }
            }

            $buttons .= '</div> '.$current_page_index.' / '.count($pageTurns).' <div class="btn-group">';
            $buttons .= $last_page_id > 0 ? '<a class="btn btn-sm btn-default" href="'.url('show', ['id' => $last_page_id]).'"><i class="fa fa-chevron-left"></i></a>' : '<a class="btn btn-sm btn-default disabled"><i class="fa fa-chevron-left"></i></a>';
            $buttons .= $next_page_id > 0 ? '<a class="btn btn-sm btn-default" href="'.url('show', ['id' => $next_page_id]).'"><i class="fa fa-chevron-right"></i></a>' : '<a class="btn btn-sm btn-default disabled"><i class="fa fa-chevron-right"></i></a>';
            $buttons .= '</div>';
        }

        $js = '<script>jqgridFormList.'.$master['table'].' = [];';
        $js .= '$(function() {';
        
        // 有子表
        if ($sublist) {
            foreach ($sublist as $_view) {
                $model = DB::table('flow')->where('table', $_view['field'])->first();

                $fields = Field::where('model_id', $model['id'])
                ->orderBy('sort', 'asc')
                ->get();
                $fields = array_by($fields, 'field');

                $editoptions = $counts = $rowCounts = [];

                $columns = [
                    ['name' => "id", 'hidden' => true],
                ];

                $permission_table  = $permission[$model['table']];
                $permission_option = $permission_table['@option'];
                if ($permission_option['w']) {
                    $columns[] = ['name' => 'op', 'label' => '&nbsp;', 'formatter' => 'options', 'width' => 60, 'sortable' => false, 'align' => 'center'];
                }

                // 查询子表数据
                $q = DB::table($model['table'])->where($model['relation'], $id);

                $views = $_view['fields'];

                $_data = Hook::fire($model['table'].'.onBeforeForm', ['q' => $q, 'model' => $model, 'fields' => $fields, 'views' => $views]);
                extract($_data);
                
                foreach ($views as $view) {
                    if (isset($fields[$view['field']])) {
                        $field = $fields[$view['field']];

                        $column = [];

                        // 数据类型格式化
                        switch ($field['type']) {
                            case 'DECIMAL':
                                list($_, $len) = explode(',', $field['length']);
                                $column['formatter'] = 'number';
                                $column['formatoptions'] = [
                                    'decimalSeparator'   => '.',
                                    'thousandsSeparator' => ',',
                                    'decimalPlaces'      => (int)$len,
                                    'defaultValue'       => number_format(0, $len),
                                ];
                                break;
                        }

                        $setting = json_decode($field['setting'], true);

                        if ($setting['align']) {
                            $column['align'] = $setting['align'];
                        }

                        // 合计事件
                        if ($setting['total_count']) {
                            $counts[] = ['field' => $field['field'], 'type' => $setting['total_count']];
                        }

                        // 行计事件
                        if ($setting['row_count']) {
                            $rowCounts[] = ['field' => $field['field'], 'rule' => $setting['row_count']];
                        }

                        $permission_field = $permission_table[$field['field']];
                        $validates = $permission_field['v'];

                        $required = '';

                        if ($validates) {
                            $rules = [];

                            foreach ($validates as $validate) {
                                // 设置验证规则
                                $rules[$validate] = 1;
                            }

                            // 整形规则格式化
                            if ($rules['integer']) {
                                $column['formatter'] = 'integer';
                            }

                            // 如果规则有必填和整形设置大于0
                            if ($rules['required'] && $rules['integer']) {
                                $rules['minValue'] = 1;
                            }
                            $column['rules'] = $rules;
                            $required = isset($rules['required']) ? '<span class="red">*</span> ' : '';
                        }

                        $column['label'] = $required.$field['name'];
                        $column['name']  = $field['field'];
                        
                        if ($field['form_type'] == 'label') {
                            $column['editable'] = false;
                        } else {
                            $column['editable'] = $permission_field['w'] == 1 ? true : false;
                        }

                        // 是否隐藏
                        $column['hidden'] = $permission_field['s'] == 1 ? true : (bool)$field['is_hide'];

                        // 字段宽度
                        if ($setting['width']) {
                            if ($setting['width'] == 'auto') {
                                $column['minWidth'] = 280;
                            } else {
                                $column['width'] = $setting['width'];
                            }
                        }

                        if ($field['form_type'] == 'date') {
                            $editoptions[$field['field']] = [
                                'form_type' => $field['form_type'],
                                'type'      => $setting['type'],
                                'field'     => $field['field'],
                            ];
                        }

                        if ($field['form_type'] == 'option') {
                            $_option = option($setting['value']);
                            foreach ($_option as $k => $v) {
                                $_option[$k]['text'] = $v['name'];
                            }

                            $editCombo[$field['field']] = $_option;
                            $editoptions[$field['field']] = [
                                'form_type' => $field['form_type'],
                                'field'     => $field['field'],
                            ];

                            $column['formatter'] = 'dropdown';
                        }

                        if ($field['form_type'] == 'dataset') {

                            // 映射列表选择的字段
                            $map = [];
                            $_id   = explode(':', $setting['id']);
                            $_name = explode(':', $setting['name']);

                            $map[$_id[0]]   = $_id[1];
                            $map[$_name[0]] = $_name[1];

                            $maps = explode("\n", $setting['map']);
                            foreach ($maps as $_map) {
                                $_map   = explode(':', $_map);
                                $map[trim($_map[0])] = trim($_map[1]);
                            }

                            $dialog = Module::dialogs($setting['type']);

                            $editoptions[$field['field']] = [
                                'form_type' => $field['form_type'],
                                'title'     => $dialog['name'],
                                'type'      => $setting['type'],
                                'field'     => $field['field'],
                                'srcField'  => $_id[0],
                                'mapField'  => $map,
                                'display'   => $setting['display'],
                                'url'       => $dialog['url'],
                            ];
                        }
                        $columns[] = $column;
                    }
                }

                $buttons .= '<input type="hidden" name="models['.$model['table'].'][type]" value="'.$model['type'].'">';
                $buttons .= '<input type="hidden" name="models['.$model['table'].'][relation]" value="'.$model['relation'].'">';
                $buttons .= '<input type="hidden" name="uri" value="'.Request::module().'/'.Request::controller().'">';
                $buttons .= '<input type="hidden" name="_token" value="'.csrf_token().'">';
                
                // 子表权限
                $multiselect = false;

                // 子表查询
                $rows = $q->get();

                $_data = Hook::fire($model['table'].'.onAfterForm', ['rows' => $rows, 'gets' => $gets, 'id' => $id, 'multiselect' => $multiselect]);
                extract($_data);

                $_options = [
                    'autoOption'  => $permission_option['w'],
                    'multiselect' => $multiselect,
                    'editCombo'   => $editCombo,
                    'columns'     => $columns,
                    'editoptions' => $editoptions,
                    'counts'      => $counts,
                    'rowCounts'   => $rowCounts,
                    'data'        => $rows,
                    'title'       => $model['name'],
                ];

                $js .= 'jqgridForm("'.$table.'","'.$model['table'].'", '.json_encode($_options, JSON_UNESCAPED_UNICODE).');';
                
                $_replace['{'.$model['table'].'}'] = '<div id="jqgrid-editor-container"><table id="grid_'.$model['table'].'"></table></div>';
            }
        }

        $js .= '});</script>';

        $_data = Hook::fire($table.'.onAfterForm', ['row' => $row, '_replace' => $_replace]);
        extract($_data);

        $_tpl = '';

        $i = 0;
        $n = count($form_groups);
        foreach ($form_groups as $form_group) {
            $i++;

            $heading = $form_group['title'] == '' ? '' : '<div class="panel-heading b-t"><i class="fa fa-clone"></i> '.$form_group['title'].'</div>';

            $_tpl .= $heading.$form_group['tpl'];
        }

        $master_tpl = strtr($_tpl, $_replace);

        $panel_body .= $quick ? $js.$buttons : '<div class="flow-tool-header bg-light lter b-b">'.$js.$buttons.'</div>';

        if ($quick) {
            $forms = '<form class="form-horizontal" method="post" enctype="multipart/form-data" action="'.url().'" id="'.$table.'_form" name="'.$table.'_form"><div class="panel-form-show quick-form-show">'.$master_tpl.$panel_body.'<div class="first-line"></div></div></form>';
            return $forms;
        } else {
            $forms = '<form class="form-horizontal" method="post" enctype="multipart/form-data" action="'.url().'" id="'.$table.'_form" name="'.$table.'_form"><div class="panel panel-form-show">'.$master_tpl.'<div class="first-line"></div></div>'.$panel_body.'</form>';
            $layout = Auth::user()->client == 'app' ? 'layouts.empty' : 'layouts.default';
            return view($layout, ['content' => $forms, 'flow' => 'flow']);
        }
    }

    // 删除模型数据
    public function remove($table)
    {
        $id = Input::get('id');
        if (empty($id)) {
            return $this->json('最少选择一行记录。');
        }

        $gets = Input::get();

        // 主模型字段
        $master = DB::table('flow')->where('table', $table)->first();

        // 删除前执行
        $_data = Hook::fire($table.'.onBeforeDelete', ['table' => $table, 'gets' => $gets, 'master' => $master]);
        extract($_data);

        // 查询子表
        $models = DB::table('flow')->where('parent_id', $master['id'])->get();
        if ($models->count()) {
            foreach ($models as $model) {
                // 删除子表数据
                DB::table($model['table'])->whereIn($model['relation'], $id)->delete();
            }
        }
        
        // 这里以后加入删除文件选项
        DB::table($table)->whereIn('id', $id)->delete();
        
        // 删除审批记录
        DB::table('flow_step_log')->where('table', $table)->whereIn('table_id', $id)->delete();

        // 删除后执行
        Hook::fire($table.'.onAfterDelete', ['table' => $table, 'gets' => $gets, 'master' => $master]);

        $msg = $master['name'].'删除成功。';
        session()->flash('message', $msg);
        return $this->json($msg, true);
    }
}
