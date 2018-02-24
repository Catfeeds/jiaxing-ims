<?php namespace Aike\Web\Car\Controllers;

use DB;
use Input;
use Request;

use Aike\Web\Car\Plate;

use Aike\Web\Index\Controllers\DefaultController;

class PlateController extends DefaultController
{
    public $permission = [];

    // 车型列表
    public function indexAction()
    {
        $columns = [[
            'name'     => 'name',
            'index'    => 'car_plate.name',
            'search'   => 'text',
            'label'    => '车牌前缀名称',
            'minWidth' => 200,
            'align'    => 'left',
        ],[
            'name'     => 'region',
            'index'    => 'car_plate.region',
            'search'   => 'text',
            'label'    => '省份',
            'width'    => 140,
            'align'    => 'center',
        ],[
            'name'     => 'remark',
            'index'    => 'car_plate.remark',
            'search'   => 'text',
            'label'    => '备注',
            'width'    => 140,
            'align'    => 'center',
        ],[
            'name'  => 'id',
            'index' => 'car_plate.id',
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

        $model = Plate::query();

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

    // 新建车型
    public function createAction()
    {
        $id = (int)Input::get('id');

        if (Request::method() == 'POST') {
            $gets = Input::get();

            if (empty($gets['name'])) {
                return $this->json('车牌前缀名称必须填写。');
            }

            if ($gets['id']) {
                Plate::where('id', $gets['id'])->update($gets);
            } else {
                Plate::insert($gets);
            }
            return $this->json('恭喜你，车牌更新成功。', true);
        }
        $row = Plate::where('id', $id)->first();
        return $this->render(array(
            'row' => $row,
        ));
    }

    // 编辑车型
    public function editAction()
    {
        $id = (int)Input::get('id');

        if (Request::method() == 'POST') {
            $gets = Input::get();

            if (empty($gets['name'])) {
                return $this->json('车牌前缀名称必须填写。');
            }

            if ($gets['id']) {
                Plate::where('id', $gets['id'])->update($gets);
            } else {
                Plate::insert($gets);
            }
            return $this->json('恭喜你，车牌更新成功。', true);
        }

        $row = Plate::where('id', $id)->first();
        return $this->render(array(
            'row' => $row,
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
            Plate::whereIn('id', $id)->delete();
            return $this->json('恭喜你，操作成功。', true);
        }
    }
}
