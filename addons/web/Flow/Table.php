<?php namespace Aike\Web\Flow;

use DB;
use Auth;
use Input;
use Request;
use URL;

use Module;
use Hook;
use AES;

use Aike\Web\User\User;
use Aike\Web\Index\Access;
use Aike\Web\Flow\Model;
use Aike\Web\Flow\Field;
use Aike\Web\Flow\Step;
use Aike\Web\Flow\StepLog;
use Aike\Web\Flow\Form;

class Table
{
    public function make($table, $options = [])
    {
        // 排序列表
        if (Request::method() == 'POST') {
            $sorts = Input::get('sort');
            foreach ($sorts as $id => $sort) {
                DB::table($table)->where('id', $id)->update(['sort' => $sort]);
            }
            return redirect()->back()->with('message', '恭喜您，排序成功。');
        }

        $_left_action = $options['left_action'];

        // 当前控制器权限
        $access = Access::getNowRoleAssets();

        $item = Table::columns($table);

        $auth = Auth::user();

        $assets = Access::getRoleAuthorise($auth->role_id);

        $search_query = [
            'referer' => 1,
            'status'  => 1,
            'limit'   => 25,
        ];

        if ($item['is_flow']) {
            $search_query['step_status'] = 'todo';
        }

        $condition = (array)$options['query'];
        $search_query = $search_query + $condition;
        $search = search_form($search_query, $item['search']);

        $query = $search['query'];

        $model = DB::table($table);
        foreach ($item['join'] as $join) {
            $model->leftJoin($join[0], $join[1], $join[2], $join[3]);
        }

        if ($item['is_flow']) {
            /*
            // 待办
            if($query['step_status'] == 'todo') {

                $model->where($table.'.step_sn', '>', 0);

                $log = function($q) use($table, $auth) {
                    $q->selectRaw('1')
                    ->from('flow_step_log')
                    ->whereRaw('table_id = '.$table.'.id')
                    ->where('updated_id', 0)
                    ->where('user_id', $auth['id']);
                };
                $model->whereExists($log);
            }

            // 已办
            if($query['step_status'] == 'trans') {

                $model->where($table.'.step_sn', '>', 0);

                $log = function($q) use($table, $auth) {
                    $q->selectRaw('1')
                    ->from('flow_step_log')
                    ->whereRaw('table_id = '.$table.'.id')
                    ->where('updated_id', 0)
                    ->where('user_id', $auth['id']);
                };
                $model->whereNotExists($log);

                $log = function($q) use($table, $auth) {
                    $q->selectRaw('1')
                    ->from('flow_step_log')
                    ->whereRaw('table_id = '.$table.'.id')
                    ->where('updated_id', '>', 0)
                    ->where('user_id', $auth['id']);
                };
                $model->whereExists($log);
            }

            // 结束
            if($query['step_status'] == 'done') {
                $model->where($table.'.step_sn', 0);
            }
            */
        }

        foreach ($search['where'] as $where) {
            if ($where['active']) {
                $model->search($where);
            }
        }

        // 直接指定条件
        foreach ($condition as $k => $v) {
            $v = str_replace('.', '_', $k);
            if ($query[$v]) {
                $search['params'][$k] = $query[$v];
                $model->where($k, $query[$v]);
            }
        }

        if ($query['order'] && $query['srot']) {
            $model->orderBy($query['srot'], $query['order']);
        } else {

            // 默认排序
            if ($options['orderBy']) {
                foreach ($options['orderBy'] as $k => $v) {
                    $model->orderBy($k, $v);
                }
            } else {
                $model->orderBy($table.'.id', 'desc');
            }
        }

        if ($options['whereIn']) {
            foreach ($options['whereIn'] as $key => $where) {
                $model->whereIn($key, $where);
            }
        }

        $rows = $model->select($item['select'])
        ->paginate($query['limit'])
        ->appends($query);

        $left_action = $right_action = '';

        // 新建
        if (isset($access['create'])) {
            $left_action .= $options['disableCreate'] == true ? '' : '<a href="'.url('create').'" class="btn btn-sm btn-info"><i class="icon icon-plus"></i> 新建</a> ';
        }

        $right_action .= '<div class="btn-group"><a class="btn btn-sm btn-default" data-toggle="dropdown" aria-expanded="false">动作 <span class="caret"></span></a>';
        $right_action .= '<ul class="dropdown-menu">';

        // 排序
        if ($item['is_sort']) {
            $right_action .= $options['disableSort'] == true ? '' : '<li><a href="javascript:optionSort(\'#myform\',\''.url().'\');"><i class="icon icon-sort-by-order"></i> 排序</a></li>';
        }

        // 导出
        if (isset($access['export'])) {
            $right_action .= $options['disableExport'] == true ? '' : '<li><a href="'.url('model/form/export', ['table' => $table]).'"><i class="fa fa-share"></i> 导出全部</a></li>';
        }

        // 删除
        if (isset($access['delete'])) {
            $right_action .= $options['disableDelete'] == true ? '' : '<li><a href="javascript:flow.remove(\''.$table.'\');"><i class="icon icon-remove"></i> 删除</a></li>';
        }

        $right_action .= '</ul></div> ';

        // new \App\Services\Pagination\FlowPresenter($rows)

        $panel .= '<div class="flow-tool-header bg-light lter b-b"><div class="pull-left">'.$left_action.$right_action.'</div>'.$rows->render('vendor/pagination/flow').'</div>';

        $panel .= '<div class="panel m-b-none">';

        /*
        if($item['is_flow']) {
            $panel .= '<div class="panel-heading tabs-box"><ul class="nav nav-tabs">';
            foreach (['todo' => '待办', 'trans' => '已办', 'done' => '结束'] as $k => $v) {
                $panel .= '<li class="'.($k == $query['step_status'] ? 'active': '').'"><a class="text-sm" href="'.url(null, ['step_status' => $k]).'">'.$v.'</a></li>';
            }
            $panel .= '</ul></div>';
        }
        */

        $panel .= '<div>';

        $panel .= '<form id="'.$table.'_search_form" class="search-container form-inline" name="'.$table.'_search_form" action="'.url().'" method="get">';
        $panel .= view('searchForm2', ['search' => $search]);

        $panel .= '<script type="text/javascript">
        $(function() {
            $("#'.$table.'_search_form").searchForm({
                data:'.json_encode($search['forms'], JSON_UNESCAPED_UNICODE).',
                init:function(e) {
                    var self = this;
                }
            });
        });
        </script>
        </form>
        </div>';

        $html = '<form method="post" id="'.$table.'_form" name="'.$table.'_form">
            <div class="table-responsive">
            <table class="table m-b-none table-hover">';

        $html .= '<tr>';

        $html .= '<th align="center"><input class="select-all" type="checkbox"></th>';

        foreach ($item['columns'] as $column) {
            $setting = $column['setting'];
            $align = $setting['align'] ? $setting['align'] : 'left';
            // 点击排序
            if ($column['is_sort']) {
                $name = url_order($search, $column['index'], $column['name']);
            } else {
                $name = $column['name'];
            }
            $html .= '<th align="'.$align.'">'.$name.'</th>';
        }
        $html .= '<th></th></tr>';

        // 模块待办流程
        $_setps = DB::table('flow_step_log')->where('model_id', $item['model_id'])
        ->where('updated_id', 0)
        ->get();

        $setps = [];
        foreach ($_setps as $setp) {
            $setps[$setp['table_id']][$setp['user_id']] = $setp;
            if ($item['is_flow'] == 2) {
                $setp['name'] = $setp['step_name'];
                $item['steps'][$setp['step_sn']] = $setp;
            }
        }

        $dialogs = $item['dialog'];
        foreach ($dialogs as $k => $v) {
            $config = $v['config'];
            $dialogs[$k]['items'] = DB::table($k)->pluck($config['value'], $config['key']);
        }

        // 记录翻页ID
        $pageTurns = [];

        foreach ($rows as $i => $row) {
            $pageTurns[] = $row['id'];

            $html .= '<tr><td align="center"><input type="checkbox" class="select-row" value="'.$row['id'].'" name="id[]"></td>';
            
            foreach ($item['columns'] as $column) {
                $setting = $column['setting'];

                $align = $setting['align'] ? $setting['align'] : 'left';

                $td = $row[$column['field']];

                // 流程处理显示
                if ($column['field'] == 'step_status') {
                    $td = '查看';
                }

                if ($column['field'] == 'step_sn') {
                    if ($item['is_flow']) {
                        if ($row['step_sn'] == 0) {
                            $td = '结束';
                        } else {
                            $step_edit = $setps[$row['id']][$auth['id']];
                            $td = '<span class="'.($step_edit ? 'badge bg-danger' : 'badge').'">'.$item['steps'][$td]['sn'].'</span> '.$item['steps'][$td]['name'];
                        }
                    }
                }

                if ($column['field'] == 'created_at') {
                    $td = format_datetime($td);
                }

                if ($column['field'] == 'created_by') {
                    $td = get_user($td, 'nickname');
                }

                if ($column['field'] == 'sort') {
                    $td = '<input type="text" name="sort['.$row['id'].']" class="form-control input-sort" value="'.$row['sort'].'">';
                }

                if ($column['form_type'] == 'date') {
                    if ($setting['save'] == 'u') {
                        $td = date($setting['type'], $td);
                    }
                }

                if ($column['form_type'] == 'option') {
                    $td = option($setting['value'], $td);
                }

                if ($column['form_type'] == 'dialog') {
                    // 兼容多字段
                    $dialog = $dialogs[$setting['type']];
                    if ($dialog) {
                        $values = explode(',', $row[$dialog[$column['field']]]);
                        $data   = [];
                        foreach ($values as $value) {
                            $data[] = $dialog['items'][$value];
                        }
                        $td = join(',', $data);
                    }
                }

                if ($options[$column['field'].'Formater']) {
                    $_data = $options[$column['field'].'Formater'](['td' => $td, 'column' => $column, 'row' => $row]);
                    extract($_data);
                }

                $html .= '<td align="'.$align.'">'.$td.'</td>';
            }
            $html .= '<td align="center">';

            // 显示动作
            if ($access['show']) {
                $html .= '<a class="option" href="'.url('show', ['id'=>$row['id']]).'"> 查看 </a>';
            }

            // 编辑动作
            if ($access['edit']) {
                if ($item['is_flow']) {

                    // 待办流程
                    if ($row['step_sn'] > 0) {
                        $step_edit = $setps[$row['id']][$auth['id']];
                        if ($step_edit) {
                            $html .= '<a class="option red" href="'.url('edit', ['id'=>$row['id']]).'">审批</a>';
                        }
                    }
                } else {
                    $html .= '<a class="option" href="'.url('edit', ['id'=>$row['id']]).'"> 编辑 </a>';
                }
            }

            $_data = Hook::fire($table.'.onListButton', ['row' => $row, 'assets' => $assets, 'html' => $html]);
            extract($_data);

            $html .= '</td></tr>';
        }
        $html .= '</table></div>';

        // 记录翻页ID
        session()->put('page_'.Request::module().'_'.Request::controller().'_index', $pageTurns);

        $html .= '<input type="hidden" name="uri" value="'.Request::module().'/'.Request::controller().'">';

        $html .= '</form></div>';

        $panel .= $html;

        $layout = Auth::user()->client == 'app' ? 'layouts.empty' : 'layouts.default';

        return view($layout, ['content' => $panel, 'flow' => 'flow']);
    }

