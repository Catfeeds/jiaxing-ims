<?php namespace Aike\Web\Stock\Controllers;

use DB;
use Input;
use Request;
use Validator;

use Aike\Web\Stock\Purchase;
use Aike\Web\Stock\PurchaseLine;

use Aike\Web\Index\Controllers\DefaultController;

class PurchaseController extends DefaultController
{
    // 向导
    public function guideAction()
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

        return $this->display([
            'columns' => $columns,
            'search'  => $search
        ]);
    }

    // 采购入库单列表
    public function indexAction()
    {
        $suppliers = DB::table('supplier')->get(['id', 'name as text']);
        $stores    = DB::table('store')->get(['id', 'name as text']);

        $columns = [[
            'name'     => 'sn',
            'index'    => 'stock_purchase.sn',
            'search'   => 'text',
            'label'    => '单号',
            'width' => 160,
            'align'    => 'center',
        ],[
            'name'    => 'quantity',
            'index'   => 'stock_purchase.quantity',
            'label'   => '数量',
            'width'   => 80,
            'align'   => 'right',
        ],[
            'name'    => 'rec_money',
            'index'   => 'stock_purchase.rec_money',
            'label'   => '应收',
            'width'   => 80,
            'align'   => 'right',
        ],[
            'name'    => 'discount_money',
            'index'   => 'stock_purchase.discount_money',
            'label'   => '折扣',
            'width'   => 80,
            'align'   => 'right',
        ],[
            'name'    => 'pay_money',
            'index'   => 'stock_purchase.pay_money',
            'label'   => '付款',
            'width'   => 80,
            'align'   => 'right',
        ],[
            'name'    => 'arear_money',
            'index'   => 'stock_purchase.arear_money',
            'label'   => '欠款',
            'width'   => 80,
            'align'   => 'right',
        ],[
            'name'    => 'buyer_name',
            'index'   => 'buyer.name',
            'label'   => '采购员',
            'width'   => 100,
            'align'   => 'center',
        ],[
            'name'    => 'supplier_name',
            'index'   => 'supplier.id',
            'label'   => '供应商',
            'search'   => [
                'type' => 'select',
                'data' => $suppliers,
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
            'name'    => 'date',
            'index'   => 'stock_purchase.date',
            'search'  => 'date2',
            'label'   => '采购日期',
            'width'   => 100,
            'formatter' => 'date',
            'formatoptions' => [
                'srcformat' => 'Y-m-d',
                'newformat' => 'Y-m-d'
            ],
            'align' => 'center',
        ],[
            'name'     => 'arear_money',
            'index'    => 'stock_purchase.arear_money',
            'label'    => '欠款状态',
            'hidden'   => true,
            'width'    => 100,
            'search'   => [
                'type' => 'select',
                'data' => [['id' => 1, 'text' => '有'],['id' => 0, 'text' => '无']],
            ],
            'formatter' => 'status',
            'align'     => 'center',
        ],[
            'name'    => 'created_at',
            'index'   => 'stock_purchase.created_at',
            'label'   => '创建时间',
            'width'   => 140,
            'formatter' => 'date',
            'formatoptions' => [
                'srcformat' => 'u',
                'newformat' => 'Y-m-d H:i'
            ],
            'align' => 'center',
        ],[
            'name'    => 'remark',
            'index'   => 'stock_purchase.remark',
            'label'   => '备注',
            'minWidth'   => 140,
            'align'   => 'left',
        ],[
            'name'  => 'id',
            'index' => 'stock_purchase.id',
            'label' => 'ID',
            'width' => 60,
            'align' => 'center',
        ]];

        $search_columns = search_columns($columns);
        $search = search_form([
            'referer'  => 1,
            'advanced' => 1,
        ], $search_columns);

        $model = Purchase::orderBy('stock_purchase.id', 'desc')
        ->leftJoin('user as buyer', 'buyer.id', '=', 'stock_purchase.buyer')
        ->leftJoin('supplier', 'supplier.id', '=', 'stock_purchase.supplier_id')
        ->leftJoin('store', 'store.id', '=', 'stock_purchase.store_id')
        ->select(['stock_purchase.*', 'store.name as store_name', 'buyer.name as buyer_name', 'supplier.name as supplier_name'])
        ->where('stock_purchase.status', 1);

        foreach ($search['where'] as $where) {
            if ($where['active']) {
                if ($where['field'] == 'stock_purchase.arear_money') {
                    if ($where['search'] == 0) {
                        $model->where($where['field'], 0);
                    } else {
                        $model->where($where['field'], '>', 0);
                    }
                } else {
                    $model->search($where);
                }
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

    // 采购入库明细列表
    public function lineAction()
    {
        $suppliers = DB::table('supplier')->get(['id', 'name as text']);
        $stores    = DB::table('store')->get(['id', 'name as text']);

        $columns = [[
            'name'     => 'sn',
            'index'    => 'stock_purchase.sn',
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
            'index'   => 'stock_purchase_line.quantity',
            'label'   => '数量',
            'width'   => 80,
            'align'   => 'right',
        ],[
            'name'    => 'price',
            'index'   => 'stock_purchase_line.price',
            'label'   => '进价',
            'width'   => 80,
            'align'   => 'right',
        ],[
            'name'    => 'money',
            'index'   => 'stock_purchase_line.money',
            'label'   => '金额',
            'width'   => 80,
            'align'   => 'right',
        ],[
            'name'    => 'warehouse_name',
            'index'   => 'warehouse.name',
            'label'   => '采购仓库',
            'width'   => 120,
            'align'   => 'center',
        ],[
            'name'    => 'supplier_name',
            'index'   => 'supplier.id',
            'label'   => '供应商',
            'search'   => [
                'type' => 'select',
                'data' => $suppliers,
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
            'name'    => 'date',
            'index'   => 'stock_purchase.date',
            'search'  => 'date2',
            'label'   => '采购日期',
            'width'   => 100,
            'formatter' => 'date',
            'formatoptions' => [
                'srcformat' => 'Y-m-d',
                'newformat' => 'Y-m-d'
            ],
            'align' => 'center',
        ],[
            'name'    => 'remark',
            'index'   => 'stock_purchase_line.remark',
            'label'   => '备注',
            'minWidth'   => 140,
            'align'   => 'left',
        ],[
            'name'  => 'id',
            'index' => 'stock_purchase.id',
            'label' => 'ID',
            'width' => 60,
            'align' => 'center',
        ]];

        $search_columns = search_columns($columns);
        $search = search_form([
            'referer'  => 1,
            'advanced' => 1,
        ], $search_columns);

        $model = PurchaseLine::orderBy('stock_purchase_line.id', 'desc')
        ->leftJoin('product', 'product.id', '=', 'stock_purchase_line.product_id')
        ->leftJoin('stock_purchase', 'stock_purchase.id', '=', 'stock_purchase_line.purchase_id')
        ->leftJoin('supplier', 'supplier.id', '=', 'stock_purchase.supplier_id')
        ->leftJoin('warehouse', 'warehouse.id', '=', 'stock_purchase_line.warehouse_id')
        ->leftJoin('store', 'store.id', '=', 'stock_purchase.store_id')
        ->select([
            'stock_purchase_line.*',
            'store.name as store_name',
            'stock_purchase.sn',
            'stock_purchase.date',
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

        if (Input::ajax()) {
            return response()->json($model->paginate($search['limit']));
        }

        return $this->display(array(
            'search'  => $search,
            'columns' => $columns,
        ));
    }

    // 采购入库单列表
    public function trashAction()
    {
        $suppliers = DB::table('supplier')->get(['id', 'name as text']);
        $stores    = DB::table('store')->get(['id', 'name as text']);

        $columns = [[
            'name'     => 'sn',
            'index'    => 'stock_purchase.sn',
            'search'   => 'text',
            'label'    => '单号',
            'width' => 160,
            'align'    => 'center',
        ],[
            'name'    => 'quantity',
            'index'   => 'stock_purchase.quantity',
            'label'   => '数量',
            'width'   => 80,
            'align'   => 'right',
        ],[
            'name'    => 'rec_money',
            'index'   => 'stock_purchase.rec_money',
            'label'   => '应收',
            'width'   => 80,
            'align'   => 'right',
        ],[
            'name'    => 'discount_money',
            'index'   => 'stock_purchase.discount_money',
            'label'   => '折扣',
            'width'   => 80,
            'align'   => 'right',
        ],[
            'name'    => 'pay_money',
            'index'   => 'stock_purchase.pay_money',
            'label'   => '付款',
            'width'   => 80,
            'align'   => 'right',
        ],[
            'name'    => 'arear_money',
            'index'   => 'stock_purchase.arear_money',
            'label'   => '欠款',
            'width'   => 80,
            'align'   => 'right',
        ],[
            'name'    => 'buyer_name',
            'index'   => 'buyer.name',
            'label'   => '采购员',
            'width'   => 100,
            'align'   => 'center',
        ],[
            'name'    => 'supplier_name',
            'index'   => 'supplier.id',
            'label'   => '供应商',
            'search'   => [
                'type' => 'select',
                'data' => $suppliers,
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
            'name'    => 'date',
            'index'   => 'stock_purchase.date',
            'search'  => 'date2',
            'label'   => '采购日期',
            'width'   => 100,
            'formatter' => 'date',
            'formatoptions' => [
                'srcformat' => 'Y-m-d',
                'newformat' => 'Y-m-d'
            ],
            'align' => 'center',
        ],[
            'name'    => 'created_at',
            'index'   => 'stock_purchase.created_at',
            'label'   => '创建时间',
            'width'   => 140,
            'formatter' => 'date',
            'formatoptions' => [
                'srcformat' => 'u',
                'newformat' => 'Y-m-d H:i'
            ],
            'align' => 'center',
        ],[
            'name'    => 'invalid_at',
            'index'   => 'stock_purchase.invalid_at',
            'label'   => '作废时间',
            'width'   => 140,
            'formatter' => 'date',
            'formatoptions' => [
                'srcformat' => 'u',
                'newformat' => 'Y-m-d H:i'
            ],
            'align' => 'center',
        ],[
            'name'    => 'remark',
            'index'   => 'stock_purchase.remark',
            'label'   => '作废备注',
            'minWidth'   => 140,
            'align'   => 'left',
        ],[
            'name'  => 'id',
            'index' => 'stock_purchase.id',
            'label' => 'ID',
            'width' => 60,
            'align' => 'center',
        ]];

        $search_columns = search_columns($columns);
        $search = search_form([
            'referer'  => 1,
            'advanced' => 1,
        ], $search_columns);

        $model = Purchase::orderBy('stock_purchase.id', 'desc')
        ->leftJoin('user as buyer', 'buyer.id', '=', 'stock_purchase.buyer')
        ->leftJoin('supplier', 'supplier.id', '=', 'stock_purchase.supplier_id')
        ->leftJoin('store', 'store.id', '=', 'stock_purchase.store_id')
        ->select(['stock_purchase.*', 'store.name as store_name', 'buyer.name as buyer_name', 'supplier.name as supplier_name'])
        ->where('stock_purchase.status', 0);

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

    // 新建采购入库
    public function createAction()
    {
        $id = (int)Input::get('id');
        
        if (Request::method() == 'POST') {
            $gets = Input::get();

            $model = Purchase::findOrNew($gets['id']);
            $rules = [
                'supplier_id'    => 'required',
                'date'           => 'required',
                'buyer'          => 'required',
                'discount_money' => 'required',
                'pay_money'      => 'required',
            ];
            $v = Validator::make($gets, $rules);
            if ($v->fails()) {
                return $this->json($v->errors()->all());
            }
            $gets['store_id'] = auth()->user()->store_id;
            $model->fill($gets)->save();

            $purchase_line = $gets['purchase_line'];
            foreach ($purchase_line as $line) {
                $line['purchase_id'] = $model->id;
                PurchaseLine::insert($line);
            }

            return $this->json('恭喜你，采购入库更新成功。', true);
        }

        $row = Purchase::where('id', $id)->first();

        $columns = [
            ['name' => "id", 'hidden' => true],
            ['name' => 'op', 'label' => '&nbsp;', 'formatter' => 'options', 'width' => 60, 'sortable' => false, 'align' => 'center'],
            ['name' => "product_id", 'label' => '商品ID', 'hidden' => true],
            ['name' => "warehouse_id", 'label' => '仓库ID', 'hidden' => true],
            ['name' => "product_name", 'width' => 280, 'label' => '商品名称', 'rules' => ['required'=>true], 'sortable' => false, 'editable' => true],
            ['name' => "product_spec", 'width' => 140, 'label' => '商品规格', 'align' => 'center', 'sortable' => false],
            ['name' => "category_name", 'width' => 140, 'label' => '商品类别', 'align' => 'center', 'sortable' => false],
            ['name' => "warehouse_name", 'width' => 140, 'label' => '仓库', 'align' => 'center', 'rules' => ['required'=>true], 'sortable' => false, 'editable' => true],
            ['name' => "last_price", 'width' => 80, 'label' => '上次进价', 'align' => 'right', 'formatter' => 'number', 'sortable' => false],
            ['name' => "product_code", 'width' => 100, 'label' => '商品编码', 'sortable' => false],
            ['name' => "quantity", 'width' => 80, 'label' => '数量', 'align' => 'right', 'rules' => ['required'=>true], 'sortable' => false, 'editable' => true],
            ['name' => "unit", 'width' => 80, 'label' => '单位', 'sortable' => false],
            ['name' => "price", 'width' => 80, 'label' => '采购单价', 'formatter' => 'number', 'align' => 'right', 'rules' => ['required'=>true], 'sortable' => false, 'editable' => true],
            ['name' => "money", 'label' => '采购金额', 'width' => 100, 'formatter' => 'number', 'sortable' => false, 'align' => 'right'],
            ['name' => "description", 'label' => '备注', 'width' => 200, 'sortable' => false, 'editable' => true],
        ];

        foreach ($columns as &$column) {
            if (isset($column['rules'])) {
                if ($column['rules']['required'] == true) {
                    $column['label'] = '<span class="red"> * </span>'.$column['label'];
                }
            }
        }
        return $this->display([
            'row'     => $row,
            'columns' => $columns,
        ], 'create');
    }

    // 删除仓库
    public function deleteAction()
    {
        if (Request::method() == 'POST') {
            $id = (array)Input::get('id');
            if (empty($id)) {
                return $this->json('最少选择一行记录。');
            }
            Warehouse::whereIn('id', $id)->delete();
            return $this->json('恭喜你，仓库删除成功。', true);
        }
    }
}
