<?php namespace Aike\Web\Supplier\Controllers;

use DB;
use Input;
use Illuminate\Http\Request;
use Validator;
use Auth;

use Aike\Web\User\User;
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

class OrderController extends DefaultController
{
    public $permission = ['stock', 'print'];
    
    public function indexAction()
    {
        $search = search_form([
            'referer' => 1,
            'status'  => ''
        ], [
            ['text','supplier_order.number','单号'],
            ['text','user.nickname','供应商'],
            ['second','supplier_order.created_at','创建时间'],
        ]);

        $query = $search['query'];

        $model = Order::stepAt()->with('datas', 'supplier.user')
        ->leftJoin('supplier', 'supplier.id', '=', 'supplier_order.supplier_id')
        ->leftJoin('user', 'user.id', '=', 'supplier.user_id')
        ->orderBy('supplier_order.id', 'desc')
        ->select(['supplier_order.*']);

        if (is_numeric($query['status'])) {
            $model->where('supplier_order.status', $query['status']);
        }

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

        $tabs = [
            'name'  => 'status',
            'items' => Order::$tabs
        ];

        return $this->display([
            'rows'   => $rows,
            'search' => $search,
            'tabs'   => $tabs,
            'status' => array_by(Order::$tabs),
        ]);
    }

    // 送货显示
    public function showAction(Request $request)
    {
        $id = $request->input('id');
        $order = Order::stepAt()->with('datas')->find($id);

        $planDataIds = [];
        foreach ($order->datas as $data) {
            $planDataIds[] = $data->plan_data_id;
        }

        $_plan_data = PlanData::leftJoin('supplier_plan', 'supplier_plan.id', '=', 'supplier_plan_data.plan_id')
        ->whereIn('supplier_plan_data.id', $planDataIds)->get(['supplier_plan_data.*', 'supplier_plan.number']);

        $planIds = $planDataIds = [];
        foreach ($_plan_data as $data) {
            $product_id = $data->product_id;
            $plan['quantity'][$product_id] += $data->quantity;
            $plan['sn'][$product_id]        = $data->number;
            $plan['id'][$product_id]        = $data->plan_id;
        }

        //$orderIds = Order::whereIn('plan_id', $planIds)->pluck('id');
        $_stocks = Stock::with('datas')->where('order_id', $id)->get();
        $stock   = [];
        foreach ($_stocks as $_stock) {
            foreach ($_stock->datas as $data) {
                $stock[$data->product_id] += $data->quantity;
            }
        }

        $categorys = ProductCategory::type('supplier')->orderBy('lft', 'asc')->get()->toNested();
        $products = Product::type('supplier')->get(['product.*'])->KeyBy('id');
        
        $step = get_step_status($order);

        return $this->display([
            'order'     => $order,
            'stock'     => $stock,
            'plan'      => $plan,
            'categorys' => $categorys,
            'products'  => $products,
            'step'      => $step,
        ]);
    }

    // 超时送货单
    public function overtimeAction(Request $request)
    {
        $search = search_form([
            'referer' => 1,
            'status'  => ''
        ], [
            ['text','supplier_order.number','单号'],
            ['text','user.nickname','供应商'],
            ['second','supplier_order.created_at','创建时间'],
        ]);

        $query = $search['query'];

        $model = Order::stepAt()->with('datas', 'supplier.user')->leftJoin('supplier', 'supplier.id', '=', 'supplier_order.supplier_id')
        ->leftJoin('user', 'user.id', '=', 'supplier.user_id')
        ->whereRaw("supplier_order.delivery_at > 0 and IF(supplier_order.stock_at > 0, supplier_order.delivery_at - supplier_order.stock_at, supplier_order.delivery_at - unix_timestamp(date_format(now(),'%y-%m-%d'))) < 0")
        ->orderBy('supplier_order.id', 'desc')
        ->select(['supplier_order.*']);

        if (is_numeric($query['status'])) {
            $model->where('supplier_order.status', $query['status']);
        }

        foreach ($search['where'] as $where) {
            if ($where['active']) {
                $model->search($where);
            }
        }

        $rows = $model->paginate()->appends($query);

        $tabs = [
            'name'  => 'status',
            'items' => Order::$tabs
        ];

        return $this->display([
            'rows'   => $rows,
            'search' => $search,
            'tabs'   => $tabs,
            'status' => array_by(Order::$tabs),
        ]);
    }

