<?php namespace Aike\Web\Stock\Controllers;

use DB;
use Input;
use Request;
use Validator;

use Aike\Web\Stock\StockWarehouse;

use Aike\Web\Index\Controllers\DefaultController;

class StockWarehouseController extends DefaultController
{
    public $permission = ['dialog'];

    /**
     * 弹出商品
     */
    public function dialogAction()
    {
        $search = search_form([
            'advanced'    => 1,
            'page'        => 1,
            'category_id' => 0,
            'sort'        => '',
            'order'       => '',
            'limit'       => '',
        ], [
            ['text2','product.name|product.spec|product.barcode|product.id','名称 / 规格 / 条码 / ID'],
        ]);

        $query = $search['query'];

        if (Request::method() == 'POST') {
            $model = StockWarehouse::leftJoin('product', 'product.id', '=', 'stock_warehouse.product_id')
            ->leftJoin('product_category', 'product_category.id', '=', 'product.category_id')
            ->leftJoin('warehouse', 'warehouse.id', '=', 'stock_warehouse.warehouse_id')
            ->where('product.type', 1)
            ->where('product.status', 1)
            ->orderBy('product_category.lft', 'asc');

            // 排序方式
            if ($query['sort'] && $query['order']) {
                $model->orderBy($query['sort'], $query['order']);
            } else {
                $model->orderBy('product.sort', 'asc');
            }

            if ($query['category_id']) {
                $category = DB::table('product_category')->where('id', $query['category_id'])->first(['lft', 'rgt']);
                $model->whereBetween('product_category.lft', [$category['lft'], $category['rgt']]);
            }

            // 搜索条件
            foreach ($search['where'] as $where) {
                if ($where['active']) {
                    $model->search($where);
                }
            }

            $model->selectRaw("
                stock_warehouse.*,
                product.name as text,
                product.name as product_name,
                product.unit as product_unit,
                product.spec as product_spec,
                product.price as product_price,
                product.barcode as product_barcode,
                product_category.name as category_name,
                warehouse.name as warehouse_name,
                warehouse.id as warehouse_id,
                1 as quantity
            ");
            $rows = $model->paginate($query['limit']);
            return response()->json($rows);
        }
        $gets = Input::get();
        return $this->render([
            'search' => $search,
            'gets'   => $gets,
        ]);
    }
}
