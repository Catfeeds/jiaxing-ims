<?php namespace Aike\Web\Car\Controllers;

use DB;
use Input;
use Request;

use Aike\Web\Car\Brand;

use Aike\Web\Index\Controllers\DefaultController;

class BrandController extends DefaultController
{
    public $permission = [];

    // 门店列表
    public function indexAction()
    {
        $columns = [[
            'name'     => 'name',
            'index'    => 'car_brand.name',
            'search'   => 'text',
            'label'    => '名称',
            'minWidth' => 200,
            'align'    => 'left',
        ],[
            'name'  => 'id',
            'index' => 'car_brand.id',
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

        $model = Brand::query();

        foreach ($search['where'] as $where) {
            if ($where['active']) {
                $model->search($where);
            }
        }

        if (Input::ajax()) {
            return response()->json($model->paginate($search['limit']));
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
                Brand::where('id', $gets['id'])->update($gets);
            } else {
                Brand::insert($gets);
            }
            return $this->json('恭喜你，门店更新成功。', true);
        }

        $row = Brand::where('id', $id)->first();

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
                Brand::where('id', $gets['id'])->update($gets);
            } else {
                Brand::insert($gets);
            }
            return $this->json('恭喜你，门店更新成功。', true);
        }

        $row = Brand::where('id', $id)->first();

        return $this->render(array(
            'row'  => $row,
        ), 'create');
    }

    // 删除门店
    public function deleteAction()
    {
        if (Request::method() == 'POST') {
            $id = (array)Input::get('id');
            if (empty($id)) {
                return $this->json('最少选择一行记录。');
            }
            Brand::whereIn('id', $id)->delete();
            return $this->json('恭喜你，操作成功。', true);
        }
    }
}
