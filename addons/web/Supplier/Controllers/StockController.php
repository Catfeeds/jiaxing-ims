<?php namespace Aike\Web\Supplier\Controllers;

use DB;
use Input;
use Illuminate\Http\Request;
use Validator;
use Auth;

use Aike\Web\User\User;

use Aike\Web\Product\StockType;

use Aike\Web\Supplier\Warehouse;
use Aike\Web\Supplier\Supplier;
use Aike\Web\Supplier\Plan;
use Aike\Web\Supplier\PlanData;
use Aike\Web\Supplier\Order;
use Aike\Web\Supplier\OrderData;
use Aike\Web\Supplier\Stock;
use Aike\Web\Supplier\StockData;
use Aike\Web\Supplier\Product;
use Aike\Web\Supplier\ProductCategory;
use Aike\Web\Index\Controllers\DefaultController;

class StockController extends DefaultController
{
    public function indexAction()
    {
        $search = search_form([
            'referer' => 1,
        ], [
            ['text','supplier_stock.number','单号'],
            ['text','user.nickname','供应商'],
            ['second','supplier_stock.created_at','创建时间'],
        ]);

        $query  = $search['query'];

        $model = Stock::with('datas', 'supplier.user')
        ->leftJoin('supplier', 'supplier.id', '=', 'supplier_stock.supplier_id')
        ->leftJoin('user', 'user.id', '=', 'supplier.user_id')
        ->orderBy('supplier_stock.id', 'desc')
        ->select(['supplier_stock.*']);

        if (authorise() == 1) {
            $supplier = Supplier::where('user_id', Auth::id())->first();
            $model->where('supplier_id', $supplier->id);
        }

        foreach ($search['where'] as $where) {
            if ($where['active']) {
                $model->search($where);
            }
        }

        $rows = $model->paginate()->appends($query);

        return $this->display([
            'rows'   => $rows,
            'search' => $search,
        ]);
    }

    // 入库显示
    public function showAction(Request $request)
    {
        $id     = $request->input('id');
        $stock  = Stock::with('datas')->find($id);
        $_order = Order::with('datas')->find($stock->order_id);
        $order = [];
        foreach ($_order->datas as $data) {
            $order[$data->product_id] += $data->quantity;
        }

        $categorys = ProductCategory::type('supplier')->orderBy('lft', 'asc')->get()->toNested();
        $products = Product::type('supplier')->get(['product.*'])->KeyBy('id');

        return $this->display([
            'stock'     => $stock,
            'order'     => $order,
            'categorys' => $categorys,
            'products'  => $products,
        ]);
    }

    // 新建入库
    public function createAction(Request $request)
    {
        $order_id = $request->input('order_id');

        if ($request->method() == 'POST') {
            $datas = $request->input('datas');

            $rules = $messages = $product = $plan_data_ids = [];
            
            $quantity = 0;

            foreach ($datas as $i => $data) {
                if ($data['quantity']) {
                    if ($data['plan_status']) {
                        $plan_data_ids[] = $data['plan_data_id'];
                    }

                    $quantity += $data['quantity'];
                    $product[] = $data['product_id'];
                    $rules['datas.'.$i.'.quantity'] = 'required|numeric';
                    $messages['datas.'.$i.'.quantity.required'] = '['.$i.']入库数量必须填写。';
                    $messages['datas.'.$i.'.quantity.numeric']  = '['.$i.']入库数量必须是数字。';
                } else {
                    unset($datas[$i]);
                }
            }

            $v = Validator::make($request->all(), $rules, $messages);
            if ($v->fails()) {
                return $this->back()->withErrors($v)->withInput();
            }

            // 检查送货数量
            $lists = Stock::where('order_id', $order_id)->pluck('id');

            // 检查发货数量
            $order_sum = OrderData::where('order_id', $order_id)->whereIn('product_id', $product)->sum('quantity');
            $stock_sum = StockData::whereIn('stock_id', $lists)->whereIn('product_id', $product)->sum('quantity');

            if (($order_sum * 1.1) < $stock_sum + $quantity) {
                return $this->error('很抱歉，入库数量大于10%送货数量。');
            }

            $order = Order::find($order_id);
            if ($order['stock_at'] == 0) {
                // 写入第一次入库日期
                $data['stock_at'] = strtotime(date('Y-m-d'));
                $order->fill($data)->save();
            }

            // 订单编号+入库次数+1
            $_stock['number'] = $order->number .'-'. (count($lists) + 1);

            $_stock['supplier_id'] = $request->input('supplier_id');
            $_stock['order_id'] = $order_id;

            $stock = new Stock;
            $stock->fill($_stock)->save();

            foreach ($datas as $row) {
                $data = new StockData;
                $data->stock_id = $stock->id;
                $data->fill($row)->save();
            }

            // 更新周期订单子表送货状态
            if ($plan_data_ids) {
                PlanData::whereIn('id', $plan_data_ids)->update(['status' => 1]);
            }

            return $this->success('supplier/order/show', ['id'=>$order_id], '恭喜您，入库单生成成功。');
        }

        $order = Order::with('datas')->find($order_id);
        $categorys = ProductCategory::type('supplier')->orderBy('lft', 'asc')->get()->toNested();
        $products = Product::type('supplier')->get(['product.*'])->KeyBy('id');

        return $this->display([
            'order_id'  => $order_id,
            'order'     => $order,
            'query'     => $query,
            'categorys' => $categorys,
            'products'  => $products,
            'status'    => Order::$tabs,
        ]);
    }