    // 改变状态
    public function statusAction(Request $request)
    {
        if ($request->method() == 'POST') {
            $id = $request->input('id');
            $id = array_filter((array)$id);

            if (empty($id)) {
                return $this->error('请先选择数据。');
            }

            $orders = Order::whereIn('id', $id)->get();
            foreach ($orders as $order) {
                $orders->status = $order->status == 1 ? 0 : 1;
                $orders->save();
            }
            return $this->success('index', '恭喜你，操作成功。');
        }
    }

    // 送货打印
    public function printAction(Request $request)
    {
        $id = $request->input('id');
        $order = Order::stepAt()->with('datas')->find($id);

        $planDataIds = [];
        foreach ($order->datas as $data) {
            $planDataIds[] = $data->plan_data_id;
        }

        $_plan_data = PlanData::leftJoin('supplier_plan', 'supplier_plan.id', '=', 'supplier_plan_data.plan_id')
        ->whereIn('supplier_plan_data.id', $planDataIds)->get(['supplier_plan_data.*', 'supplier_plan.number']);

        $planIds = $planDataIds = [];
        foreach ($_plan_data as $data) {
            $product_id = $data->product_id;
            $plan['quantity'][$product_id] += $data->quantity;
            $plan['sn'][$product_id]        = $data->number;
            $plan['id'][$product_id]        = $data->plan_id;
            $planIds[]                      = $data->plan_id;
        }

        $orderIds = Order::whereIn('plan_id', $planIds)->pluck('id');
        $_stocks = Stock::with('datas')->whereIn('order_id', $orderIds)->get();
        $stock   = [];
        foreach ($_stocks as $_stock) {
            foreach ($_stock->datas as $data) {
                $stock[$data->product_id] += $data->quantity;
            }
        }

        $categorys = ProductCategory::type('supplier')->orderBy('lft', 'asc')->get()->toNested();
        $products = Product::type('supplier')->get(['product.*'])->KeyBy('id');
        
        $this->layout = 'layouts.print';
        return $this->display([
            'order'     => $order,
            'stock'     => $stock,
            'categorys' => $categorys,
            'products'  => $products,
        ]);
    }

    // 入库列表
    public function stockAction(Request $request)
    {
        $id         = $request->input('id');
        $product_id = $request->input('product_id');

        $lists = Stock::where('order_id', $id)->pluck('id');
        $stock = StockData::whereIn('stock_id', $lists)
        ->where('product_id', $product_id)
        ->get();

        $categorys = ProductCategory::type('supplier')->orderBy('lft', 'asc')->get()->toNested();
        $products = Product::type('supplier')->get(['product.*'])->KeyBy('id');

        return $this->render([
            'stock'     => $stock,
            'categorys' => $categorys,
            'products'  => $products,
        ]);
    }

