<?php namespace Aike\Web\Stock\Controllers;

use DB;
use Input;
use Request;
use Validator;

use Aike\Web\Stock\StockWarehouse;
use Aike\Web\Stock\Stock;
use Aike\Web\Stock\Product;
use Aike\Web\Stock\StockLine;

use Aike\Web\Index\Controllers\DefaultController;

class StockController extends DefaultController
{
    // 首页
    public function homeAction()
    {
        return $this->display();
    }

    // 库存统计
    public function countAction()
    {
        $suppliers = DB::table('supplier')->get(['id', 'name as text']);
        $stores    = DB::table('store')->get(['id', 'name as text']);

        $date = Input::get('date', 'day');
        if ($date == 'day') {
            $dates = [date('Y-m-d'),date('Y-m-d')];
        }
        if ($date == 'month') {
            $dates = [date('Y-m-01'),date('Y-m-d')];
        }
        if ($date == 'year') {
            $dates = [date('Y-01-01'),date('Y-m-d')];
        }

        $columns = [[
            'name'     => 'store_name',
            'index'    => 'stock.store_id',
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
                'value' => $dates,
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
            'date'     => $date,
        ], $search_columns);

        // 获取日期范围入库和出库
        $model = StockLine::leftJoin('stock', 'stock.id', '=', 'stock_line.stock_id')
        ->leftJoin('stock_type', 'stock_type.id', '=', 'stock.type_id')
        ->leftJoin('product', 'product.id', '=', 'stock.type_id')
        ->whereBetween('stock.date', $dates)
        ->where('stock.status', 1)
        ->selectRaw('
            stock.type_id,
            stock_type.type,
            sum(stock.total_quantity) as total_quantity,
            sum(stock.pay_money) as total_money,
            sum(stock_line.quantity * stock_line.price) as sales_money
        ')
        ->groupBy('stock.type_id');
        foreach ($search['where'] as $where) {
            if ($where['active']) {
                if ($where['field'] == 'stock.store_id') {
                    $model->search($where);
                }
            }
        }
        $count = $model->get();
        $type_list = $count->keyBy('type_id');

        // 计算当前库存金额和数量
        $model = Stock::leftJoin('stock_type', 'stock_type.id', '=', 'stock.type_id')
        ->where('stock.status', 1)
        ->selectRaw('
            stock.type_id,
            stock_type.type,
            sum(stock.total_quantity) as total_quantity,
            sum(stock.pay_money) as total_money
        ')
        ->groupBy('stock_type.type');
        foreach ($search['where'] as $where) {
            if ($where['active']) {
                if ($where['field'] == 'stock.store_id') {
                    $model->search($where);
                }
            }
        }
        $rows = $model->get();

        $total_quantity = $rows->where('type', 1)->sum('total_quantity') - $rows->where('type', 2)->sum('total_quantity');
        $total_money = $rows->where('type', 1)->sum('total_money') - $rows->where('type', 2)->sum('total_money');

        // 本月和上月销售额

        $types = DB::table('stock_type')->get();

        return $this->display([
            'search'         => $search,
            'columns'        => $columns,
            'count'          => $count,
            'types'          => $types,
            'type_list'      => $type_list,
            'total_quantity' => $total_quantity,
            'total_money'    => $total_money,
        ]);
    }

    // 库存列表
    public function indexAction()
    {
        $warehouses = DB::table('warehouse')->get(['id', 'name as text']);
        $stores = DB::table('store')->get(['id', 'name as text']);

        $columns = [[
            'name'     => 'product_name',
            'index'    => 'product.name',
            'search'   => 'text',
            'label'    => '商品',
            'width' => 160,
            'align'    => 'center',
        ],[
            'name'    => 'product_spec',
            'index'   => 'product.spec',
            'search'   => 'text',
            'label'   => '规格',
            'width'   => 120,
            'align'   => 'center',
        ],[
            'name'    => 'product_barcode',
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
            'name'    => 'stock_quantity',
            'index'   => 'product.stock_quantity',
            'label'   => '库存',
            'width'   => 80,
            'align'   => 'right',
        ],[
            'name'    => 'virtual_quantity',
            'index'   => 'stock_warehouse.virtual_quantity',
            'label'   => '虚拟库存',
            'width'   => 80,
            'align'   => 'right',
        ],[
            'name'    => 'stock_cost',
            'index'   => 'stock_warehouse.stock_cost',
            'label'   => '成本',
            'width'   => 80,
            'align'   => 'right',
        ],[
            'name'    => 'product_price',
            'index'   => 'product.price',
            'label'   => '售价',
            'width'   => 80,
            'align'   => 'right',
        ],[
            'name'    => 'stock_min',
            'index'   => 'stock_warehouse.stock_min',
            'label'   => '最小库存',
            'width'   => 80,
            'align'   => 'right',
        ],[
            'name'    => 'stock_max',
            'index'   => 'stock_warehouse.stock_max',
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

        if (Input::ajax()) {
            $model = StockWarehouse::leftJoin('product', 'product.id', '=', 'stock_warehouse.product_id')
            ->leftJoin('product_category', 'product_category.id', '=', 'product.category_id')
            ->leftJoin('warehouse', 'warehouse.id', '=', 'stock_warehouse.warehouse_id')
            ->leftJoin('store', 'store.id', '=', 'warehouse.store_id')
            ->select([
                'stock_warehouse.*',
                'product.name as product_name',
                'product.spec as product_spec',
                'product.price as product_price',
                'product.barcode as product_barcode',
                'store.name as store_name',
                'warehouse.name as warehouse_name',
                'product_category.name as category_name',
            ])
            ->where('product.status', 1)
            ->where('product.type', 1)
            ->orderBy('warehouse.sort', 'asc')
            ->orderBy('product_category.sort', 'asc');

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

    // 库存预警
    public function warningAction()
    {
        $warehouses = DB::table('warehouse')->get(['id', 'name as text']);
        $stores     = DB::table('store')->get(['id', 'name as text']);

        $columns = [[
            'name'     => 'product_name',
            'index'    => 'product.name',
            'search'   => 'text',
            'label'    => '商品',
            'width' => 160,
            'align'    => 'center',
        ],[
            'name'    => 'product_spec',
            'index'   => 'product.spec',
            'search'   => 'text',
            'label'   => '规格',
            'width'   => 120,
            'align'   => 'center',
        ],[
            'name'    => 'product_barcode',
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
            'name'    => 'stock_quantity',
            'index'   => 'product.stock_quantity',
            'label'   => '当前库存',
            'width'   => 80,
            'align'   => 'right',
        ],[
            'name'    => 'stock_min',
            'index'   => 'stock_warehouse.stock_min',
            'label'   => '最小库存',
            'width'   => 80,
            'align'   => 'right',
        ],[
            'name'    => 'stock_max',
            'index'   => 'stock_warehouse.stock_max',
            'label'   => '最大库存',
            'width'   => 80,
            'align'   => 'right',
        ],[
            'name'    => 'stock_limit',
            'index'   => 'stock_warehouse.stock_limit',
            'label'   => '超限数量',
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

        if (Input::ajax()) {
            $model = StockWarehouse::leftJoin('product', 'product.id', '=', 'stock_warehouse.product_id')
            ->leftJoin('product_category', 'product_category.id', '=', 'product.category_id')
            ->leftJoin('warehouse', 'warehouse.id', '=', 'stock_warehouse.warehouse_id')
            ->leftJoin('store', 'store.id', '=', 'warehouse.store_id')
            ->selectRaw('
                stock_warehouse.*,
                product.name as product_name,
                product.spec as product_spec,
                product.price as product_price,
                product.barcode as product_barcode,
                store.name as store_name,
                warehouse.name as warehouse_name,
                product_category.name as category_name
            ')
            ->where('product.status', 1)
            ->where('product.type', 1)
            ->orderBy('warehouse.sort', 'asc')
            ->orderBy('product_category.sort', 'asc');

            foreach ($search['where'] as $where) {
                if ($where['active']) {
                    $model->search($where);
                }
            }
            $rows = $model->paginate($search['limit']);
            $rows->transform(function ($row) {
                $quantity = $row['stock_quantity'];
                if ($quantity > 0) {
                    $quantity = $quantity - $row['stock_max'];
                } else {
                    $quantity = $quantity - $row['stock_min'];
                }
                $row['stock_limit'] = $quantity;
                return $row;
            });
            return response()->json($rows);
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
            'name'      => 'cost_quantity1',
            'index'     => 'stock_line.cost_quantity1',
            'formatter' => 'integer',
            'label'     => '入库数量',
            'width'     => 120,
            'align'     => 'right',
        ],[
            'name'      => 'cost_price1',
            'index'     => 'stock_line.cost_price1',
            'formatter' => 'number',
            'label'     => '入库成本',
            'width'     => 120,
            'align'     => 'right',
        ],[
            'name'      => 'cost_money1',
            'index'     => 'stock_line.cost_money1',
            'formatter' => 'number',
            'label'     => '入库金额',
            'width'     => 120,
            'align'     => 'right',
        ],[
            'name'      => 'cost_quantity2',
            'index'     => 'stock_line.quantity',
            'formatter' => 'integer',
            'label'     => '出库数量',
            'width'     => 120,
            'align'     => 'right',
        ],[
            'name'      => 'cost_price2',
            'index'     => 'stock_line.cost_price2',
            'formatter' => 'number',
            'label'     => '出库成本',
            'width'     => 120,
            'align'     => 'right',
        ],[
            'name'      => 'cost_money2',
            'index'     => 'stock_line.cost_money2',
            'formatter' => 'number',
            'label'     => '出库金额',
            'width'     => 120,
            'align'     => 'right',
        ]];

        $search_columns = search_columns($columns);
        $search = search_form([
            'referer'  => 1,
            'advanced' => 1,
        ], $search_columns);

        if (Input::ajax()) {
            $model = StockLine::leftJoin('product', 'product.id', '=', 'stock_line.product_id')
            ->leftJoin('stock', 'stock.id', '=', 'stock_line.stock_id')
            ->leftJoin('stock_type', 'stock_type.id', '=', 'stock.type_id')
            ->leftJoin('warehouse', 'warehouse.id', '=', 'stock_line.warehouse_id')
            ->leftJoin('store', 'store.id', '=', 'stock.store_id')
            ->select([
                'product.*',
                'stock_line.quantity as cost_quantity',
                'stock_line.cost_price',
                'stock_line.cost_money',
                'store.name as store_name',
                'warehouse.name as warehouse_name',
                'stock.date',
                'stock.sn',
                'stock_type.name as type_name',
                'stock_type.type as stock_type',
                'stock_line.id',
            ])
            ->where('stock.status', 1)
            ->where('product.status', 1)
            ->where('product.type', 1)
            ->orderBy('stock_line.id', 'desc');

            foreach ($search['where'] as $where) {
                if ($where['active']) {
                    $model->search($where);
                }
            }

            $rows = $model->paginate($search['limit']);
            $rows->transform(function ($row) {
                $type     = $row['stock_type'];
                $quantity = $row['cost_quantity'];
                $price    = $row['cost_price'];
                $money    = $row['cost_money'];
                $row['cost_quantity'.$type] = $quantity;
                $row['cost_price'.$type] = $quantity;
                $row['cost_money'.$type] = $quantity;
                return $row;
            });
            return response()->json($rows);
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
        $row = StockWarehouse::where('id', $gets['id'])->first();

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
    public function costEditAction()
    {
        $gets = Input::get();
        $row = StockWarehouse::where('id', $gets['id'])->first();

        if (Request::method() == 'POST') {
            if ($row->id) {
                $row->stock_cost = $gets['stock_cost'];
                $row->save();
                return $this->json('恭喜你，成本修改成功。', true);
            } else {
                return $this->json('很抱歉，仓库不存在。');
            }
        }

        return $this->render([
            'row' => $row
        ]);
    }
}
