<?php namespace Aike\Web\Stock\Controllers;

use DB;
use Input;
use Request;
use Validator;

use Aike\Web\Supplier\Warehouse;

use Aike\Web\Index\Controllers\DefaultController;

class WarehouseController extends DefaultController
{
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

        return $this->display(array(
            'search'  => $search,
            'columns' => $columns,
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

        return $this->render(array(
            'type' => $types,
            'row'  => $row,
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