    // 新建送货
    public function createAction(Request $request)
    {
        if ($request->method() == 'POST') {
            $datas = $request->input('datas');

            $products = $rules = $rows = $plan_data_ids = [];

            foreach ($datas as $data) {
                $supplier_id = $data['supplier_id'];

                $products[$supplier_id]['supplier_id'] = $supplier_id;
                $products[$supplier_id]['number']      = $data['plan_sn'];
                $products[$supplier_id]['plan_id']     = $data['plan_id'];
                $products[$supplier_id]['delivery_at'] = strtotime($data['delivery_date']);
                $rows[$supplier_id][]                  = $data;

                /*
                if($data['plan_status']) {
                    $plan_data_ids[] = $data['plan_data_id'];
                }
                */
            }

            $rules['datas.*.supplier_id']   = 'required';
            $rules['datas.*.plan_id']       = 'required';
            $rules['datas.*.product_id']    = 'required';
            $rules['datas.*.quantity']      = 'required|numeric|min:1';
            $rules['datas.*.delivery_date'] = 'required|date';

            $attributes = [
                'quantity'      => '送货数量',
                'delivery_date' => '送货日期',
                'product_id'    => '商品',
                'plan_id'       => '周期订单',
                'supplier_id'   => '供应商',
            ];

            $v = Validator::make($request->all(), $rules, [], $attributes);
            if ($v->fails()) {
                return join('<br>', $v->errors()->all());
            }
            
            foreach ($products as $supplier_id => $_order) {
                $order = new Order;
                $order->fill($_order)->save();
                
                foreach ($rows[$supplier_id] as $row) {
                    $data = new OrderData;
                    $data->order_id = $order->id;
                    $data->fill($row)->save();
                }
            }

            /*
            // 更新周期订单子表送货状态
            if($plan_data_ids) {
                PlanData::whereIn('id', $plan_data_ids)->update(['status' => 1]);
            }
            */

            session()->flash('message', '送货订单生成成功。');
            $referer = url_referer('index');
            return $this->json($referer, true);
        }

        $models = [
            ['name' => "id", 'hidden' => true],
            ['name' => 'op', 'label' => '&nbsp;', 'formatter' => 'options', 'width' => 60, 'sortable' => false, 'align' => 'center'],
            ['name' => "supplier_id", 'label' => '供应商ID', 'hidden' => true],
            ['name' => "product_id", 'label' => '商品ID', 'hidden' => true],
            ['name' => "plan_id", 'label' => '周期订单ID', 'hidden' => true],
            ['name' => "plan_sn", 'label' => '周期订单号', 'hidden' => true],
            ['name' => "plan_data_id", 'label' => '周期订单子表ID', 'hidden' => true],
            ['name' => "product_name", 'width' => 280, 'label' => '商品', 'rules' => ['required'=>true], 'sortable' => false, 'editable' => true],
            ['name' => "plan_name", 'width' => 320, 'label' => '周期订单', 'rules' => ['required'=>true], 'sortable' => false, 'editable' => true],
            ['name' => "plan_quantity", 'label' => '周期订单数量', 'width' => 120, 'formatter' => 'integer', 'sortable' => false, 'align' => 'right'],
            ['name' => "quantity", 'label' => '送货数量', 'width' => 140, 'rules' => ['required' => true, 'minValue' => 1, 'integer' => true], 'formatter' => 'integer', 'sortable' => false, 'editable' => true, 'align' => 'right'],
            ['name' => "delivery_date", 'label' => '送货日期', 'width' => 140, 'rules' => ['required' => true], 'formatter' => 'date', 'sortable' => false, 'editable' => true, 'align' => 'center'],
            //['name' => "plan_status", 'label' => '周期订单状态', 'width' => 140, 'rules' => ['required' => true], 'formatter' => 'dropdown', 'sortable' => false, 'editable' => true, 'align' => 'center'],
            ['name' => "description", 'label' => '备注', 'width' => 200, 'sortable' => false, 'editable' => true],
        ];

        return $this->display([
            'query'  => $query,
            'models' => $models,
        ]);
    }

    // 订单统计
    public function countAction(Request $request)
    {
        $search = search_form([
            'advanced' => '',
            'referer'  => 1,
        ], [
            ['text','user.nickname','供应商名称'],
        ]);

        $model = Supplier::with('plans.datas', 'orders.datas', 'stocks.datas')
        ->leftJoin('user', 'user.id', '=', 'supplier.user_id')
        ->where('user.status', 1);

        foreach ($search['where'] as $where) {
            if ($where['active']) {
                $model->search($where);
            }
        }

        $suppliers = $model->get(['supplier.*']);
        
        return $this->display([
            'suppliers' => $suppliers,
            'search'    => $search,
        ]);
    }


