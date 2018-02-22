<?php namespace Aike\Web\Stock\Controllers;

use DB;
use Input;
use Request;
use Validator;

use Aike\Web\Supplier\Warehouse;

use Aike\Web\Index\Controllers\DefaultController;

class PurchaseController extends DefaultController
{
    // 向导
    public function guideAction()
    {
        $tabs = [
            ['key' => 'guide', 'name'=>'采购统计', 'url' => 'stock/purchase/guide'],
            ['key' => 'create', 'name'=>'采购单', 'url' => 'stock/purchase/create'],
            ['key' => 'index', 'name'=>'采购单据', 'url' => 'stock/purchase/index'],
            ['key' => 'detail', 'name'=>'采购明细', 'url' => 'user/department/detail'],
            ['key' => 'invalid', 'name'=>'作废单据', 'url' => 'user/position/invalid'],
        ];

        $columns = [[
            'name'     => 'status',
            'index'    => 'warehouse.status',
            'label'    => '所属门店',
            'width'    => 100,
            'search'   => [
                'type' => 'select',
                'data' => ['1' => '启用', '0' => '停用'],
            ],
            'formatter' => 'status',
            'align'     => 'center',
        ]];

        $search_columns = search_columns($columns);
        $search = search_form([
            'referer'  => 1,
            'advanced' => 1,
        ], $search_columns);

        return $this->display(['columns' => $columns,'tabs' => $tabs, 'search' => $search]);
    }

    // 仓库列表
    public function indexAction()
    {
        $columns = [[
            'name'     => 'name',
            'index'    => 'warehouse.name',
            'search'   => 'text',
            'label'    => '名称',
            'minWidth' => 200,
            'align'    => 'left',
        ],[
            'name'    => 'sort',
            'index'   => 'warehouse.sort',
            'label'   => '排序',
            'width'   => 120,
            'align'   => 'center',
        ],[
            'name'     => 'status',
            'index'    => 'warehouse.status',
            'label'    => '状态',
            'width'    => 100,
            'search'   => [
                'type' => 'select',
                'data' => ['1' => '启用', '0' => '停用'],
            ],
            'formatter' => 'status',
            'align'     => 'center',
        ],[
            'name'    => 'updated_at',
            'index'   => 'warehouse.updated_at',
            'search'  => 'second2',
            'label'   => '操作时间',
            'width'   => 140,
            'formatter' => 'date',
            'formatoptions' => [
                'srcformat' => 'u',
                'newformat' => 'Y-m-d H:i'
            ],
            'align' => 'center',
        ],[
            'name'  => 'id',
            'index' => 'user_group.id',
            'label' => 'ID',
            'width' => 60,
            'align' => 'center',
        ]];

        $search_columns = search_columns($columns);
        $search = search_form([
            'referer'  => 1,
            'advanced' => 1,
        ], $search_columns);

        $model = Warehouse::orderBy('sort', 'asc');

        foreach ($search['where'] as $where) {
            if ($where['active']) {
                $model->search($where);
            }
        }

        if (Input::ajax()) {
            return response()->json($model->get());
        }

        $tabs = [
            ['key' => 'user', 'name'=>'采购统计', 'url' => 'user/user/index'],
            ['key' => 'role', 'name'=>'角色', 'url' => 'user/role/index'],
            ['key' => 'group', 'name'=>'用户组', 'url' => 'user/group/index'],
            ['key' => 'department', 'name'=>'部门', 'url' => 'user/department/index'],
            ['key' => 'position', 'name'=>'职位', 'url' => 'user/position/index'],
        ];

        return $this->display(array(
            'search'  => $search,
            'columns' => $columns,
            'tabs'    => $tabs,
        ));
    }