    public function makeShowSubview($master, $view, $options, $id, $type = 'show')
    {
        // 子表
        //$tabs = '<div class="panel m-b-none"><ul id="myTab" class="nav nav-tabs b-t p-t">';
        //$tabContent = '<div id="'.$master['table'].'TabContent" class="tab-content">';

        $tabContent = '';

        $model = DB::table('flow')->where('table', $view['field'])->first();

        if ($i == 0) {
            $tabs .= '<li class="active m-l"><a href="#'.$model['table'].'" data-toggle="tab">'.$model['name'].'</a></li>';
        // $tabContent .= '<div class="tab-pane fade in active" id="'.$model['table'].'">';
        } else {
            $tabs .= '<li><a href="#'.$model['table'].'" data-toggle="tab">'.$model['name'].'</a></li>';
            // $tabContent .= '<div class="tab-pane fade" id="'.$model['table'].'">';
        }
        
        if ($type == 'show') {
            $tabContent .= '<div class="table-responsive"><table class="table table-hover m-b-none table-bordered"><tbody>';
        } else {
            $tabContent .= '<div><table class="table m-b-none"><tbody>';
        }

        $fields = DB::table('flow_field')->where('model_id', $model['id'])->orderBy('sort', 'asc')->get();
        $fields = array_by($fields, 'field');

        // 查询子表数据
        $q = DB::table($model['table'])->where($model['relation'], $id);

        $views = $view['fields'];

        $_data = Hook::fire($model['table'].'.onBeforeShow', ['row' => $row, 'q' => $q, 'gets' => $gets, 'id' => $id, 'views' => $views, 'fields' => $fields]);
        extract($_data);

        $tabContent .= '<tr><th align="center" style="width:80px;">序号</th>';
        foreach ($views as $view) {
            $field = $fields[$view['field']];
            $setting = isset($field['setting']) ? json_decode($field['setting'], true) : $field;
            $align   = $setting['align'] ? $setting['align'] : 'left';
            $tabContent .= '<th align="'.$align.'">'.$view['name'].'</th>';
        }
        $tabContent .= '</tr>';

        // 查询数据
        $rows = $q->get();

        $_data = Hook::fire($model['table'].'.onAfterShow', ['row' => $row, 'rows' => $rows, 'gets' => $gets, 'id' => $id, 'views' => $views, 'fields' => $fields]);
        extract($_data);

        foreach ($rows as $i => $row) {
            $tabContent .= '<tr><td align="center">'.($i + 1).'</td>';

            foreach ($views as $view) {
                $field = $fields[$view['field']];

                $field['is_show'] = 1;
                $field['is_tab']  = 1;

                $setting = isset($field['setting']) ? json_decode($field['setting'], true) : $field;
                $align   = $setting['align'] ? $setting['align'] : 'left';
                $tabContent .= '<td align="'.$align.'">'.Field::{'content_'.$field['form_type']}($field, $row[$field['field']]).'</td>';
            }
            $tabContent .= '</tr>';
        }
        $tabContent .= '</tbody></table></div>';
        
        return $tabContent;
    }

