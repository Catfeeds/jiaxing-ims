<?php namespace Aike\Web\Supplier\Controllers;

use Illuminate\Http\Request;
use Validator;
use Auth;
use DB;

use Aike\Web\Supplier\Supplier;
use Aike\Web\Supplier\Plan;
use Aike\Web\Supplier\PlanData;
use Aike\Web\Supplier\Order;
use Aike\Web\Supplier\OrderData;
use Aike\Web\Supplier\Stock;
use Aike\Web\Supplier\StockData;
use Aike\Web\Supplier\Product;
use Aike\Web\Supplier\ProductCategory;

use Aike\Web\Model\Flow;
use Aike\Web\Index\Attachment;

use Aike\Web\Index\Controllers\DefaultController;

class PlanController extends DefaultController
{
    public $permission = ['order', 'print', 'dialog'];

    public function indexAction(Request $request)
    {
        $search = search_form([
            'referer' => 1,
            'status'  => 0
        ], [
            ['text','supplier_plan.number','单号'],
            ['text','user.nickname','供应商'],
            ['second','supplier_plan.created_at','创建时间'],
        ]);

        $query = $search['query'];

        $model = Plan::stepAt()->with('supplier.user', 'datas')
        ->leftJoin('supplier', 'supplier.id', '=', 'supplier_plan.supplier_id')
        ->leftJoin('user', 'user.id', '=', 'supplier.user_id')
        ->where('supplier_plan.status', $query['status'])
        ->orderBy('supplier_plan.id', 'desc')
        ->select(['supplier_plan.*']);

        if (authorise() == 1) {
            $supplier = Supplier::where('user_id', Auth::id())->first();
            $model->where('supplier_plan.supplier_id', $supplier->id);
        }

        foreach ($search['where'] as $where) {
            if ($where['active']) {
                $model->search($where);
            }
        }

        $tabs = [
            'name'  => 'status',
            'items' => Plan::$tabs
        ];

        $rows = $model->paginate()->appends($query);
        return $this->display([
            'rows'   => $rows,
            'search' => $search,
            'tabs'   => $tabs,
            'status' => array_by(Plan::$tabs),
        ]);
    }