    // 新建仓库
    public function createAction()
    {
        $id = (int)Input::get('id');
        
        if (Request::method() == 'POST') {
            $gets = Input::get();

            $model = Warehouse::findOrNew($gets['id']);
            $rules = [
                'name' => 'required',
            ];
            $v = Validator::make($gets, $rules);
            if ($v->fails()) {
                return $this->back()->withErrors($v)->withInput();
            }
            $model->fill($gets)->save();
            $model->treeRebuild();
            return $this->json('恭喜你，仓库更新成功。', true);
        }

        $row = Warehouse::where('id', $id)->first();
        $types = Warehouse::orderBy('lft', 'asc')->get()->toNested();

        $models = [
            ['name' => "id", 'hidden' => true],
            ['name' => 'op', 'label' => '&nbsp;', 'formatter' => 'options', 'width' => 60, 'sortable' => false, 'align' => 'center'],
            ['name' => "product_id", 'label' => '商品ID', 'hidden' => true],
            ['name' => "warehouse_id", 'label' => '仓库ID', 'hidden' => true],
            ['name' => "product_name", 'width' => 280, 'label' => '商品名称', 'rules' => ['required'=>true], 'sortable' => false, 'editable' => true],
            ['name' => "product_spec", 'width' => 120, 'label' => '商品规格', 'rules' => ['required'=>true], 'sortable' => false, 'editable' => true],
            ['name' => "category_name", 'width' => 120, 'label' => '商品类别', 'rules' => ['required'=>true], 'sortable' => false, 'editable' => true],
            ['name' => "warehouse_name", 'width' => 120, 'label' => '仓库', 'rules' => ['required'=>true], 'sortable' => false, 'editable' => true],
            ['name' => "last_price", 'width' => 80, 'label' => '上次进价', 'rules' => ['required'=>true], 'sortable' => false, 'editable' => true],
            ['name' => "product_code", 'width' => 100, 'label' => '商品编码', 'rules' => ['required'=>true], 'sortable' => false, 'editable' => true],
            ['name' => "quantity", 'width' => 80, 'label' => '数量', 'rules' => ['required'=>true], 'sortable' => false, 'editable' => true],
            ['name' => "unit", 'width' => 80, 'label' => '单位', 'rules' => ['required'=>true], 'sortable' => false, 'editable' => true],
            ['name' => "price", 'width' => 80, 'label' => '采购单价', 'rules' => ['required'=>true], 'sortable' => false, 'editable' => true],
            ['name' => "money", 'label' => '采购金额', 'width' => 100, 'formatter' => 'integer', 'sortable' => false, 'align' => 'right'],
            ['name' => "description", 'label' => '备注', 'width' => 200, 'sortable' => false, 'editable' => true],
        ];

        $tabs = [
            ['key' => 'guide', 'name'=>'采购统计', 'url' => 'stock/purchase/guide'],
            ['key' => 'create', 'name'=>'采购单', 'url' => 'stock/purchase/create'],
            ['key' => 'index', 'name'=>'采购单据', 'url' => 'stock/purchase/index'],
            ['key' => 'detail', 'name'=>'采购明细', 'url' => 'user/department/detail'],
            ['key' => 'invalid', 'name'=>'作废单据', 'url' => 'user/position/invalid'],
        ];

        return $this->display(array(
            'type' => $types,
            'row'  => $row,
            'tabs' => $tabs,
            'models' => $models,
        ), 'create');
    }

    // 编辑仓库
    public function editAction()
    {
        $id = (int)Input::get('id');
        
        if (Request::method() == 'POST') {
            $gets = Input::get();
            
            $model = Warehouse::findOrNew($gets['id']);
            $rules = [
                'name' => 'required',
            ];
            $v = Validator::make($gets, $rules);
            if ($v->fails()) {
                return $this->back()->withErrors($v)->withInput();
            }
            $model->fill($gets)->save();
            $model->treeRebuild();
            return $this->json('恭喜你，仓库更新成功。', true);
        }

        $row = Warehouse::where('id', $id)->first();
        $types = Warehouse::orderBy('lft', 'asc')->get()->toNested();

        return $this->render(array(
            'type' => $types,
            'row'  => $row,
        ), 'create');
    }

    // 删除仓库
    public function deleteAction()
    {
        if (Request::method() == 'POST') {
            $id = (array)Input::get('id');
            if (empty($id)) {
                return $this->json('最少选择一行记录。');
            }
            Warehouse::whereIn('id', $id)->delete();
            return $this->json('恭喜你，仓库删除成功。', true);
        }
    }
}
