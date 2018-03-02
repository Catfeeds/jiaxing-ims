<?php namespace Aike\Web\Stock\Controllers;

use DB;
use Input;
use Request;
use Validator;

use Aike\Web\Stock\Stock;
use Aike\Web\Stock\Product;
use Aike\Web\Stock\StockLine;

use Aike\Web\Index\Controllers\DefaultController;

class StockController extends DefaultController
{
    // 库存统计
    public function countAction()
    {
        $suppliers = DB::table('supplier')->get(['id', 'name as text']);
        $stores    = DB::table('store')->get(['id', 'name as text']);

        $date = Input::get('date', 'day');
        if ($date == 'day') {
            $values = [date('Y-m-d'),date('Y-m-d')];
        }
        if ($date == 'month') {
            $values = [date('Y-m-01'),date('Y-m-d')];
        }
        if ($date == 'year') {
            $values = [date('Y-01-01'),date('Y-m-d')];
        }

        $columns = [[
            'name'     => 'store_name',
            'index'    => 'store.id',
            'label'    => '门店',
            'search'   => [
                'type' => 'select',
                'data' => $stores,
            ],
            'width'   => 160,
            'align'   => 'center',
        ],[
            'name'    => 'date',
            'index'   => 'stock.date',
            'search'  => [
                'type'  => 'date2',
                'value' => $values,
            ],
            'label'   => '日期',
            'width'   => 100,
            'formatter' => 'date',
            'formatoptions' => [
                'srcformat' => 'Y-m-d',
                'newformat' => 'Y-m-d'
            ],
            'align' => 'center',
        ]];

        $search_columns = search_columns($columns);
        $search = search_form([
            'referer'  => 1,
            'advanced' => 1,
            'date'     => 'day',
        ], $search_columns);

        if (Input::ajax()) {
            $model = StockLine::orderBy('stock_line.id', 'desc')
            ->leftJoin('stock', 'stock.id', '=', 'stock_line.stock_id')
            ->leftJoin('product', 'product.id', '=', 'stock_line.product_id')
            ->leftJoin('supplier', 'supplier.id', '=', 'stock.supplier_id')
            ->leftJoin('warehouse', 'warehouse.id', '=', 'stock_line.warehouse_id')
            ->leftJoin('store', 'store.id', '=', 'stock.store_id')
            ->where('stock.type_id', 1)
            ->select([
                'stock_line.*',
                'store.name as store_name',
                'stock.sn',
                'stock.date',
                'warehouse.name as warehouse_name',
                'product.barcode as product_barcode',
                'product.name as product_name',
                'product.spec as product_spec',
                'supplier.name as supplier_name'
            ]);

            foreach ($search['where'] as $where) {
                if ($where['active']) {
                    $model->search($where);
                }
            }
            return response()->json($model->paginate($search['limit']));
        }

        $types = DB::table('stock_type')->get();

        return $this->display([
            'search'  => $search,
            'columns' => $columns,
            'other'   => $other,
            'types'   => $types,
        ]);
    }

