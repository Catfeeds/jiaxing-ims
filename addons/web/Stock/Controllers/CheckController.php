<?php namespace Aike\Web\Stock\Controllers;

use DB;
use Input;
use Request;
use Validator;

use Aike\Web\Stock\Stock;
use Aike\Web\Stock\StockLine;
use Aike\Web\Stock\ProductCategory;
use Aike\Web\Stock\StockWarehouse;

use Aike\Web\Index\Controllers\DefaultController;

class CheckController extends DefaultController
{
    public $permission = ['store'];

    // 首页
    public function homeAction()
    {
        $stores = DB::table('store')->get(['id', 'name as text']);
        $columns = [[
            'name'     => 'store_id',
            'index'    => 'store.id',
            'label'    => '所属门店',
            'width'    => 100,
            'search'   => [
                'type' => 'select',
                'data' => $stores,
            ],
            'align'    => 'left',
        ]];

        $search_columns = search_columns($columns);
        $search = search_form([
            'referer'  => 1,
            'advanced' => 1,
        ], $search_columns);

        $query = $search['query'];

        // 本月金额
        $model = Stock::whereIn('type_id', [8, 9])
        ->where('status', 1)
        ->whereRaw("to_days(date) = to_days(now())");
        if ($query['store_id']) {
            $model->where('stock.store_id', $query['store_id']);
        }
        $day = $model->sum('total_money');

        // 本年金额
        $model = Stock::whereIn('type_id', [8, 9])
        ->where('status', 1)
        ->whereRaw("DATE_FORMAT(date,'%Y%m') = DATE_FORMAT(CURDATE(),'%Y%m')");
        if ($query['store_id']) {
            $model->where('stock.store_id', $query['store_id']);
        }
        $month = $model->sum('total_money');

        // 全部金额
        $model = Stock::whereIn('type_id', [8, 9])
        ->where('status', 1);
        if ($query['store_id']) {
            $model->where('stock.store_id', $query['store_id']);
        }
        $all = $model->sum('total_money');

        return $this->display([
            'columns' => $columns,
            'search'  => $search,
            'day'     => $day,
            'month'   => $month,
            'all'     => $all,
        ]);
    }