    public function show($table, $options = [])
    {
        $id  = (int)Input::get('id');
        $row = DB::table($table)->find($id);

        $auth = Auth::user();

        // 标记提醒已读
        $unread = Input::get('unread');
        if ($unread) {
            DB::table('notify_message')->where('id', $unread)->update(['status'=>1]);
        }

        // 当前控制器权限
        $access = Access::getNowRoleAssets();

        $master = DB::table('flow')->where('table', $table)
        ->first();

        $fields = DB::table('flow_field')->where('model_id', $master['id'])
        ->orderBy('sort', 'asc')->get();
        $fields = array_by($fields, 'field');

        $log = DB::table('flow_step_log')->where('model_id', $master['id'])
        ->where('table_id', $row['id'])
        ->where('step_sn', $row['step_sn'])
        ->where('user_id', auth()->id())
        ->where('updated_id', 0)
        ->first();

        // 权限查询类型
        $type_sql = '('.join(' or ', [db_instr('type', 'show')]).')';
        $template = DB::table('flow_template')
        ->permission('receive_id')
        ->whereRaw($type_sql)
        ->where('model_id', $master['id'])
        ->first();
        $views = json_decode($template['tpl'], true);

        $_replace = [];

        $col = 0;

        $sublist = [];
        $form_groups = [];

        foreach ($views as $k => $view_group) {
            $tpl = '';

            foreach ($view_group['fields'] as $view) {
                $field = $fields[$view['field']];

                if ($col == 0) {
                    $tpl .= '<div class="row">';
                }

                $col += $view['col'];
            
                if ($view['type'] == 0) {
                    $tpl .= '<div class="col-sm-2 control-label">{'.$view['name'].'}</div>';
                }

                if ($view['type'] == 1) {
                    $right_col = $view['col'];
                } else {
                    $right_col = $view['col'] - 2;
                }

                if ($view['type'] == 1) {
                    $tpl .= '<div class="col-sm-'.$right_col.' control-label control-table">{'.$view['field'].'}</div>';
                } else {
                    $tpl .= '<div class="col-sm-'.$right_col.' control-text">{'.$view['field'].'}</div>';
                }
            
                if ($col == 12) {
                    $col = 0;
                    $tpl .= '</div>';
                }

                $attribute = [];

                $field['table']   = $table;
                $field['is_show'] = 1;
                
                if ($view['type'] == 1) {
                    $_replace['{'. $view['field'].'}'] = self::makeShowSubview($master, $view, $options, $id, 'show');
                } else {
                    $_replace['{'. $field['name'].'}']  = $required.$field['name'];
                    $_replace['{'. $field['field'].'}'] = Field::{'content_'.$field['form_type']}($field, $row[$field['field']]);
                }
            }

            $form_groups[$k] = [
                'tpl'   => $tpl,
                'title' => $view_group['title'],
            ];
        }

        // 记住跳转url
        $url = session()->put('referer_'.Request::module().'_'.Request::controller().'_index');
        $referer = $url ? $url : URL::previous();

        $buttons = '<div class="pull-left"><div class="btn-group">
        <button type="button" onclick="history.back();" class="btn btn-sm btn-default"><i class="fa fa-arrow-left"></i> 返回</button>
        <a class="btn btn-sm btn-default" data-toggle="dropdown" aria-expanded="false"><span class="caret"></span></a>
        <ul class="dropdown-menu">
            <li><a href="'.$referer.'"><i class="fa fa-navicon"></i> 回到列表</a></li>
        </ul>
        </div> ';

        if ($log['id'] > 0 && $access['edit']) {
            $buttons .= '<a class="btn btn-sm btn-info" href="'.url('edit', ['id' => $id]).'"><i class="fa fa-pencil"></i> 编辑</a> ';
        }

        $_data = Hook::fire($table.'.onAfterShow', ['buttons' => $buttons, 'row' => $row, '_replace' => $_replace, 'form_groups' => $form_groups, 'view_type' => 'show']);
        extract($_data);

        $buttons .= '<div class="btn-group">
            <a class="btn btn-sm btn-default" data-toggle="dropdown" aria-expanded="false">动作 <span class="caret"></span></a>
            <ul class="dropdown-menu">';
                
        if ($master['is_flow']) {
            $step['key'] = AES::encrypt($master['table'].'.'.$id, config('app.key'));
            $buttons .= '<li><a href="javascript:;" onclick="flow.turnlog(\''.$step['key'].'\');"><i class="fa fa-file-text-o"></i> 审核记录</a></li>';
        }

        if ($access['print']) {
            $buttons .= '<li><a target="_blank" href="'.url('print', ['table' => $table, 'id' => $id]).'"><i class="fa fa-print"></i> 打印</a></li>';
        }

        $buttons .= '</ul></div> ';

        $pageTurns = session()->get('page_'.Request::module().'_'.Request::controller().'_index');
        $last_page_id = $next_page_id = -1;
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

        $_tpl = '';

        $i = 0;
        $n = count($form_groups);
        foreach ($form_groups as $form_group) {
            $i++;

            $heading = $form_group['title'] == '' ? '' : '<div class="panel-heading b-t text-muted"><i class="fa fa-clone"></i> '.$form_group['title'].'</div>';

            $_tpl .= ''.$heading.''.$form_group['tpl'].'';
        }

        $master_tpl = strtr($_tpl, $_replace);

        $panel_body .= '<div class="flow-tool-header bg-light lter b-b">'.$buttons.'</div>';

        $forms = '<div class="panel panel-form-show">'.$master_tpl.'<div class="first-line"></div></div>'.$panel_body;
        
        $layout = Auth::user()->client == 'app' ? 'layouts.empty' : 'layouts.default';

        
        return view($layout, ['content' => $forms, 'flow' => 'flow']);
    }

