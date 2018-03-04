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
            'name'     => 'name',
            'index'    => 'supplier.name',
            'search'   => 'text',
            'label'    => '供应商名称',
            'minWidth' => 200,
            'align'    => 'left',
        ],[
            'name'     => 'personal_name',
            'index'    => 'supplier.personal_name',
            'search'   => 'text',
            'label'    => '联系人',
            'width' => 120,
            'align'    => 'center',
        ],[
            'name'     => 'personal_mobile',
            'index'    => 'supplier.personal_mobile',
            'search'   => 'text',
            'label'    => '联系手机',
            'width' => 160,
            'align'    => 'left',
        ],[
            'name'     => 'tel',
            'index'    => 'supplier.tel',
            'search'   => 'text',
            'label'    => '电话',
            'width' => 140,
            'align'    => 'left',
        ],[
            'name'     => 'fax',
            'index'    => 'supplier.fax',
            'search'   => 'text',
            'label'    => '传真',
            'width' => 140,
            'align'    => 'left',
        ],[
            'name'     => 'address',
            'index'    => 'supplier.address',
            'search'   => 'text',
            'label'    => '地址',
            'width' => 280,
            'align'    => 'left',
        ],[
            'name'     => 'status',
            'index'    => 'supplier.status',
            'label'    => '状态',
            'width'    => 100,
            'search'   => [
                'type' => 'select',
                'data' => [['id' => 1, 'text' => '启用'],['id' => 0, 'text' => '停用']],
            ],
            'formatter' => 'status',
            'align'     => 'center',
        ],[
            'name'    => 'updated_at',
            'index'   => 'supplier.updated_at',
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
            'index' => 'supplier.id',
            'label' => 'ID',
            'width' => 60,
            'align' => 'center',
        ]];

        $search_columns = search_columns($columns);
        $search = search_form([
            'referer'  => 1,
            'advanced' => 1,
        ], $search_columns);

        if (Input::ajax()) {
            $model = Supplier::query();

            foreach ($search['where'] as $where) {
                if ($where['active']) {
                    $model->search($where);
                }
            }
            return response()->json($model->paginate($search['limit']));
        }

        return $this->display(array(
            'search'  => $search,
            'columns' => $columns,
        ));
    }

    // 新建供应商
    public function createAction()
    {
        $id = (int)Input::get('id');
        
        if (Request::method() == 'POST') {
            $gets = Input::get();

            $model = Supplier::findOrNew($gets['id']);
            $rules = [
                'name' => 'required',
            ];
            $v = Validator::make($gets, $rules);
            if ($v->fails()) {
                return $this->json($v->errors()->all());
            }
            $model->fill($gets)->save();
            return $this->json('恭喜你，供应商更新成功。', true);
        }

        $row = Supplier::where('id', $id)->first();
        return $this->render(array(
            'row' => $row,
        ), 'create');
    }

    // 编辑供应商
    public function editAction()
    {
        $id = (int)Input::get('id');
        
        if (Request::method() == 'POST') {
            $gets = Input::get();
            
            $model = Supplier::findOrNew($gets['id']);
            $rules = [
                'name' => 'required',
            ];
            $v = Validator::make($gets, $rules);
            if ($v->fails()) {
                return $this->json($v->errors()->all());
            }
            $model->fill($gets)->save();
            return $this->json('恭喜你，供应商更新成功。', true);
        }
        $row = Supplier::where('id', $id)->first();
        return $this->render(array(
            'row' => $row,
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
            ['text','supplier.name','供应商名称'],
            ['text','supplier.id','编号'],
            ['address','supplier.address','地址'],
        ]);

        $query = $search['query'];

        if (Request::method() == 'POST') {
            $model = Supplier::select(['*', 'name as text']);

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

            $rows = $model->paginate();

            return response()->json($rows);
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
            Supplier::whereIn('id', $id)->delete();
            return $this->json('恭喜你，供应商删除成功。', true);
        }
    }
}
