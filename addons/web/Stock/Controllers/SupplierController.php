<?php namespace Aike\Web\Stock\Controllers;

use DB;
use Input;
use Request;
use Validator;

use Aike\Web\Stock\Supplier;

use Aike\Web\Index\Controllers\DefaultController;

class SupplierController extends DefaultController
{
    public $permission = ['dialog'];

    // 仓库列表
    public function indexAction()
    {
        $columns = [[
            'name'     => 'user.name',
            'index'    => 'user.name',
            'search'   => 'text',
            'label'    => '名称',
            'minWidth' => 200,
            'align'    => 'left',
        ],[
            'name'    => 'sort',
            'index'   => 'supplier.sort',
            'label'   => '排序',
            'width'   => 120,
            'align'   => 'center',
        ],[
            'name'     => 'status',
            'index'    => 'supplier.status',
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
            'index'   => 'supplier.updated_at',
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

        $model = Supplier::with('user');

        foreach ($search['where'] as $where) {
            if ($where['active']) {
                $model->search($where);
            }
        }

        if (Input::ajax()) {
            return response()->json($model->paginate($search['limit'])->appends($query));
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

    // 供应商对话框
    public function dialogAction()
    {
        $search = search_form([
            'advanced' => '',
            'sort'     => '',
            'order'    => '',
            'offset'   => 0,
            'limit'    => 10,
        ], [
            ['text','user.name','姓名'],
            ['text','user.login','账号'],
            ['text','user.id','编号'],
            ['address','user.address','地址'],
        ]);

        $query = $search['query'];

        if (Request::method() == 'POST') {
            $model = Supplier::LeftJoin('user', 'user.id', '=', 'supplier.user_id');
            $model->where('category_id', 1);

            // 排序方式
            if ($query['sort'] && $query['order']) {
                $model->orderBy($query['sort'], $query['order']);
            }

            // 搜索条件
            foreach ($search['where'] as $where) {
                if ($where['active']) {
                    $model->search($where);
                }
            }

            $json['total'] = $model->count();
            $rows = $model->skip($query['offset'])->take($query['limit'])
            ->get(['supplier.id', 'supplier.user_id', 'user.role_id', 'user.status', 'user.login', 'user.name as text', 'user.name', 'user.email', 'user.mobile']);
            
            $json['data'] = $rows;
            return response()->json($json);
        }
        $get = Input::get();

        return $this->render(array(
            'search' => $search,
            'get'    => $get,
        ));
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