    public function export($table, $options = [])
    {
        // 当前控制器权限
        $access = Access::getNowRoleAssets();

        $item = Table::columns($table);

        $auth = Auth::user();

        $assets = Access::getRoleAuthorise($auth->role_id);

        $condition = (array)$options['query'];

        $search = search_form(array_merge([
            'referer' => 1,
            'status'  => 1,
            'limit'   => 25,
        ], $condition), $item['search']);

        $query = $search['query'];

        $model = DB::table($table);
        foreach ($item['join'] as $join) {
            $model->leftJoin($join[0], $join[1], $join[2], $join[3]);
        }

        foreach ($search['where'] as $where) {
            if ($where['active']) {
                $model->search($where);
            }
        }

        // 直接指定条件
        foreach ($condition as $k => $v) {
            $v = str_replace('.', '_', $k);
            if ($query[$v]) {
                $search['params'][$k] = $query[$v];
                $model->where($k, $query[$v]);
            }
        }

        if ($query['order'] && $query['srot']) {
            $model->orderBy($query['srot'], $query['order']);
        } else {
            $model->orderBy($table.'.id', 'desc');
        }

        if ($options['whereIn']) {
            foreach ($options['whereIn'] as $key => $where) {
                $model->whereIn($key, $where);
            }
        }

        $rows = $model->select($item['select'])
        ->get();

        $thead = [];
        
        foreach ($item['columns'] as $column) {
            $setting = $column['setting'];
            $align = $setting['align'] ? $setting['align'] : 'left';
            $thead[] = ['label' => $column['name'], 'name' => $column['field']];
        }

        $_setps = DB::table('flow_step_log')
        ->where('flow_step_log.model_id', $item['model_id'])
        ->where('flow_step_log.updated_id', 0)
        ->get();

        $setps = [];
        foreach ($_setps as $setp) {
            $setps[$setp['table_id']][$setp['user_id']] = $setp;
        }

        $dialogs = $item['dialog'];
        foreach ($dialogs as $k => $v) {
            $config = $v['config'];
            $dialogs[$k]['items'] = DB::table($k)->pluck($config['value'], $config['key']);
        }

        $tbody = [];

        foreach ($rows as $i => $row) {
            $tr = [$row['id']];

            foreach ($item['columns'] as $column) {
                $setting = $column['setting'];

                $align = $setting['align'] ? $setting['align'] : 'left';

                $td = $row[$column['field']];

                if ($column['field'] == 'step_sn') {
                    $td = $item['steps'][$td]['name'];
                }

                if ($column['field'] == 'created_at') {
                    $td = format_datetime($td);
                }

                if ($column['field'] == 'created_by') {
                    $td = get_user($td, 'nickname');
                }

                if ($column['form_type'] == 'option') {
                    $td = option($setting['value'], $td);
                }

                if ($column['form_type'] == 'dialog') {
                    // 兼容多字段
                    $dialog = $dialogs[$setting['type']];
                    if ($dialog) {
                        $values = explode(',', $row[$dialog[$column['field']]]);
                        $data   = [];
                        foreach ($values as $value) {
                            $data[] = $dialog['items'][$value];
                        }
                        $td = join(',', $data);
                    }
                }
                $tr[$column['field']] = $td;
            }
            $tbody[] = $tr;
        }

        return writeExcel($thead, $tbody, $table.'-'.date('ymd'));
    }

