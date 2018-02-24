<?php namespace Aike\Web\Car\Controllers;

use DB;
use Input;
use Request;

use Aike\Web\Car\Type;
use Aike\Web\Car\Brand;

use Aike\Web\Index\Controllers\DefaultController;

class TypeController extends DefaultController
{
    public $permission = [];

    // 车型列表
    public function indexAction()
    {
        $columns = [[
            'name'     => 'name',
            'index'    => 'car_brand.name',
            'search'   => 'text',
            'label'    => '车型名称',
            'minWidth' => 200,
            'align'    => 'left',
        ],[
            'name'     => 'brand_name',
            'index'    => 'car_brand.name',
            'search'   => 'text',
            'label'    => '品牌',
            'width'    => 140,
            'align'    => 'center',
        ],[
            'name'     => 'brand_price',
            'index'    => 'car_brand.price',
            'label'    => '价格',
            'width'    => 180,
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

        /*
        $model = Type::LeftJoin('car_brand', 'car_brand.name', '=', 'car_type.brand_id');
        $rows = $model->get(['car_brand.id as brand_id', 'car_type.id']);

        foreach ($rows as $row) {
            DB::table('car_type')->where('id', $row['id'])->update(['brand_id' => $row['brand_id']]);
        }
        echo 1;
        exit;
        */

        $model = Type::LeftJoin('car_brand', 'car_brand.id', '=', 'car_type.brand_id');

        foreach ($search['where'] as $where) {
            if ($where['active']) {
                $model->search($where);
            }
        }

        $model->select(['car_type.*', 'car_brand.name as brand_name']);

        if (Input::ajax()) {
            return response()->json($model->paginate($search['limit']));
        }

        return $this->display([
            'search'  => $search,
            'columns' => $columns,
        ]);
    }

    // 新建车型
    public function createAction()
    {
        $id = (int)Input::get('id');

        if (Request::method() == 'POST') {
            $gets = Input::get();

            if (empty($gets['name'])) {
                return $this->json('车型名称必须填写。');
            }

            if ($gets['id']) {
                Type::where('id', $gets['id'])->update($gets);
            } else {
                Type::insert($gets);
            }
            return $this->json('恭喜你，车型更新成功。', true);
        }

        $row = Type::where('id', $id)->first();
        $brands = Brand::get();

        return $this->render(array(
            'row'    => $row,
            'brands' => $brands,
        ));
    }

    // 编辑车型
    public function editAction()
    {
        $id = (int)Input::get('id');

        if (Request::method() == 'POST') {
            $gets = Input::get();

            if (empty($gets['name'])) {
                return $this->json('车型名称必须填写。');
            }

            if ($gets['id']) {
                Type::where('id', $gets['id'])->update($gets);
            } else {
                Type::insert($gets);
            }
            return $this->json('恭喜你，车型更新成功。', true);
        }

        $row = Type::where('id', $id)->first();
        $brands = Brand::get();

        return $this->render(array(
            'row'    => $row,
            'brands' => $brands,
        ), 'create');
    }

    // 删除车型
    public function deleteAction()
    {
        if (Request::method() == 'POST') {
            $id = (array)Input::get('id');
            if (empty($id)) {
                return $this->json('最少选择一行记录。');
            }
            Type::whereIn('id', $id)->delete();
            return $this->json('恭喜你，操作成功。', true);
        }
    }
}
