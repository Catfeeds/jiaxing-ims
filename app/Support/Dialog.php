<?php

class Dialog
{
    public static $items = [
        'user' => [
            'title' => '用户',
            'table' => 'user',
            'field' => 'name',
            'url'   => 'user/user/dialog',
        ],
        'role' => [
            'title' => '角色',
            'table' => 'role',
            'field' => 'name',
            'url'   => 'user/role/dialog',
        ],
        'department' => [
            'title' => '部门',
            'table' => 'department',
            'field' => 'name',
            'url'   => 'user/department/dialog',
        ],
        'supplier' => [
            'title' => '供应商',
            'table' => 'supplier',
            'join'  => 'user',
            'field' => 'user.name',
            'url'   => 'stock/supplier/dialog',
        ],
        'product' => [
            'title' => '产品',
            'table' => 'product',
            'field' => 'name',
            'url'   => 'stock/product/dialog',
        ],
        'product_category' => [
            'title' => '产品类别',
            'table' => 'product_category',
            'field' => 'name',
            'url'   => 'stock/product-category/dialog',
        ],
        'store' => [
            'title' => '门店',
            'table' => 'store',
            'field' => 'name',
            'url'   => 'setting/store/dialog',
        ],
    ];

    public function text($item, $value)
    {
        $dialog = self::$items[$item];

        $rows = '';
        
        if ($value) {
            $ids = explode(',', $value);

            $table = $dialog['table'];
            $join = $dialog['join'];

            if ($join) {
                $rows = DB::table($table)
                ->LeftJoin('user', 'user.id', '=', $table.'.user_id')
                ->whereIn($table.'.id', $ids)
                ->pluck($dialog['field'])->implode(',');
            } else {
                $rows = DB::table($table)
                ->whereIn('id', $ids)
                ->pluck($dialog['field'])->implode(',');
            }
        }
        return $rows;
    }

    public function show($key, $data, $multi = 0, $readonly = 0)
    {
        $id   = $key.'_id';
        $name = $key.'_name';

        if ($readonly == 0) {
            $html[] = '<div class="select-group input-group">';
        } else {
            $html[] = '<div class="select-group">';
        }
        
        if ($readonly == 0) {
            $arrow  = $multi == 1 ? 'icon-group' : 'icon-user';
            $option = "dialogShow('$id','$name','$multi');";
            $clear  = "dialogClear('$id','$name');";

            $html[] = '<div class="form-control input-sm" onclick="'.$option .'" id="'.$name.'">'.$data[$name].'</div>';

            $html[] = '<div class="input-group-btn">';
            $html[] = '<button type="button" onclick="'.$option .'" class="btn btn-sm btn-default"><i class="icon '.$arrow.'"></i></button>';
            $html[] = '<button type="button" onclick="'.$clear .'" class="btn btn-sm btn-default"><i class="icon icon-trash"></i></button>';
            $html[] = '</div>';
        } else {
            $html[] = '<div class="form-control input-sm" id="'.$name.'">'.$data[$name].'</div>';
        }
        $html[] = '<input type="hidden" id="'.$id.'" name="'.$id.'" value="'.$data[$id].'">';
        $html[] = '</div>';
        return join("\n", $html);
    }

    public function select2($item, $name, $value = '', $multi = 0, $readonly = 0, $width = 153)
    {
        $rows = [];

        $dialog = self::$items[$item];

        $ids = [];

        if ($value) {
            $ids = explode(',', $value);

            $table = $dialog['table'];
            $join = $dialog['join'];

            if ($join) {
                $rows = DB::table($table)
                ->LeftJoin('user', 'user.id', '=', $table.'.user_id')
                ->whereIn($table.'.id', $ids)
                ->pluck($dialog['field'], $table.'.user_id');
            } else {
                $rows = DB::table($table)
                ->whereIn('id', $ids)
                ->pluck($dialog['field'], $table.'.id');
            }
        }

        $id = str_replace(['[',']'], ['_',''], $name);

        $options = [];
        foreach ($rows as $k => $v) {
            if (in_array($k, $ids)) {
                $options[] = '<option '.$selected.' value="'.$k.'">'.$v.'</option>';
            }
        }

        $arrow  = $multi == 0 ? 'fa-caret-down' : 'fa-caret-down';
        $option = "dialogUser('$dialog[title]','$dialog[url]','$id','$multi');";
        $clear  = "selectClear('$id');";
        $disabled = $readonly == 0 ? '' : 'disabled="disabled"';

        $html[] = '<select '.$disabled.' class="form-control input-sm" id="'.$id.'">'.join('', $options).'</select>';

        $select2['options'] = [
            'width' => $width.'px',
            'multiple' => $multi,
            'ajax' => [
                'url' => url($dialog['url']),
            ],
        ];
        $html[] = '<script type="text/javascript">select2List.'.$name.'='.json_encode($select2).';</script>';

        return join("\n", $html);
    }