    // 库存列表
    public function indexAction()
    {
        $warehouses = DB::table('warehouse')->get(['id', 'name as text']);
        $stores     = DB::table('store')->get(['id', 'name as text']);

        $columns = [[
            'name'     => 'name',
            'index'    => 'product.name',
            'search'   => 'text',
            'label'    => '商品',
            'width' => 160,
            'align'    => 'center',
        ],[
            'name'    => 'spec',
            'index'   => 'product.spec',
            'search'   => 'text',
            'label'   => '规格',
            'width'   => 120,
            'align'   => 'center',
        ],[
            'name'    => 'barcode',
            'index'   => 'product.barcode',
            'search'   => 'text',
            'label'   => '条码',
            'width'   => 120,
            'align'   => 'center',
        ],[
            'name'    => 'category_name',
            'index'   => 'category.name',
            'label'   => '商品类别',
            'search'   => [
                'type' => 'select',
                'url'  => 'stock/product-category/dialog',
            ],
            'width'   => 120,
            'align'   => 'center',
        ],[
            'name'    => 'warehouse_name',
            'index'   => 'warehouse.id',
            'label'   => '默认仓库',
            'search'   => [
                'type' => 'select',
                'data' => $warehouses,
            ],
            'width'    => 160,
            'align'    => 'center',
        ],[
            'name'     => 'store_name',
            'index'    => 'store.id',
            'label'    => '门店',
            'search'   => [
                'type' => 'select',
                'data' => $stores,
            ],
            'width'   => 160,
            'align'   => 'center',
        ],[
            'name'    => 'stock_quantity',
            'index'   => 'product.stock_quantity',
            'label'   => '当前库存',
            'width'   => 80,
            'align'   => 'right',
        ],[
            'name'    => 'cost',
            'index'   => 'product.cost',
            'label'   => '成本价格',
            'width'   => 80,
            'align'   => 'right',
        ],[
            'name'    => 'price',
            'index'   => 'product.price',
            'label'   => '销售价格',
            'width'   => 80,
            'align'   => 'right',
        ],[
            'name'    => 'stock_min',
            'index'   => 'product.stock_min',
            'label'   => '最小库存',
            'width'   => 80,
            'align'   => 'right',
        ],[
            'name'    => 'stock_max',
            'index'   => 'product.stock_max',
            'label'   => '最大库存',
            'width'   => 80,
            'align'   => 'right',
        ],[
            'name'    => 'action',
            'formatter' => 'actionLink',
            'formatoptions' => [
                'edit' => '修改成本',
            ],
            'label'   => '&nbsp;',
            'width'   => 100,
            'align'   => 'center',
        ]];

        $search_columns = search_columns($columns);
        $search = search_form([
            'referer'  => 1,
            'advanced' => 1,
        ], $search_columns);

        $model = Product::orderBy('product.id', 'desc')
        ->leftJoin('product_category', 'product_category.id', '=', 'product.category_id')
        ->leftJoin('warehouse', 'warehouse.id', '=', 'product.warehouse_id')
        ->leftJoin('store', 'store.id', '=', 'product.store_id')
        ->select([
            'product.*',
            'store.name as store_name',
            'warehouse.name as warehouse_name',
            'product_category.name as category_name',
        ])
        ->where('product.status', 1)
        ->where('product.type', 1);

        foreach ($search['where'] as $where) {
            if ($where['active']) {
                $model->search($where);
            }
        }

        if (Input::ajax()) {
            return response()->json($model->paginate($search['limit']));
        }

        return $this->display(array(
            'search'  => $search,
            'columns' => $columns,
        ));
    }

    // 库存预警
    public function warningAction()
    {
        $warehouses = DB::table('warehouse')->get(['id', 'name as text']);
        $stores     = DB::table('store')->get(['id', 'name as text']);

        $columns = [[
            'name'     => 'name',
            'index'    => 'product.name',
            'search'   => 'text',
            'label'    => '商品',
            'width' => 160,
            'align'    => 'center',
        ],[
            'name'    => 'spec',
            'index'   => 'product.spec',
            'search'   => 'text',
            'label'   => '规格',
            'width'   => 120,
            'align'   => 'center',
        ],[
            'name'    => 'barcode',
            'index'   => 'product.barcode',
            'search'   => 'text',
            'label'   => '条码',
            'width'   => 120,
            'align'   => 'center',
        ],[
            'name'    => 'category_name',
            'index'   => 'category.name',
            'label'   => '商品类别',
            'search'   => [
                'type' => 'select',
                'url'  => 'stock/product-category/dialog',
            ],
            'width'   => 120,
            'align'   => 'center',
        ],[
            'name'    => 'warehouse_name',
            'index'   => 'warehouse.id',
            'label'   => '默认仓库',
            'search'   => [
                'type' => 'select',
                'data' => $warehouses,
            ],
            'width'    => 160,
            'align'    => 'center',
        ],[
            'name'     => 'store_name',
            'index'    => 'store.id',
            'label'    => '门店',
            'search'   => [
                'type' => 'select',
                'data' => $stores,
            ],
            'width'   => 160,
            'align'   => 'center',
        ],[
            'name'    => 'stock_quantity',
            'index'   => 'product.stock_quantity',
            'label'   => '当前库存',
            'width'   => 80,
            'align'   => 'right',
        ],[
            'name'    => 'stock_min',
            'index'   => 'product.stock_min',
            'label'   => '最小库存',
            'width'   => 80,
            'align'   => 'right',
        ],[
            'name'    => 'stock_max',
            'index'   => 'product.stock_max',
            'label'   => '最大库存',
            'width'   => 80,
            'align'   => 'right',
        ],[
            'name'    => 'action',
            'formatter' => 'actionLink',
            'formatoptions' => [
                'edit' => '设置预警',
            ],
            'label'   => '&nbsp;',
            'width'   => 100,
            'align'   => 'center',
        ]];

        $search_columns = search_columns($columns);
        $search = search_form([
            'referer'  => 1,
            'advanced' => 1,
        ], $search_columns);

        $model = Product::orderBy('product.id', 'desc')
        ->leftJoin('product_category', 'product_category.id', '=', 'product.category_id')
        ->leftJoin('warehouse', 'warehouse.id', '=', 'product.warehouse_id')
        ->leftJoin('store', 'store.id', '=', 'product.store_id')
        ->select([
            'product.*',
            'store.name as store_name',
            'warehouse.name as warehouse_name',
            'product_category.name as category_name',
        ])
        ->where('product.status', 1)
        ->where('product.type', 1);

        foreach ($search['where'] as $where) {
            if ($where['active']) {
                $model->search($where);
            }
        }

        if (Input::ajax()) {
            return response()->json($model->paginate($search['limit']));
        }

        return $this->display(array(
            'search'  => $search,
            'columns' => $columns,
        ));
    }