    // 库存汇总
    public function reportAction(Request $request)
    {
        $query = [
            'sdate'        => date('Y-m-01'),
            'edate'        => date('Y-m-d'),
            'warehouse_id' => 0,
            'category_id'  => 0,
            'product_id'   => 0
        ];
        foreach ($query as $k => $v) {
            $query[$k] = Input::get($k, $v);
        }

        $model = DB::table('product as p')
        ->LeftJoin('product_category as pc', 'p.category_id', '=', 'pc.id')
        ->LeftJoin('warehouse', 'p.warehouse_id', '=', 'warehouse.id')
        //->whereRaw('p.status=1')
        ->where('pc.type', 2)
        ->orderBy('pc.lft', 'asc')
        ->orderBy('p.sort', 'asc');
        
        // 选择产品类别
        if ($query['category_id'] > 0) {
            $category = DB::table('product_category')->where('id', $query['category_id'])->first();
            $model->whereBetween('pc.lft', [$category['lft'], $category['rgt']]);
        }

        // 选择仓库
        if ($query['warehouse_id'] > 0) {
            $warehouse = DB::table('warehouse')->where('id', $query['warehouse_id'])->first();
            $model->whereBetween('warehouse.lft', [$warehouse['lft'], $warehouse['rgt']]);
        }

        // 获取产品列表
        $rows = $model->get(['p.*', 'pc.name as category_name']);

        // 历史结存
        $yonyou_history = DB::table('stock_yonyou_data')
        ->where('date', '<', $query['sdate'])
        ->selectRaw('code,sum(quantity_set - quantity_get) as quantity')
        ->groupBy('code')
        ->pluck('quantity', 'code');

        // 用友当前月份数据
        $yonyou_data = DB::table('stock_yonyou_data')
        ->whereBetween('date', [$query['sdate'], $query['edate']])
        ->selectRaw('flag,code,sum(quantity_set) as quantity_a,sum(quantity_get) as quantity_b, sum(quantity_set - quantity_get) as quantity')
        ->groupBy('code')
        ->get();
        $yonyou_data = array_by($yonyou_data, 'code');

        // 读取产品类别
        $categorys = ProductCategory::type('supplier')->orderBy('lft', 'asc')->get()->toNested();
        $types = StockType::orderBy('lft', 'asc')->get()->toNested();
        $warehouse = Warehouse::type('supplier')->orderBy('lft', 'asc')->get()->toNested();

        return $this->display(array(
            'yonyou_history' => $yonyou_history,
            'yonyou_data'    => $yonyou_data,
            'categorys'      => $categorys,
            'warehouse'      => $warehouse,
            'types'          => $types,
            'query'          => $query,
            'rows'           => $rows,
        ));
    }

    // 删除入库
    public function deleteAction(Request $request)
    {
        if ($request->method() == 'POST') {
            $id = $request->input('id');
            $id = array_filter((array)$id);

            if (empty($id)) {
                return $this->error('请先选择数据。');
            }

            Stock::whereIn('id', $id)->delete();
            StockData::whereIn('stock_id', $id)->delete();

            return $this->success('index', '恭喜你，操作成功。');
        }
    }
}