    public function user($item, $name, $value = '', $multi = 0, $readonly = 0, $width = 'auto')
    {
        $rows = '';

        $dialog = self::$items[$item];

        if ($value) {
            $ids = explode(',', $value);

            $table = $dialog['table'];
            $join = $dialog['join'];

            if ($join) {
                $rows = DB::table($table)
                ->LeftJoin('user', 'user.id', '=', $table.'.user_id')
                ->whereIn($table.'.id', $ids)
                ->pluck($dialog['field'])->implode(',');
            } else {
                $rows = DB::table($table)
                ->whereIn('id', $ids)
                ->pluck($dialog['field'])->implode(',');
            }
        }

        $id = str_replace(['[',']'], ['_',''], $name);

        if ($readonly == 0) {
            $html[] = '<div class="select-group input-group">';
        } else {
            $html[] = '<div class="select-group">';
        }

        if ($readonly == 0) {
            $arrow  = $multi == 0 ? 'fa-caret-down' : 'fa-caret-down';
            $option = "dialogUser('$dialog[title]','$dialog[url]','$id','$multi');";
            $clear  = "selectClear('$id');";

            $width  = is_numeric($width) ? 'width:'.$width.'px;' : '';

            $html[] = '<div class="form-control input-sm" onclick="'.$option .'" style="'.$width.';cursor:pointer;" id="'.$id.'_text">'.$rows.'</div>';
            $html[] = '<div class="input-group-btn">';
            $html[] = '<button type="button" onclick="'.$option .'" class="btn btn-sm btn-default"><i class="fa '.$arrow.'"></i></button>';
            $html[] = '</div>';
            $html[] = '<input type="hidden" id="'.$id.'" name="'.$name.'" value="'.$value.'">';
        } else {
            $html[] = '<div class="form-control input-sm" id="'.$id.'_text">'.$rows.'</div><input type="hidden" id="'.$id.'" name="'.$name.'" value="'.$value.'">';
        }
        return join("\n", $html);
    }

    public function search($data, $query)
    {
        $params = [];
        parse_str($query, $params);

        $defaultParams = [
            'prefix'   => 1,
            'multi'    => 0,
            'readonly' => 0
        ];

        $params = array_merge($defaultParams, $params);
        extract($params);
        
        $_id = str_replace(['[',']'], ['_',''], $id);
        $_name = str_replace(['[',']'], ['_',''], $name);

        $jq = '';
        foreach ($params as $key => $value) {
            $jq .= ' data-'.$key.'="'. $value.'"';
        }

        $params['id']   = str_replace(['[',']'], ['_',''], $id);
        $params['name'] = str_replace(['[',']'], ['_',''], $name);

        $e[] = '<div class="select-group input-group">';
        $e[] = '<div class="form-control input-sm" style="cursor:pointer;" data-toggle="dialog-search"'.$jq.' id="'.$params['id'].'_text">'.$data[$name].'</div>';
        $e[] = '<div class="input-group-btn">';
        $e[] = '<button type="button" data-toggle="dialog-search"'.$jq.' class="btn btn-sm btn-default"><i class="icon icon-search"></i></button>';
        $e[] = '<button type="button" data-toggle="dialog-search-clear" data-id="'.$params['id'].'" data-name="'.$params['name'].'" class="btn btn-sm btn-default"><i class="icon icon-trash"></i></button>';
        $e[] = '</div>';
        $e[] = '<input type="hidden" id="'.$params['id'].'" name="'.$id.'" value="'.$data[$id].'">';
        $e[] = '<input type="hidden" id="'.$params['name'].'" name="'.$name.'" value="'.$data[$name].'">';
        $e[] = '</div>';
        return join("\n", $e);
    }
}