    // 商品收发明细
    public function lineAction()
    {
        $warehouses = DB::table('warehouse')->get(['id', 'name as text']);
        $stores     = DB::table('store')->get(['id', 'name as text']);
        $types      = DB::table('stock_type')->get(['id', 'name as text']);

        $columns = [[
            'name'     => 'name',
            'index'    => 'product.name',
            'search'   => 'text',
            'label'    => '商品',
            'width' => 160,
            'align'    => 'center',
        ],[
            'name'    => 'spec',
            'index'   => 'product.spec',
            'search'   => 'text',
            'label'   => '规格',
            'width'   => 120,
            'align'   => 'center',
        ],[
            'name'    => 'date',
            'index'   => 'stock.date',
            'search'   => 'date2',
            'label'   => '单据日期',
            'width'   => 120,
            'align'   => 'center',
        ],[
            'name'    => 'sn',
            'index'   => 'stock.sn',
            'search'   => 'text',
            'label'   => '单号',
            'width'   => 140,
            'align'   => 'center',
        ],[
            'name'    => 'type_name',
            'index'   => 'stock_type.id',
            'label'   => '单据类型',
            'search'   => [
                'type' => 'select',
                'data' => $types,
            ],
            'width'    => 120,
            'align'    => 'center',
        ],[
            'name'    => 'warehouse_name',
            'index'   => 'warehouse.id',
            'label'   => '仓库',
            'search'   => [
                'type' => 'select',
                'data' => $warehouses,
            ],
            'width'    => 160,
            'align'    => 'center',
        ],[
            'name'     => 'store_name',
            'index'    => 'store.id',
            'label'    => '门店',
            'search'   => [
                'type' => 'select',
                'data' => $stores,
            ],
            'width'   => 160,
            'align'   => 'center',
        ],[
            'name'    => 'line_quantity',
            'index'   => 'stock_line.quantity',
            'label'   => '数量',
            'width'   => 120,
            'align'   => 'right',
        ],[
            'name'    => 'line_price',
            'index'   => 'stock_line.price',
            'label'   => '单价',
            'width'   => 120,
            'align'   => 'right',
        ],[
            'name'    => 'line_money',
            'index'   => 'stock_line.line_money',
            'label'   => '金额',
            'width'   => 120,
            'align'   => 'right',
        ]];

        $search_columns = search_columns($columns);
        $search = search_form([
            'referer'  => 1,
            'advanced' => 1,
        ], $search_columns);

        $model = StockLine::orderBy('product.id', 'desc')
        ->leftJoin('product', 'product.id', '=', 'stock_line.product_id')
        ->leftJoin('stock', 'stock.id', '=', 'stock_line.stock_id')
        ->leftJoin('stock_type', 'stock_type.id', '=', 'stock.type_id')
        ->leftJoin('warehouse', 'warehouse.id', '=', 'stock_line.warehouse_id')
        ->leftJoin('store', 'store.id', '=', 'stock.store_id')
        ->select([
            'product.*',
            'stock_line.quantity as line_quantity',
            'stock_line.price as line_price',
            'stock_line.money as line_money',
            'store.name as store_name',
            'warehouse.name as warehouse_name',
            'stock.date',
            'stock.sn',
            'stock_type.name as type_name'
        ])
        ->where('product.status', 1)
        ->where('product.type', 1);

        foreach ($search['where'] as $where) {
            if ($where['active']) {
                $model->search($where);
            }
        }

        if (Input::ajax()) {
            return response()->json($model->paginate($search['limit']));
        }

        return $this->display(array(
            'search'  => $search,
            'columns' => $columns,
        ));
    }

    // 修改预警
    public function warningEditAction()
    {
        $gets = Input::get();
        $row = Product::where('id', $gets['id'])->first();

        if (Request::method() == 'POST') {
            if ($row->id) {
                $row->fill($gets);
                $row->save();
                return $this->json('恭喜你，库存预警修改成功。', true);
            } else {
                return $this->json('很抱歉，商品不存在。');
            }
        }

        return $this->render([
            'row' => $row
        ]);
    }

    // 修改成本
    public function costAction()
    {
        $gets = Input::get();
        $row = Product::where('id', $gets['id'])->first();

        if (Request::method() == 'POST') {
            if ($row->id) {
                $row->cost = $gets['cost'];
                $row->save();
                return $this->json('恭喜你，商品成本修改成功。', true);
            } else {
                return $this->json('很抱歉，商品不存在。');
            }
        }

        return $this->render([
            'row' => $row
        ]);
    }
}
