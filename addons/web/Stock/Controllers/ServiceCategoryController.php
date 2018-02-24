<?php namespace Aike\Web\Stock\Controllers;

use DB;
use Input;
use Request;
use Validator;

use Aike\Web\Supplier\ProductCategory;
use Aike\Web\Supplier\Warehouse;

use Aike\Web\Index\Controllers\DefaultController;

class ServiceCategoryController extends DefaultController
{
    public $permission = ['dialog'];
    
    // 仓库列表
    public function indexAction()
    {
        $columns = [[
            'name'     => 'text',
            'index'    => 'product_category.name',
            'search'   => 'text',
            'label'    => '名称',
            'minWidth' => 200,
            'align'    => 'left',
        ],[
            'name'    => 'sort',
            'index'   => 'product_category.sort',
            'label'   => '排序',
            'width'   => 120,
            'align'   => 'center',
        ],[
            'name'     => 'status',
            'index'    => 'product_category.status',
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
            'index'   => 'product_category.updated_at',
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
            'index' => 'product_category.id',
            'label' => 'ID',
            'width' => 60,
            'align' => 'center',
        ]];

        $search_columns = search_columns($columns);
        $search = search_form([
            'referer'  => 1,
            'advanced' => 1,
        ], $search_columns);

        $model = ProductCategory::where('type', 2)->orderBy('sort', 'asc');

        foreach ($search['where'] as $where) {
            if ($where['active']) {
                $model->search($where);
            }
        }

        if (Input::ajax()) {
            $json = [];
            $model->get()->toNested()->each(function ($row) use (&$json) {
                $row['text'] = $row['layer_html'].$row['name'];
                $json[] = $row;
            });
            return response()->json($json);
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

            $model = ProductCategory::findOrNew($gets['id']);
            $rules = [
                'name' => 'required',
            ];
            $v = Validator::make($gets, $rules);
            if ($v->fails()) {
                return $this->json($v->errors()->all());
            }

            $gets['type'] = 2;
            $model->fill($gets)->save();
            $model->treeRebuild();
            return $this->json('恭喜你，服务类别更新成功。', true);
        }

        $row = ProductCategory::where('id', $id)->first();
        $categorys = ProductCategory::where('type', 2)->orderBy('lft', 'asc')->get()->toNested();

        return $this->render(array(
            'categorys' => $categorys,
            'row'  => $row,
        ), 'create');
    }

    // 编辑仓库
    public function editAction()
    {
        $id = (int)Input::get('id');
        
        if (Request::method() == 'POST') {
            $gets = Input::get();
            
            $model = ProductCategory::findOrNew($gets['id']);
            $rules = [
                'name' => 'required',
            ];
            $v = Validator::make($gets, $rules);
            if ($v->fails()) {
                return $this->json($v->errors()->all());
            }
            $gets['type'] = 2;
            $model->fill($gets)->save();
            $model->treeRebuild();
            return $this->json('恭喜你，服务类别更新成功。', true);
        }

        $row = ProductCategory::where('id', $id)->first();
        $categorys = ProductCategory::where('type', 2)->orderBy('lft', 'asc')->get()->toNested();

        return $this->render(array(
            'categorys' => $categorys,
            'row'  => $row,
        ), 'create');
    }

    /**
     * 弹出层信息
     */
    public function dialogAction()
    {
        $gets = Input::get();

        if (Request::method() == 'POST') {
            $maps = ProductCategory::where('type', 2)->orderBy('lft', 'asc')->get()->toNested();
            $rows = [];
            foreach ($maps as $row) {
                $row['text'] = $row['layer_space'].$row['name'];
                $rows[] = $row;
            }
            $json = ['total' => count($rows), 'data' => $rows];
            return response()->json($json);
        }

        return $this->render(array(
            'gets' => $gets,
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

            $count = DB::table('product')->where('category_id', $id)->count();
            if ($count > 0) {
                return $this->json('很抱歉，分类下面存在商品，请先删除。');
            }

            ProductCategory::whereIn('id', $id)->delete();
            return $this->json('恭喜你，服务类别删除成功。', true);
        }
    }
}
