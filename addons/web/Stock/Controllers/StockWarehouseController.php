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
        $gets = Input::get();

        $abc = [
            ['text','product.name','产品名称'],
            ['text','product.spec','产品规格'],
            ['text','product.barcode','产品条码'],
            ['text','product.barcode','存货编码'],
            ['status','product.status','产品状态'],
            ['category','product.category_id','产品类别'],
            ['text','product.id','产品ID'],
        ];

        if ($gets['type'] == 2) {
            $abc[] = ['supplier','product.supplier_id','供应商'];
        }

        $search = search_form([
            'advanced'    => '',
            'owner_id'    => 0,
            'supplier_id' => 0,
            'type'        => 1,
            'page'        => 1,
            'sort'        => '',
            'order'       => '',
            'limit'       => '',
        ], $abc);
        
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

            // 搜索条件
            foreach ($search['where'] as $where) {
                if ($where['active']) {
                    $model->search($where);
                }
            }

            $rows = $model->selectRaw("
            stock_warehouse.*,
            product.name as text,
            product.name as product_name,
            product.unit as product_unit,
            product.spec as product_spec,
            product.price as product_price,
            product.barcode as product_barcode,
            product_category.name as category_name,
            warehouse.name as warehouse_name,
            warehouse.id as warehouse_id
            ")->paginate($query['limit']);

            return response()->json($rows);
        }
        return $this->render(array(
            'search' => $search,
            'gets'   => $gets,
        ), 'jqgrid');
    }
}