    // 订单统计明细
    public function count_showAction(Request $request)
    {
        $search = search_form([
            'advanced'    => '',
            'referer'     => 1,
            'supplier_id' => 0,
        ], [
            ['goods','product_id','商品'],
        ]);

        $query = $search['query'];
        $product_id = $query['search_0'];

        // 获取单个供应商和供应的商品
        $supplier = Supplier::with('products')
        ->leftJoin('user', 'user.id', '=', 'supplier.user_id')
        ->where('supplier.id', $query['supplier_id'])
        ->where('user.status', 1)
        ->first(['supplier.*']);

        // 获取单个供应商全部周期计划
        $plans = Plan::with(['datas' => function ($q) use ($product_id) {
            if ($product_id) {
                $q->where('product_id', $product_id);
            }
        }])->where('supplier_id', $query['supplier_id'])->get();

        // 获取单个供应商全部订单
        $orders = Order::with(['datas' => function ($q) use ($product_id) {
            if ($product_id) {
                $q->where('product_id', $product_id);
            }
        }])->where('supplier_id', $query['supplier_id'])->get();

        $stocks = Stock::with(['datas' => function ($q) use ($product_id) {
            if ($product_id) {
                $q->where('product_id', $product_id);
            }
        }])->where('supplier_id', $query['supplier_id'])->get();

        $plan_sum = $order_sum = $stock_sum = [];

        $years = [];

        foreach ($plans as $plan) {
            $y = date('Y', $plan->created_at);
            $m = date('n', $plan->created_at);
            $years[$y] = $y;
            foreach ($plan->datas as $data) {
                $plan_sum['data'][$data->product_id][$y][$m] += $data->quantity;
                $plan_sum['total'][$y][$m] += $data->quantity;
            }
        }

        foreach ($orders as $order) {
            $y = date('Y', $order->created_at);
            $m = date('n', $order->created_at);
            foreach ($order->datas as $data) {
                $order_sum['data'][$data->product_id][$y][$m] += $data->quantity;
                $order_sum['total'][$y][$m] += $data->quantity;
            }
        }
        foreach ($stocks as $stock) {
            $y = date('Y', $stock->created_at);
            $m = date('n', $stock->created_at);
            foreach ($stock->datas as $data) {
                $stock_sum['data'][$data->product_id][$y][$m] += $data->quantity;
                $stock_sum['total'][$y][$m] += $data->quantity;
            }
        }

        $products[] = ['id' => '', 'name' => ' - '];
        foreach ($supplier->products as $product) {
            $products[$product->id] = $product;
        }
        
        rsort($years);

        $rows = $query['search_0'] > 0 ? [$product_id => $products[$query['search_0']]] : $products;

        return $this->display([
            'years'     => $years,
            'supplier'  => $supplier,
            'plan_sum'  => $plan_sum,
            'order_sum' => $order_sum,
            'stock_sum' => $stock_sum,
            'products'  => $products,
            'search'    => $search,
            'rows'      => $rows,
        ]);
    }

    // 删除订单
    public function deleteAction(Request $request)
    {
        if ($request->method() == 'POST') {
            $id = $request->input('id');
            $id = array_filter((array)$id);

            if (empty($id)) {
                return $this->error('请先选择数据。');
            }

            /*
            $count = Stock::whereIn('order_id', $id)->count();
            if($count) {
                return $this->error('请先删除相关入库单。');
            }
            */

            Order::whereIn('id', $id)->delete();
            OrderData::whereIn('order_id', $id)->delete();

            // 删除入库
            $stockIds = Stock::whereIn('order_id', $id)->pluck('id');
            Stock::whereIn('id', $stockIds)->delete();
            StockData::whereIn('stock_id', $stockIds)->delete();

            return $this->success('index', '恭喜你，操作成功。');
        }
    }
}