    // 领料出库列表
    public function indexAction()
    {
        $stores = DB::table('store')->get(['id', 'name as text']);

        $columns = [[
            'name'     => 'sn',
            'index'    => 'stock.sn',
            'search'   => 'text',
            'label'    => '单号',
            'width' => 160,
            'align'    => 'center',
        ],[
            'name'    => 'total_quantity',
            'index'   => 'stock.total_quantity',
            'label'   => '数量',
            'width'   => 80,
            'align'   => 'right',
        ],[
            'name'    => 'pay_money',
            'index'   => 'stock.pay_money',
            'label'   => '金额',
            'width'   => 80,
            'align'   => 'right',
        ],[
            'name'    => 'date',
            'index'   => 'stock.date',
            'label'   => '退货日期',
            'search'  => 'date2',
            'width'   => 120,
            'align'   => 'center',
        ],[
            'name'    => 'created_at',
            'index'   => 'stock.created_at',
            'label'   => '创建时间',
            'width'   => 140,
            'formatter' => 'date',
            'formatoptions' => [
                'srcformat' => 'u',
                'newformat' => 'Y-m-d H:i'
            ],
            'align' => 'center',
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
            'name'    => 'user_name',
            'index'   => 'user.id',
            'search'   => [
                'type' => 'select',
                'url' => 'user/user/dialog',
            ],
            'label'   => '退货员',
            'width'   => 120,
            'align'   => 'center',
        ],[
            'name'    => 'remark',
            'index'   => 'stock.remark',
            'label'   => '备注',
            'minWidth'   => 140,
            'align'   => 'left',
        ],[
            'name'  => 'id',
            'index' => 'stock.id',
            'label' => 'ID',
            'width' => 60,
            'align' => 'center',
        ],[
            'name'  => 'action',
            'formatter' => 'actionLink',
            'formatoptions' => [
                'show'        => '明细',
                'invalidEdit' => '作废'
            ],
            'label' => ' ',
            'width' => 100,
            'align' => 'center',
        ]];

        $search_columns = search_columns($columns);
        $search = search_form([
            'referer'  => 1,
            'advanced' => 1,
        ], $search_columns);

        if (Input::ajax()) {
            $model = Stock::orderBy('stock.id', 'desc')
            ->leftJoin('user', 'user.id', '=', 'stock.user_id')
            ->leftJoin('store', 'store.id', '=', 'stock.store_id')
            ->select(['stock.*', 'store.name as store_name', 'user.name as user_name'])
            ->where('stock.status', 1)
            ->whereIn('stock.type_id', [8, 9]);

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

    // 采购入库明细列表
    public function lineAction()
    {
        $stores = DB::table('store')->get(['id', 'name as text']);

        $columns = [[
            'name'     => 'sn',
            'index'    => 'stock.sn',
            'search'   => 'text',
            'label'    => '单号',
            'width'    => 160,
            'align'    => 'center',
        ],[
            'name'    => 'product_barcode',
            'index'   => 'product.barcode',
            'label'   => '条码',
            'width'   => 140,
            'align'   => 'center',
        ],[
            'name'    => 'product_name',
            'index'   => 'product.name',
            'label'   => '商品名称',
            'width'   => 220,
            'align'   => 'left',
        ],[
            'name'    => 'product_spec',
            'index'   => 'product.spec',
            'label'   => '规格',
            'width'   => 140,
            'align'   => 'center',
        ],[
            'name'    => 'quantity',
            'index'   => 'stock_line.quantity',
            'label'   => '数量',
            'width'   => 80,
            'align'   => 'right',
        ],[
            'name'    => 'cost_price',
            'index'   => 'stock_line.cost_price',
            'label'   => '成本',
            'width'   => 80,
            'align'   => 'right',
        ],[
            'name'    => 'cost_money',
            'index'   => 'stock_line.cost_money',
            'label'   => '金额',
            'width'   => 80,
            'align'   => 'right',
        ],[
            'name'    => 'warehouse_name',
            'index'   => 'warehouse.name',
            'label'   => '退货仓库',
            'width'   => 120,
            'align'   => 'center',
        ],[
            'name'    => 'user_name',
            'index'   => 'user.id',
            'label'   => '退货员',
            'width'    => 120,
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
            'name'    => 'date',
            'index'   => 'stock.date',
            'search'  => 'date2',
            'label'   => '退货日期',
            'width'   => 100,
            'formatter' => 'date',
            'formatoptions' => [
                'srcformat' => 'Y-m-d',
                'newformat' => 'Y-m-d'
            ],
            'align' => 'center',
        ],[
            'name'    => 'remark',
            'index'   => 'stock_line.remark',
            'label'   => '备注',
            'minWidth'   => 140,
            'align'   => 'left',
        ],[
            'name'  => 'id',
            'index' => 'stock.id',
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
            $model = StockLine::orderBy('stock_line.id', 'desc')
            ->leftJoin('stock', 'stock.id', '=', 'stock_line.stock_id')
            ->leftJoin('product', 'product.id', '=', 'stock_line.product_id')
            ->leftJoin('warehouse', 'warehouse.id', '=', 'stock_line.warehouse_id')
            ->leftJoin('store', 'store.id', '=', 'stock.store_id')
            ->leftJoin('user', 'user.id', '=', 'stock.user_id')
            ->where('stock.status', 1)
            ->whereIn('stock.type_id', [8, 9])
            ->select([
                'stock_line.*',
                'store.name as store_name',
                'stock.sn',
                'stock.date',
                'warehouse.name as warehouse_name',
                'product.barcode as product_barcode',
                'product.name as product_name',
                'product.spec as product_spec',
                'user.name as user_name'
            ]);

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

    // 采购入库单列表
    public function invalidAction()
    {
        $stores = DB::table('store')->get(['id', 'name as text']);

        $columns = [[
            'name'     => 'sn',
            'index'    => 'stock.sn',
            'search'   => 'text',
            'label'    => '单号',
            'width' => 160,
            'align'    => 'center',
        ],[
            'name'    => 'total_quantity',
            'index'   => 'stock.total_quantity',
            'label'   => '数量',
            'width'   => 80,
            'align'   => 'right',
        ],[
            'name'    => 'pay_money',
            'index'   => 'stock.pay_money',
            'label'   => '金额',
            'width'   => 80,
            'align'   => 'right',
        ],[
            'name'    => 'date',
            'index'   => 'stock.date',
            'label'   => '退货日期',
            'search'  => 'date2',
            'width'   => 120,
            'align'   => 'center',
        ],[
            'name'    => 'invalid_at',
            'index'   => 'stock.invalid_at',
            'label'   => '作废时间',
            'width'   => 140,
            'formatter' => 'date',
            'formatoptions' => [
                'srcformat' => 'u',
                'newformat' => 'Y-m-d H:i'
            ],
            'align' => 'center',
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
            'name'    => 'user_name',
            'index'   => 'user.id',
            'search'   => [
                'type' => 'select',
                'url' => 'user/user/dialog',
            ],
            'label'   => '退货员',
            'width'   => 120,
            'align'   => 'center',
        ],[
            'name'    => 'invalid_remark',
            'index'   => 'stock.invalid_remark',
            'label'   => '作废备注',
            'minWidth'   => 140,
            'align'   => 'left',
        ],[
            'name'  => 'id',
            'index' => 'stock.id',
            'label' => 'ID',
            'width' => 60,
            'align' => 'center',
        ],[
            'name'  => 'action',
            'formatter' => 'actionLink',
            'formatoptions' => [
                'show' => '明细',
            ],
            'label' => ' ',
            'width' => 80,
            'align' => 'center',
        ]];

        $search_columns = search_columns($columns);
        $search = search_form([
            'referer'  => 1,
            'advanced' => 1,
        ], $search_columns);

        if (Input::ajax()) {
            $model = Stock::orderBy('stock.id', 'desc')
            ->leftJoin('user', 'user.id', '=', 'stock.user_id')
            ->leftJoin('store', 'store.id', '=', 'stock.store_id')
            ->select(['stock.*', 'store.name as store_name', 'user.name as user_name'])
            ->where('stock.status', 0)
            ->whereIn('stock.type_id', [8, 9]);

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

    // 库存盘点单
    public function createAction()
    {
        $id = (int)Input::get('id');
        
        if (Request::method() == 'POST') {
            $gets = Input::get();

            $model = StockWarehouse::orderBy('stock_warehouse.id', 'desc')
            ->leftJoin('product', 'product.id', '=', 'stock_warehouse.product_id')
            ->leftJoin('product_category', 'product_category.id', '=', 'product.category_id')
            ->leftJoin('warehouse', 'warehouse.id', '=', 'stock_warehouse.warehouse_id')
            ->select([
                'stock_warehouse.*',
                'stock_warehouse.stock_cost as cost_price',
                'warehouse.name as warehouse_name',
                'product_category.name as category_name',
                'product.barcode as product_barcode',
                'product.name as product_name',
                'product.unit as product_unit',
                'product.spec as product_spec'
            ]);
            
            if ($gets['warehouse_id'] > 0) {
                $model->where('warehouse.id', $gets['warehouse_id']);
            }

            if ($gets['category_id'] > 0) {
                $model->where('product_category.id', $gets['category_id']);
            }

            return $this->json($model->get(), true);
        }

        $columns = [
            ['name' => "id", 'hidden' => true],
            ['name' => "product_id", 'label' => '商品ID', 'hidden' => true],
            ['name' => "warehouse_id", 'label' => '仓库ID', 'hidden' => true],
            ['name' => "product_name", 'width' => 220, 'label' => '商品名称', 'rules' => ['required'=>true], 'sortable' => false],
            ['name' => "product_spec", 'width' => 120, 'label' => '商品规格', 'align' => 'center', 'sortable' => false],
            ['name' => "category_name", 'width' => 120, 'label' => '商品类别', 'align' => 'center', 'sortable' => false],
            ['name' => "product_unit", 'width' => 80, 'label' => '单位', 'align' => 'center', 'sortable' => false],
            ['name' => "warehouse_name", 'width' => 120, 'label' => '仓库', 'align' => 'center', 'sortable' => false],
            ['name' => "cost_price", 'width' => 80, 'label' => '成本', 'align' => 'right', 'formatter' => 'number', 'sortable' => false],
            ['name' => "stock_quantity", 'width' => 100, 'label' => '现存数量', 'align' => 'right', 'formatter' => 'integer'],
            ['name' => "quantity", 'width' => 100, 'label' => '盘点数量', 'align' => 'right', 'formatter' => 'number', 'formatoptions' => ['decimalPlaces' => 1], 'rules' => ['required'=>true, 'number' => true], 'sortable' => false, 'editable' => true],
            ['name' => "check", 'label' => '盘点盈亏', 'width' => 100, 'sortable' => false, 'align' => 'right'],
        ];

        foreach ($columns as &$column) {
            if (isset($column['rules'])) {
                if ($column['rules']['required'] == true) {
                    $column['label'] = '<span class="red"> * </span>'.$column['label'];
                }
            }
        }

        $categorys  = ProductCategory::where('type', 1)->orderBy('lft', 'asc')->get()->toNested();
        $warehouses = DB::table('warehouse')->orderBy('sort', 'asc')->get();
        return $this->display([
            'columns'    => $columns,
            'categorys'  => $categorys,
            'warehouses' => $warehouses,
        ]);
    }

    // 保存盘点
    public function storeAction()
    {
        $id = (int)Input::get('id');
        
        if (Request::method() == 'POST') {
            $gets = Input::get();
            $model = Stock::findOrNew($gets['id']);
            $rules = [
                'type_id' => 'required',
                'user_id' => 'required',
                'date'    => 'required',
            ];
            $v = Validator::make($gets, $rules);
            if ($v->fails()) {
                return $this->json($v->errors()->all());
            }

            $gets['pay_money'] = $gets['total_money'];
            $gets['store_id'] = auth()->user()->store_id;
            $model->fill($gets)->save();

            $purchase_line = $gets['stock_line'];
            foreach ($purchase_line as $line) {
                $line['id']       = 0;
                $line['stock_id'] = $model->id;
                StockLine::insert($line);
            }

            $text = $gets['type_id'] == 8 ? '盘盈' : '盘亏';

            // 重建存货数据
            Stock::rebuildStock($model);

            return $this->json('恭喜你，库存'.$text.'成功。', true);
        }

        $row = Stock::where('id', $id)->first();

        $columns = [
            ['name' => "id", 'hidden' => true],
            ['name' => 'op', 'label' => '&nbsp;', 'formatter' => 'options', 'width' => 60, 'sortable' => false, 'align' => 'center'],
            ['name' => "product_id", 'label' => '商品ID', 'hidden' => true],
            ['name' => "warehouse_id", 'label' => '仓库ID', 'hidden' => true],
            ['name' => "product_name", 'width' => 220, 'label' => '商品名称', 'rules' => ['required'=>true], 'sortable' => false, 'editable' => true],
            ['name' => "product_spec", 'width' => 120, 'label' => '商品规格', 'align' => 'center', 'sortable' => false],
            ['name' => "category_name", 'width' => 120, 'label' => '商品类别', 'align' => 'center', 'sortable' => false],
            ['name' => "warehouse_name", 'width' => 120, 'label' => '仓库', 'align' => 'center', 'sortable' => false],
            ['name' => "stock_quantity", 'width' => 80, 'label' => '库存', 'align' => 'right', 'formatter' => 'integer'],
            ['name' => "product_unit", 'width' => 80, 'label' => '单位', 'align' => 'center', 'sortable' => false],
            ['name' => "cost_price", 'width' => 80, 'label' => '成本', 'align' => 'right', 'formatter' => 'number', 'sortable' => false],
            ['name' => "quantity", 'width' => 80, 'label' => '数量', 'align' => 'right', 'formatter' => 'number', 'formatoptions' => ['decimalPlaces' => 1], 'rules' => ['required'=>true, 'number' => true, 'minValue' => '0.1'], 'sortable' => false, 'editable' => true],
            ['name' => "cost_money", 'label' => '金额', 'width' => 100, 'formatter' => 'number', 'sortable' => false, 'align' => 'right'],
            ['name' => "remark", 'label' => '备注', 'width' => 200, 'sortable' => false, 'editable' => true],
        ];

        foreach ($columns as &$column) {
            if (isset($column['rules'])) {
                if ($column['rules']['required'] == true) {
                    $column['label'] = '<span class="red"> * </span>'.$column['label'];
                }
            }
        }

        $categorys  = ProductCategory::where('type', 1)->orderBy('lft', 'asc')->get()->toNested();
        $warehouses = DB::table('warehouse')->orderBy('sort', 'asc')->get();

        $type_id = (int)Input::get('type_id');
        return $this->render([
            'row'        => $row,
            'columns'    => $columns,
            'categorys'  => $categorys,
            'warehouses' => $warehouses,
            'type_id'    => $type_id,
        ]);
    }

    // 显示采购入库单据
    public function showAction()
    {
        $gets = Input::get();
        $row = Stock::where('id', $gets['id'])->first();

        $lines = StockLine::orderBy('stock_line.id', 'desc')
        ->leftJoin('product', 'product.id', '=', 'stock_line.product_id')
        ->leftJoin('stock', 'stock.id', '=', 'stock_line.stock_id')
        ->leftJoin('warehouse', 'warehouse.id', '=', 'stock_line.warehouse_id')
        ->leftJoin('store', 'store.id', '=', 'stock.store_id')
        ->where('stock.id', $gets['id'])
        ->select([
            'stock_line.*',
            'stock.sn',
            'stock.date',
            'store.name as store_name',
            'warehouse.name as warehouse_name',
            'product.barcode as product_barcode',
            'product.name as product_name',
            'product.spec as product_spec'
        ])->get();

        return $this->render([
            'row'   => $row,
            'lines' => $lines,
            'trash' => $gets['trash'],
        ]);
    }

    // 作废单据
    public function invalidEditAction()
    {
        $gets = Input::get();
        $row = Stock::where('id', $gets['id'])->first();

        if (Request::method() == 'POST') {
            if ($row->id) {
                // 入库类型废除时检查存货数量
                Stock::checkStock($row);
                
                $row->status = 0;
                $row->invalid_at = time();
                $row->invalid_remark = $gets['remark'];
                $row->save();

                // 重建存货数据
                Stock::rebuildStock($row);

                return $this->json('恭喜你，库存盘点作废成功。', true);
            } else {
                return $this->json('很抱歉，库存盘点不存在。');
            }
        }

        return $this->render([
            'row' => $row
        ]);
    }

    // 删除采购单据
    public function deleteAction()
    {
        if (Request::method() == 'POST') {
            $id = (array)Input::get('id');
            if (empty($id)) {
                return $this->json('最少选择一行记录。');
            }
            Stock::whereIn('id', $id)->delete();
            StockLine::whereIn('stock_id', $id)->delete();
            return $this->json('恭喜你，库存盘点删除成功。', true);
        }
    }
}