    public function print($table, $options = [])
    {
        $id  = (int)Input::get('id');
        $row = DB::table($table)->find($id);

        $auth = Auth::user();

        // 标记提醒已读
        $unread = Input::get('unread');
        if ($unread) {
            DB::table('notify_message')->where('id', $unread)->update(['status'=>1]);
        }

        // 当前控制器权限
        $access = Access::getNowRoleAssets();

        $master = DB::table('flow')->where('table', $table)
        ->first();

        $fields = DB::table('flow_field')->where('model_id', $master['id'])
        ->orderBy('sort', 'asc')->get();
        $fields = array_by($fields, 'field');

        $log = DB::table('flow_step_log')->where('model_id', $master['id'])
        ->where('table_id', $row['id'])
        ->where('step_sn', $row['step_sn'])
        ->where('user_id', auth()->id())
        ->where('updated_id', 0)
        ->first();

        // 权限查询类型
        $type_sql = '('.join(' or ', [db_instr('type', 'show')]).')';
        $template = DB::table('flow_template')
        ->permission('receive_id')
        ->whereRaw($type_sql)
        ->where('model_id', $master['id'])
        ->first();
        $views = json_decode($template['tpl'], true);

        $_replace = [];

        $col = 0;

        $sublist = [];
        $form_groups = [];

        foreach ($views as $k => $view_group) {
            $tpl = '';

            foreach ($view_group['fields'] as $view) {
                $field = $fields[$view['field']];

                if ($col == 0) {
                    $tpl .= '<div class="row">';
                }

                $col += $view['col'];
            
                if ($view['type'] == 0) {
                    $tpl .= '<div class="col-sm-2 control-label">{'.$view['name'].'}：</div>';
                }

                if ($view['type'] == 1) {
                    $right_col = $view['col'];
                } else {
                    $right_col = $view['col'] - 2;
                }

                if ($view['type'] == 1) {
                    $tpl .= '<div class="col-sm-'.$right_col.' control-label">{'.$view['field'].'}</div>';
                } else {
                    $tpl .= '<div class="col-sm-'.$right_col.' control-text">{'.$view['field'].'}</div>';
                }
            
                if ($col == 12) {
                    $col = 0;
                    $tpl .= '</div>';
                }

                $attribute = [];

                $field['table']   = $table;
                $field['is_show'] = 1;
                
                if ($view['type'] == 1) {
                    $_replace['{'. $view['field'].'}'] = self::makeShowSubview($master, $view, $options, $id, 'print');
                } else {
                    $_replace['{'. $field['name'].'}']  = $required.$field['name'];
                    $_replace['{'. $field['field'].'}'] = Field::{'content_'.$field['form_type']}($field, $row[$field['field']]);
                }
            }

            $form_groups[$k] = [
                'tpl'   => $tpl,
                'title' => $view_group['title'],
            ];
        }

        $buttons = '';
        $buttons .= '<button type="button" onclick="history.back();" class="btn btn-sm btn-default"><i class="fa fa-reply"></i> 返回</button> ';

        if ($log['id'] > 0 && $access['edit']) {
            $buttons .= '<a class="btn btn-sm btn-danger" href="'.url('edit', ['id' => $id]).'"><i class="fa fa-check"></i> 审批</a> ';
        }

        if ($master['is_flow']) {
            $step['key'] = AES::encrypt($master['table'].'.'.$id, config('app.key'));
            $buttons .= '<a class="btn btn-sm btn-default" href="javascript:;" onclick="flow.turnlog(\''.$step['key'].'\');"><i class="fa fa-file-text-o"></i> 审核记录</a> ';
            if ($access['print']) {
                $buttons .= '<a class="btn btn-sm btn-default" target="_blank" href="'.url('print', ['table' => $table, 'id' => $id]).'"><i class="fa fa-print"></i> 打印</a> ';
            }
        } else {
            if ($access['print']) {
                $buttons .= '<a class="btn btn-sm btn-default" target="_blank" href="'.url('print', ['table' => $table, 'id' => $id]).'"><i class="fa fa-print"></i> 打印</a> ';
            }
        }

        $_data = Hook::fire($table.'.onAfterPrint', ['buttons' => $buttons, 'row' => $row, '_replace' => $_replace, 'form_groups' => $form_groups, 'view_type' => 'print']);
        extract($_data);

        $buttons .= '<div class="btn-group"><a class="btn btn-sm btn-default" href="javascript:;"><i class="fa fa-chevron-left"></i></a> ';
        $buttons .= '<a class="btn btn-sm btn-default" href="javascript:;"><i class="fa fa-chevron-right"></i></a></div> ';

        $_tpl = '';

        $i = 0;
        $n = count($form_groups);
        foreach ($form_groups as $form_group) {
            $i++;

            $heading = $form_group['title'] == '' ? '' : '<div class="panel-heading b-t text-muted"><i class="fa fa-clone"></i> '.$form_group['title'].'</div>';

            $_tpl .= ''.$heading.''.$form_group['tpl'].'';
        }

        $master_tpl = strtr($_tpl, $_replace);

        $forms = '<div class="panel-form-show">'.$master_tpl.'</div>'.$panel_body;
        
        return view('layouts.print', ['content' => $forms, 'flow' => 'flow']);
    }