    // 显示订单
    public function showAction(Request $request)
    {
        $id = $request->input('id');
        $plan = Plan::stepAt()->with('datas')->find($id);

        $lists = Order::where('plan_id', $id)->pluck('id');
        $stocks = Stock::with('datas')->whereIn('order_id', $lists)->get();
        $stock  = [];
        foreach ($stocks as $_stock) {
            foreach ($_stock->datas as $data) {
                $stock[$data->product_id] += $data->quantity;
            }
        }

        $categorys = ProductCategory::type('supplier')->orderBy('lft', 'asc')->get()->toNested();
        $products = Product::type('supplier')->get(['product.*'])->KeyBy('id');

        $step = get_step_status($plan);

        // 附件
        $attach = Attachment::view($plan['attachment']);

        return $this->display([
            'plan'      => $plan,
            'stock'     => $stock,
            'categorys' => $categorys,
            'products'  => $products,
            'step'      => $step,
            'attach'    => $attach,
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

            $plans = Plan::whereIn('id', $id)->get();
            foreach ($plans as $plan) {
                $plan->status = $plan->status == 1 ? 0 : 1;
                $plan->save();
            }
            return $this->success('index', '恭喜你，操作成功。');
        }
    }

    // 打印计划订单
    public function printAction(Request $request)
    {
        $id = $request->input('id');
        $plan = Plan::with('datas', 'order.datas')->find($id);
        $order = [];
        foreach ($plan->order as $_order) {
            foreach ($_order->datas as $data) {
                $order[$data->product_id] += $data->quantity;
            }
        }

        $categorys = ProductCategory::type('supplier')->orderBy('lft', 'asc')->get()->toNested();
        $products = Product::type('supplier')->get(['product.*'])->KeyBy('id');
        $this->layout = 'layouts.print';
        return $this->display([
            'plan'      => $plan,
            'order'     => $order,
            'categorys' => $categorys,
            'products'  => $products,
        ]);
    }

    // 送货明细
    public function orderAction(Request $request)
    {
        $id         = $request->input('id');
        $product_id = $request->input('product_id');

        $lists = Order::where('plan_id', $id)->pluck('id');
        $order = OrderData::whereIn('order_id', $lists)
        ->where('product_id', $product_id)
        ->get();

        $categorys = ProductCategory::type('supplier')->orderBy('lft', 'asc')->get()->toNested();
        $products = Product::type('supplier')->get(['product.*'])->KeyBy('id');

        return $this->render([
            'order'     => $order,
            'categorys' => $categorys,
            'products'  => $products,
        ]);
    }

    // 新建订单
    public function createAction(Request $request)
    {
        if ($request->method() == 'POST') {
            $gets = $request->all();

            $rules = $messages = [];

            $rules['datas.*.product_id']  = 'required';
            $rules['datas.*.supplier_id'] = 'required';
            $rules['datas.*.quantity']    = 'required|numeric';

            $messages['datas.*.product_id.required']  = '商品必须选择。';
            $messages['datas.*.supplier_id.required'] = '商品没有指定供应商。';
            $messages['datas.*.quantity.required']    = '数量必须填写。';
            $messages['datas.*.quantity.numeric']     = '数量必须是数字。';

            $v = Validator::make($request->all(), $rules, $messages);
            if ($v->fails()) {
                return json_encode($v->errors()->all());
            }

            $datas = [];
            foreach ($gets['datas'] as $get) {
                $supplier_id = array_pull($get, 'supplier_id');
                if ($supplier_id) {
                    unset($get['product_text'], $get['category'], $get['spec']);
                    $datas[$supplier_id][] = $get;
                }
            }

            $attachment = join(',', (array)$gets['attachment']);

            foreach ($datas as $id => $_data) {
                $count  = Plan::count();
                $number = date('ymd-').$count;

                $_plan = [
                    'number'      => $number,
                    'supplier_id' => $id,
                ];

                $_plan['attachment'] = $attachment;

                $plan = new Plan;
                $plan->fill($_plan)->save();

                foreach ($_data as $row) {
                    $data = new PlanData;
                    $row['plan_id'] = $plan->id;
                    $data->fill($row)->save();
                }
            }

            // 附件发布
            Attachment::publish();

            $referer = url_referer("index");
            return $this->json($referer, true);
        }

        $id   = (int)$request->input('id');
        $plan = Plan::with('datas')->where('id', $id)->first();

        $models = [
            ['name' => "id", 'hidden' => true],
            ['name' => 'op', 'label' => '&nbsp;', 'formatter' => 'options', 'width' => 60, 'sortable' => false, 'align' => 'center'],
            ['name' => "product_id", 'label' => '商品ID', 'hidden' => true],
            ['name' => "supplier_id", 'label' => '供应商ID', 'hidden' => true],
            ['name' => "product_name", 'width' => 280, 'label' => '商品', 'rules' => ['required'=>true], 'sortable' => false, 'editable' => true],
            ['name' => "supplier_name", 'width' => 180, 'label' => '供应商', 'rules' => ['required'=>true], 'sortable' => false, 'editable' => true],
            ['name' => "quantity", 'label' => '订单数量', 'width' => 140, 'rules' => ['required' => true, 'minValue' => 1, 'integer' => true], 'formatter' => 'integer', 'sortable' => false, 'editable' => true, 'align' => 'right'],
            ['name' => "cycle", 'label' => '计划阶段', 'width' => 140, 'rules' => ['required'=>true], 'sortable' => false, 'editable' => true],
            ['name' => "description", 'label' => '备注', 'width' => 200, 'sortable' => false, 'editable' => true],
            ['name' => "month_1", 'label' => '上月', 'width' => 80, 'sortable' => false, 'align' => 'right'],
            ['name' => "month_2", 'label' => '本月', 'width' => 80, 'sortable' => false, 'align' => 'right'],
            ['name' => "month_3", 'label' => '下月', 'width' => 80, 'sortable' => false, 'align' => 'right'],
            ['name' => "month_4", 'label' => '上月', 'width' => 80, 'sortable' => false, 'align' => 'right'],
            ['name' => "month_5", 'label' => '本月', 'width' => 80, 'sortable' => false, 'align' => 'right'],
            ['name' => "month_6", 'label' => '目前包材库存量', 'width' => 100, 'sortable' => false, 'align' => 'right'],
            ['name' => "month_7", 'label' => '包材未交付订单量', 'width' => 100, 'sortable' => false, 'align' => 'right'],
            ['name' => "month_8", 'label' => '目前未生产订单', 'width' => 100, 'sortable' => false, 'align' => 'right'],
            ['name' => "month_9", 'label' => '本次订单数量', 'width' => 100, 'sortable' => false, 'align' => 'right'],
        ];

        $attach = Attachment::edit($plan->attachment);

        return $this->display([
            'plan'   => $plan,
            'query'  => $query,
            'models' => $models,
            'attach' => $attach,
        ]);
    }

    /**
     * 弹出商品列表
     */
    public function dialogAction(Request $request)
    {
        $gets = $request->all();

        $search = search_form([
            'advanced'  => '',
            'page'      => 1,
            'sort'      => '',
            'order'     => '',
            'limit'     => '',
        ], [
            ['text','product.name','订单号'],
            ['text','product.id','订单ID'],
            ['text','product.barcode','产品条码'],
            ['category','product.category_id','产品类别'],
        ]);
        
        $query = $search['query'];

        if ($request->method() == 'POST') {
            $model = DB::table('supplier_plan_data')
            ->leftJoin('supplier_plan', 'supplier_plan.id', '=', 'supplier_plan_data.plan_id')
            ->leftJoin('supplier', 'supplier_plan.supplier_id', '=', 'supplier.id')
            ->leftJoin('user', 'user.id', '=', 'supplier.user_id')
            ->leftJoin('product', 'product.id', '=', 'supplier_plan_data.product_id')
            ->leftJoin('supplier_order_data', 'supplier_order_data.plan_data_id', '=', 'supplier_plan_data.id')
            ->where('supplier_plan_data.product_id', $query['product_id'])
            ->where('supplier_plan_data.status', 0);

            // 排序方式
            if ($query['sort'] && $query['order']) {
                $model->orderBy($query['sort'], $query['order']);
            } else {
                $model->orderBy('supplier_plan.id', 'asc');
            }

            // 搜索条件
            foreach ($search['where'] as $where) {
                if ($where['active']) {
                    $model->search($where);
                }
            }

            $rows = $model->selectRaw("supplier_plan.supplier_id,supplier_plan.id,supplier_plan.number,supplier_plan_data.status,supplier_plan_data.id as plan_data_id,supplier_plan_data.quantity as plan_quantity,user.nickname,FROM_UNIXTIME(supplier_plan.created_at,'%Y-%m-%d') as created_date,product.name as product_name,concat(user.nickname,'(',FROM_UNIXTIME(supplier_plan.created_at,'%Y-%m-%d'),')') as text,product.id as product_id, if(product.spec='', product.name, concat(product.name,' - ', product.spec)) as product_text")
            ->paginate($query['limit']);

            $_quantity = DB::table('supplier_order_data')
            ->leftJoin('supplier_order', 'supplier_order.id', '=', 'supplier_order_data.order_id')
            ->where('supplier_order_data.product_id', $query['product_id'])
            ->where('supplier_order_data.status', 0)
            ->where('supplier_order_data.plan_data_id', '>', 0)
            ->selectRaw("sum(supplier_order_data.quantity) as order_quantity, supplier_order.plan_id")
            ->groupBy('plan_id')
            ->pluck('order_quantity', 'plan_id');

            $items = $rows->transform(function ($item) use ($_quantity) {
                $item['plan_quantity'] = $item['plan_quantity'] - (int)$_quantity[$item['id']];
                return $item;
            });

            $rows->setCollection($items);

            return response()->json($rows);
        }
        return $this->render(array(
            'search' => $search,
            'gets'   => $gets,
        ));
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

            $count = Order::whereIn('plan_id', $id)->count();
            if ($count) {
                return $this->error('请先删除相关送货单。');
            }

            Plan::whereIn('id', $id)->delete();
            PlanData::whereIn('plan_id', $id)->delete();

            return $this->success('index', '恭喜你，操作成功。');
        }
    }
}
