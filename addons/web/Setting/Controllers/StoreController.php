<?php namespace Aike\Web\Setting\Controllers;

use DB;
use Input;
use Request;

use Aike\Web\Setting\Store;

use Aike\Web\Index\Controllers\DefaultController;

class StoreController extends DefaultController
{
    public $permission = ['map', 'dialog'];

    // 门店列表
    public function indexAction()
    {
        $columns = [[
            'name'     => 'name',
            'index'    => 'store.name',
            'search'   => 'text',
            'label'    => '门店名称',
            'minWidth' => 200,
            'align'    => 'left',
        ],[
            'name'    => 'tel',
            'index'   => 'store.tel',
            'label'   => '门店电话',
            'search'   => 'text',
            'width'   => 180,
            'align'   => 'center',
        ],[
            'name'    => 'address',
            'index'   => 'store.address',
            'label'   => '门店地址',
            'search'   => 'text',
            'width'   => 260,
            'align'   => 'left',
        ],[
            'name'    => 'image',
            'index'   => 'store.image',
            'label'   => '门店图片',
            'width'   => 160,
            'align'   => 'left',
        ],[
            'name'      => 'main',
            'index'     => 'store.main',
            'formatter' => 'select',
            'search'    => [
                'type'  => 'select',
                'data'  => [['id' => 1, 'text' => '是'],['id' => 0, 'text' => '否']],
            ],
            'label'   => '是否总店',
            'width'   => 80,
            'align'   => 'center',
        ],[
            'name'  => 'id',
            'index' => 'store.id',
            'label' => 'ID',
            'width' => 60,
            'align' => 'center',
        ]];

        $search_columns = search_columns($columns);
        $search = search_form([
            'referer'  => 1,
            'advanced' => 1,
        ], $search_columns);

        $query = $search['query'];

        $model = Store::orderBy('sort', 'asc');

        foreach ($search['where'] as $where) {
            if ($where['active']) {
                $model->search($where);
            }
        }

        if (Input::ajax()) {
            return response()->json($model->get());
        }

        return $this->display([
            'search'  => $search,
            'columns' => $columns,
        ]);
    }

    // 新建门店
    public function createAction()
    {
        $id = (int)Input::get('id');

        if (Request::method() == 'POST') {
            $gets = Input::get();

            if (empty($gets['name'])) {
                return $this->json('门店名称必须填写。');
            }

            if ($gets['id']) {
                Store::where('id', $gets['id'])->update($gets);
            } else {
                Store::insert($gets);
            }
            return $this->json('恭喜你，门店更新成功。', true);
        }

        $row = Store::where('id', $id)->first();

        return $this->render(array(
            'row'  => $row,
        ));
    }

    // 编辑门店
    public function editAction()
    {
        $id = (int)Input::get('id');

        if (Request::method() == 'POST') {
            $gets = Input::get();

            if (empty($gets['name'])) {
                return $this->json('门店名称必须填写。');
            }

            if ($gets['id']) {
                Store::where('id', $gets['id'])->update($gets);
            } else {
                Store::insert($gets);
            }
            return $this->json('恭喜你，门店更新成功。', true);
        }

        $row = Store::where('id', $id)->first();

        return $this->render(array(
            'row'  => $row,
        ), 'create');
    }

    // 门店地图
    public function mapAction()
    {
        return $this->render();
    }

    // 门店对话框
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
            $model = Store::select(['*', 'name as text']);

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

    // 删除门店
    public function deleteAction()
    {
        if (Request::method() == 'POST') {
            $id = (array)Input::get('id');
            if (empty($id)) {
                return $this->json('最少选择一行记录。');
            }
            Store::whereIn('id', $id)->delete();
            return $this->json('恭喜你，操作成功。', true);
        }
    }
}