    public function columns($table)
    {
        $model  = Model::where('table', $table)->first();
        $fields = Field::where('model_id', $model['id'])->orderBy('sort', 'asc')->get();

        $res = $join = $select = $search = $steps = [];

        $select = [$table.'.id', $table.'.created_by', $table.'.created_at'];

        $dialog = [];

        foreach ($fields as $field) {
            if ($field['is_index'] == 1) {
                $setting = json_decode($field['setting'], true);

                $column = $index = '';

                if ($field['form_type'] == 'dialog') {
                    $_field = $field['field'];

                    $_dialog = Module::dialogs($setting['type']);

                    $select[] = $table.'.'.$_field;

                    list($_id, $__id) = explode(':', $setting['id']);
                    list($_name, $__name) = explode(':', $setting['name']);

                    if ($__name == '') {
                        $__name = 'name';
                    }
                    
                    // 供应商
                    if ($_dialog['table'] == 'supplier') {
                        $join[]   = ['supplier as supplier_'.$_field, 'supplier_'.$_field.'.id', '=', $table.'.'.$_field];
                        $join[]   = ['user as user_'.$_field, 'user_'.$_field.'.id', '=', 'supplier_'.$_field.'.user_id'];
                        $select[] = 'user_'.$_field.'.nickname as '.$_field.'_name';
                        $index    = 'user_'.$_field.'.nickname';
                        $column   = $_field.'_name';
                        $dialog['user']['config'] = ['key' => 'id', 'value' => 'nickname'];
                        $dialog['user'][$column]  = $_field;
                    }

                    // 业务订单
                    if ($setting['type'] == 'project_order') {
                        $join[]   = ['project_order as project_order_'.$_field, 'project_order_'.$_field.'.id', '=', $table.'.'.$_field];
                        $select[] = 'project_order_'.$_field.'.'.$__name.' as '.$_field.'_'.$__name;
                        $index    = 'project_order_'.$_field.'.'.$__name;
                        $column   = $_field.'_'.$__name;
                        $dialog['project_order']['config'] = ['key' => 'id', 'value' => $__name];
                        $dialog['project_order'][$column]  = $_field;
                    }

                    // 退货订单
                    if ($setting['type'] == 'project_return') {
                        $join[]   = ['project_return as project_return_'.$_field, 'project_return_'.$_field.'.id', '=', $table.'.'.$_field];
                        $select[] = 'project_return_'.$_field.'.'.$__name.' as '.$_field.'_'.$__name;
                        $index    = 'project_return_'.$_field.'.'.$__name;
                        $column   = $_field.'_'.$__name;
                        $dialog['project_return']['config'] = ['key' => 'id', 'value' => $__name];
                        $dialog['project_return'][$column]  = $_field;
                    }

                    // 用户
                    if ($setting['type'] == 'user') {
                        $join[]   = ['user as user_'.$_field, 'user_'.$_field.'.id', '=', $table.'.'.$_field];
                        $select[] = 'user_'.$_field.'.nickname as '.$_field.'_name';
                        $index    = 'user_'.$_field.'.nickname';
                        $column   = $_field.'_name';
                        $dialog['user']['config'] = ['key' => 'id', 'value' => 'nickname'];
                        $dialog['user'][$column]  = $_field;
                    }

                    // 项目
                    if ($setting['type'] == 'project') {
                        $join[]   = ['project as project_'.$_field, 'project_'.$_field.'.id', '=', $table.'.'.$_field];
                        $select[] = 'project_'.$_field.'.name as '.$_field.'_name';
                        $index    = 'project_'.$_field.'.name';
                        $column   = $_field.'_name';
                        $dialog['project']['config'] = ['key' => 'id', 'value' => 'name'];
                        $dialog['project'][$column]  = $_field;
                    }

                    // 区域
                    if ($setting['type'] == 'circle') {
                        $join[]   = ['customer_circle as circle_'.$_field, 'circle_'.$_field.'.id', '=', $table.'.'.$_field];
                        $select[] = 'circle_'.$_field.'.name as '.$_field.'_name';
                        $index    = 'circle_'.$_field.'.name';
                        $column   = $_field.'_name';
                        $dialog['customer_circle']['config'] = ['key' => 'id', 'value' => 'name'];
                        $dialog['customer_circle'][$column]  = $_field;
                    }
                    
                    // 客户
                    if ($setting['type'] == 'customer') {
                        $join[]   = ['customer as customer_'.$_field, 'customer_'.$_field.'.id', '=', $table.'.'.$_field];
                        $join[]   = ['user as user_'.$_field, 'user_'.$_field.'.id', '=', 'customer_'.$_field.'.user_id'];
                        $select[] = 'user_'.$_field.'.nickname as '.$_field.'_name';
                        $index    = 'user_'.$_field.'.nickname';
                        $column   = $_field.'_name';
                        $dialog['customer']['config'] = ['key' => 'id', 'value' => 'nickname'];
                        $dialog['customer'][$column]  = $_field;
                    }
                } else {
                    $column = $field['field'];
                    $index    = $table.'.'.$field['field'];
                    $select[] = $table.'.'.$field['field'];
                }

                $field['field'] = $column;
                $field['index'] = $index;

                $field['setting'] = $setting;

                // 搜索字段
                if ($field['is_search'] == 1) {
                    $form_type = $field['form_type'];

                    if ($field['form_type'] == 'dialog') {
                        $form_type = 'text';
                    }
                    if ($field['form_type'] == 'option') {
                        $form_type = $setting['value'];
                    }
                    if ($field['form_type'] == 'auto' || $field['form_type'] == 'sn') {
                        $form_type = 'text';
                    }
                    if ($column == 'step_sn') {
                        $form_type = 'flow_step.'.$table;
                    }
                    $search[] = [$form_type, $field['index'], $field['name']];
                }

                $res['columns'][$field['field']] = $field;
            }
        }

        // 有流程
        if ($model['is_flow']) {
            $steps = Step::where('model_id', $model['id'])->orderBy('sn', 'asc')->get();
            $steps = array_by($steps, 'sn');
            $select[] = $table.'.step_status';
        }

        $res['join']     = $join;
        $res['dialog']   = $dialog;
        $res['select']   = array_unique($select);
        $res['search']   = $search;
        $res['steps']    = $steps;
        $res['model_id'] = $model['id'];
        $res['is_sort']  = $model['is_sort'];
        $res['is_flow']  = $model['is_flow'];
        return $res;
    }
}
