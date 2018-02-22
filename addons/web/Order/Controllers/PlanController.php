<?php namespace Aike\Web\Order\Controllers;

use DB;
use Input;
use Request;
use Auth;
use Cache;

use select;

use Aike\Web\Customer\Customer;
use Aike\Web\Supplier\Product;
use Aike\Web\Supplier\ProductCategory;
use Aike\Web\Supplier\Warehouse;
use Aike\Web\Order\Order;
use Aike\Web\Product\Stock;

use Aike\Web\Index\Controllers\DefaultController;

class PlanController extends DefaultController
{
    // 订单流控
    public function indexAction()
    {
        // 提交生产时间(新)
        if (Request::method() == 'POST') {
            $gets = Input::get();
            if ($gets['oper'] == 'edit') {
                $order = Order::find($gets['order_id']);
                $order->plan_time = strtotime($gets['value']);
                $order->save();
            }
            exit('true');
        }

        // 提交生产时间
        if ($post = $this->post()) {
            $order = Order::find($post['id']);
            $order->plan_time = strtotime($post['value']);
            $order->save();
            exit('true');
        }

        //审核模板
        $order = config('order');
        $audits = $order['audit'];

        $page_id      = Input::get('page', 1);
        $category_id  = Input::get('category_id', 0);
        $warehouse_id = Input::get('warehouse_id', 0);
        $tpl          = Input::get('tpl', 'index');

        $selects['select']['category_id']  = $category_id;
        $selects['select']['warehouse_id'] = $warehouse_id;
        $selects['select']['tpl']          = $tpl;

        // 模型实例
        $model = DB::table('order_data as oh')
        ->leftJoin('order as o', 'o.id', '=', 'oh.order_id')
        ->leftJoin('product as p', 'oh.product_id', '=', 'p.id')
        ->leftJoin('product_category as pc', 'pc.id', '=', 'p.category_id')
        ->leftJoin('warehouse as psw', 'psw.id', '=', 'p.warehouse_id')
        ->where('pc.type', 1)
        ->where('oh.deleted', 0)
        ->where('amount', '>', 0)
        ->where('o.delivery_time', '<=', 0)
        ->where('o.flow_step_id', '>', 1)
        ->where('o.status', 1)
        ->groupBy('oh.product_id')
        ->groupBy('oh.client_id')
        ->groupBy('o.id')
        ->orderBy('o.plan_time', 'ASC')
        ->orderBy('o.flow_step_id', 'DESC')
        ->orderBy('o.add_time', 'DESC')
        ->selectRaw('o.pay_time,o.delivery_time,o.arrival_time,o.number, o.plan_time, o.flow_step_id, oh.product_id, o.id, oh.client_id, SUM(oh.amount) as amount');

        if ($category_id > 0) {
            $q_category = DB::table('product_category')->where('id', $category_id)->first(['lft', 'rgt']);
            $model->whereRaw('pc.lft BETWEEN '.$q_category['lft'].' AND '.$q_category['rgt']);
        }

        if ($warehouse_id > 0) {
            $q_warehouse = DB::table('warehouse')->where('id', $warehouse_id)->first(['lft', 'rgt']);
            $model->whereRaw('psw.lft BETWEEN '.$q_warehouse['lft'].' AND '.$q_warehouse['rgt']);
        }

        $rs = $model->get();

        $productModel = DB::table('product as p')
        ->leftJoin('product_category as pc', 'pc.id', '=', 'p.category_id')
        ->leftJoin('warehouse as psw', 'psw.id', '=', 'p.warehouse_id')
        ->where('pc.type', 1)
        ->where('p.status', 1);

        if ($warehouse_id > 0) {
            $productModel->whereRaw('psw.lft BETWEEN '.$q_warehouse['lft'].' AND '.$q_warehouse['rgt']);
        }

        if ($category_id > 0) {
            $productModel->whereRaw('pc.lft BETWEEN '.$q_category['lft'].' AND '.$q_category['rgt']);
        }

        $_products = $productModel->orderBy('pc.lft', 'ASC')
        ->orderBy('p.sort', 'ASC')
        ->get(['p.*']);

        // 获取库存结存
        $stocks = Stock::gets();

        $products = array();
        foreach ($_products as $v) {
            $product_id = $v['id'];
            $products[$product_id] = $v;
            $inventory[$product_id] = $stocks[1][$product_id] - $stocks[2][$product_id];
        }

        $res = $single = $money = $moneyall = $plans = array();

        foreach ($rs as $key => $value) {
            $product_clients[$value['client_id']][$value['product_id']] += $value['amount'];

            $dealers['dealer'][$value['id']] = get_user($value['client_id'], 'nickname', false);

            $dealers['client'][$value['id']] = $value['client_id'];
            $dealers['plan'][$value['id']] = $value['plan_time'];

            $dealers['info'][$value['id']] = array(
                'number' => $value['number'],
                'flow_step_id'  => $value['flow_step_id'],
                'audit_name' => $audits[$value['flow_step_id']]['name'],
                'pay_time' => $value['pay_time'],
            );

            $dealers['amount'][$value['id']] += $value['amount'];

            $all['a'] += $value['amount'];
            if ($value['pay_time'] > 0) {
                $money[$value['product_id']] += $value['amount'];
                $all['b'] += $value['amount'];
            } else {
                $single[$value['product_id']] += $value['amount'];
                $all['c'] += $value['amount'];
            }
            if ($value['flow_step_id'] > 3) {
                $all['d'] += $value['amount'];
                $plans[$value['product_id']] += $value['amount'];
            }

            $moneyall[$value['product_id']] += $value['amount'];

            $res[$value['id']]['dealer'] = get_user($value['client_id'], 'nickname', false);
            $res[$value['id']]['code'][$value['product_id']] += $value['amount'];
        }
        unset($rs);

        $query = url().'?'.http_build_query($selects['select']);

        $categorys = ProductCategory::type('sale')->orderBy('lft', 'asc')->get()->toNested();
        $warehouses = Warehouse::type('sale')->orderBy('lft', 'asc')->get()->toNested();

        return $this->display(array(
            'inventory'=> $inventory,
            'res'      => $res,
            'json'     => $json,
            'warehouses' => $warehouses,
            'single'   => $single,
            'dealers'  => $dealers,
            'all'      => $all,
            'money'    => $money,
            'moneyall' => $moneyall,
            'plans'    => $plans,
            'audits'   => $audits,
            'products' => $products,
            'pagelink' => $pagelink,
            'selects'  => $selects,
            'query'    => $query,
            'categorys'=> $categorys,
            'product_clients' => $product_clients,
        ), $tpl);
    }

    // 发货流控
    public function deliverAction()
    {
        //审核模板
        $order = config('order');

        // 开始天
        $startDate = date('Y-m-d', strtotime("-2 Day"));
        // 结束天
        $endDate = date('Y-m-d', strtotime("+3 Day"));

        $sdate = Input::get('sdate', $startDate);
        $edate = Input::get('edate', $endDate);
        $category_id = Input::get('category_id', 0);
        $warehouse_id = Input::get('warehouse_id', 0);

        $selects['select']['sdate'] = $sdate;
        $selects['select']['edate'] = $edate;
        $selects['select']['category_id'] = $category_id;
        $selects['select']['warehouse_id'] = $warehouse_id;
        
        $model = DB::table('order_data as oi')
        ->leftJoin('order as o', 'o.id', '=', 'oi.order_id')
        ->leftJoin('product as p', 'oi.product_id', '=', 'p.id')
        ->leftJoin('product_category as pc', 'pc.id', '=', 'p.category_id')
        ->leftJoin('warehouse as psw', 'psw.id', '=', 'p.warehouse_id')
        ->leftJoin('user as c', 'c.id', '=', 'o.client_id')
        ->where('pc.type', 1)
        ->where('oi.deleted', 0)
        ->where('o.flow_step_id', '>', 1)
        ->where('o.delivery_time', '>', 0)
        ->groupBy('oi.product_id')
        ->groupBy('o.client_id')
        ->groupBy('date')
        ->selectRaw('*,SUM(fact_amount) as amount, FROM_UNIXTIME(o.delivery_time,"%Y-%m-%d") as date,o.client_id,c.nickname as customer_name');

        if ($category_id > 0) {
            $q_category = DB::table('product_category')->where('id', $category_id)->first(['lft', 'rgt']);
            $model->whereRaw('pc.lft BETWEEN '.$q_category['lft'].' AND '.$q_category['rgt']);
        }

        if ($warehouse_id > 0) {
            $q_warehouse = DB::table('warehouse')->where('id', $warehouse_id)->first(['lft', 'rgt']);
            $model->whereRaw('psw.lft BETWEEN '.$q_warehouse['lft'].' AND '.$q_warehouse['rgt']);
        }

        if ($sdate) {
            $model->whereRaw('FROM_UNIXTIME(o.delivery_time,"%Y-%m-%d") >= ?', [$sdate]);
        }
        if ($edate) {
            $model->whereRaw('FROM_UNIXTIME(o.delivery_time,"%Y-%m-%d") <= ?', [$edate]);
        }

        $res = $model->orderBy('date', 'ASC')
        ->get()->toArray();

        $delivery = array();
        foreach ($res as $v) {
            $delivery['total'] += $v['amount'];
            $delivery[$v['date']]['total'] += $v['amount'];
            $delivery[$v['product_id']]['total'] += $v['amount'];
            $delivery[$v['product_id']][$v['date']] += $v['amount'];

            $client[$v['product_id']][$v['date']][$v['client_id']] += $v['amount'];
            $client['name'][$v['client_id']] = $v['customer_name'];
        }
        
        $model = DB::table('stock_data as a')
        ->leftJoin('stock as b', 'b.id', '=', 'a.stock_id')
        ->leftJoin('stock_type as c', 'c.id', '=', 'b.type_id')
        ->leftJoin('product as p', 'a.product_id', '=', 'p.id')
        ->leftJoin('product_category as pc', 'pc.id', '=', 'p.category_id')
        ->leftJoin('warehouse as psw', 'psw.id', '=', 'p.warehouse_id')
        ->where('pc.type', 1)
        ->where('c.type', 1)
        ->groupBy('a.product_id')
        ->groupBy('date')
        ->selectRaw('*,SUM(a.amount) amount, FROM_UNIXTIME(a.add_time,"%Y-%m-%d") date');

        if ($category_id > 0) {
            $model->whereRaw('pc.lft BETWEEN '.$q_category['lft'].' AND '.$q_category['rgt']);
        }

        if ($warehouse_id > 0) {
            $model->whereRaw('psw.lft BETWEEN '.$q_warehouse['lft'].' AND '.$q_warehouse['rgt']);
        }

        if ($sdate) {
            $model->whereRaw('FROM_UNIXTIME(a.add_time,"%Y-%m-%d") >= ?', [$sdate]);
        }
        if ($edate) {
            $model->whereRaw('FROM_UNIXTIME(a.add_time,"%Y-%m-%d") <= ?', [$edate]);
        }

        $res = $model->orderBy('date', 'ASC')->get()->toArray();

        $data = array();
        foreach ($res as $v) {
            $data['total'] += $v['amount'];
            $data[$v['date']]['total'] += $v['amount'];
            $data[$v['product_id']]['total'] += $v['amount'];
            $data[$v['product_id']][$v['date']] += $v['amount'];
        }
        $res = $data;
        unset($data);
        
        $model = DB::table('product as p')
        ->leftJoin('product_category as pc', 'pc.id', '=', 'p.category_id')
        ->leftJoin('warehouse as psw', 'psw.id', '=', 'p.warehouse_id')
        ->where('pc.type', 1)
        ->where('p.status', 1);

        if ($category_id > 0) {
            $model->whereRaw('pc.lft BETWEEN '.$q_category['lft'].' AND '.$q_category['rgt']);
        }
        if ($warehouse_id > 0) {
            $model->whereRaw('psw.lft BETWEEN '.$q_warehouse['lft'].' AND '.$q_warehouse['rgt']);
        }

        $products = $model->orderBy('pc.lft', 'ASC')
        ->orderBy('p.sort', 'ASC')
        ->get(['p.*']);

        $products = array_by($products);
        
        $products['total'] = 0;

        // 获取库存结存
        $stocks = Stock::gets();

        // 库存总数
        foreach ($products as $v) {
            $product_id = $v['id'];
            $inventory[$product_id] = $stocks[1][$product_id] - $stocks[2][$product_id];
            $products['total'] += $inventory[$product_id];
        }

        $date = array_reverse(date_range($sdate, $edate));

        $query = url().'?'.http_build_query($selects['select']);

        $categorys  = ProductCategory::type('sale')->orderBy('lft', 'asc')->get()->toNested();
        $warehouses = Warehouse::type('sale')->orderBy('lft', 'asc')->get()->toNested();

        return $this->display(array(
            'client'   => $client,
            'query'    => $query,
            'selects'  => $selects,
            'inventory'=> $inventory,
            'res'      => $res,
            'delivery' => $delivery,
            'date'     => $date,
            'products' => $products,
            'categorys'=> $categorys,
            'warehouses'=> $warehouses,
        ));
    }

    // 物料需求
    public function purchaseAction()
    {
        //审核模板
        $order = config('order');
        $audits = $order['audit'];

        $category_id = Input::get('category_id', 0);
        $warehouse_id = Input::get('warehouse_id', 0);

        $selects['select']['category_id'] = $category_id;
        $selects['select']['warehouse_id'] = $warehouse_id;

        //模型实例
        $model = DB::table('order_data as oh')
        ->leftJoin('order as o', 'o.id', '=', 'oh.order_id')
        ->leftJoin('product as p', 'p.id', '=', 'oh.product_id')
        ->leftJoin('product_category as pc', 'pc.id', '=', 'p.category_id')
        ->leftJoin('warehouse as psw', 'psw.id', '=', 'p.warehouse_id')
        ->where('pc.type', 1)
        ->where('oh.deleted', 0)
        ->where('oh.fact_amount', '>', 0)
        ->where('o.delivery_time', '<=', 0)
        ->where('o.flow_step_id', '>', 1)
        ->where('o.status', 1)
        ->groupBy('oh.product_id')
        ->groupBy('oh.client_id')
        ->groupBy('o.id')
        ->orderBy('o.plan_time', 'ASC')
        ->orderBy('o.flow_step_id', 'DESC')
        ->orderBy('o.add_time', 'DESC')
        ->selectRaw('o.pay_time,o.delivery_time,o.arrival_time,o.number,o.plan_time,o.flow_step_id,oh.product_id,o.id,oh.client_id, SUM(oh.fact_amount) as amount');

        if ($category_id > 0) {
            $q_category = DB::table('product_category')->where('id', $category_id)->first(['lft', 'rgt']);
            $model->whereRaw('pc.lft BETWEEN '.$q_category['lft'].' AND '.$q_category['rgt']);
        }

        if ($warehouse_id > 0) {
            $q_warehouse = DB::table('warehouse')->where('id', $warehouse_id)->first(['lft', 'rgt']);
            $model->whereRaw('psw.lft BETWEEN '.$q_warehouse['lft'].' AND '.$q_warehouse['rgt']);
        }

        $rs = $model->get();

        $productModel = DB::table('product as p')
        ->leftJoin('product_category as pc', 'pc.id', '=', 'p.category_id')
        ->leftJoin('warehouse as psw', 'psw.id', '=', 'p.warehouse_id')
        ->where('pc.type', 1)
        ->where('p.status', 1);

        if ($warehouse_id > 0) {
            $productModel->whereRaw('psw.lft BETWEEN '.$q_warehouse['lft'].' AND '.$q_warehouse['rgt']);
        }

        if ($category_id > 0) {
            $productModel->whereRaw('pc.lft BETWEEN '.$q_category['lft'].' AND '.$q_category['rgt']);
        }

        $_products = $productModel->orderBy('pc.lft', 'ASC')
        ->orderBy('p.sort', 'ASC')
        ->get(['p.*']);

        // 获取库存结存
        $stocks = Stock::gets();

        $products = array();
        foreach ($_products as $v) {
            $product_id = $v['id'];
            $products[$product_id]  = $v;
            $inventory[$product_id] = $stocks[1][$product_id] - $stocks[2][$product_id];
        }

        $res = $single = $money = $moneyall = array();
        foreach ($rs as $key => $value) {
            $product_clients[$value['client_id']][$value['product_id']] += $value['amount'];

            $dealers['dealer'][$value['id']] = get_user($value['client_id']);
            $dealers['client'][$value['id']] = $value['client_id'];
            $dealers['plan'][$value['id']] = $value['plan'];

            $dealers['info'][$value['id']] = array(
                'number' => $value['number'],
                'flow_step_id'  => $value['flow_step_id'],
                'audit_name' => $audits[$value['flow_step_id']]['name']
            );

            $dealers['amount'][$value['id']] += $value['amount'];

            $all['a'] += $value['amount'];
            if ($value['pay_time'] > 0) {
                $money[$value['product_id']] += $value['amount'];
                $all['b'] += $value['amount'];
            } else {
                $single[$value['product_id']] += $value['amount'];
                $all['c'] += $value['amount'];
            }

            $moneyall[$value['product_id']] += $value['amount'];

            // $res[$value['id']]['dealer'] = $this->data['users'][$value['client_id']];
            $res[$value['id']]['dealer'] = get_user($value['client_id']);

            $res[$value['id']]['code'][$value['product_id']] += $value['amount'];
        }
        unset($rs);

        $query = url().'?'.http_build_query($selects['select']);

        return $this->display(array(
            'inventory' => $inventory,
            'res'      => $res,
            'json'     => $json,
            'single'   => $single,
            'dealers'  => $dealers,
            'all'      => $all,
            'money'    => $money,
            'moneyall' => $moneyall,
            'audits'   => $audits,
            'products' => $products,
            'selects'  => $selects,
            'query'    => $query,
            'product_clients' => $product_clients,
        ));
    }

    /**
     * 生产需求汇总
     */
    public function summaryAction()
    {
        $search = search_form([
            'category_id'  => 0,
            'warehouse_id' => 0,
        ], []);

        $query = $search['query'];

        if (Input::wantsJson()) {
            // 获取订单总数
            $model = DB::table('order as o')
            ->leftJoin('order_data as oi', 'o.id', '=', 'oi.order_id')
            ->leftJoin('product as p', 'p.id', '=', 'oi.product_id')
            ->leftJoin('product_category as pc', 'pc.id', '=', 'p.category_id')
            ->leftJoin('warehouse as psw', 'psw.id', '=', 'p.warehouse_id')
            ->where('oi.deleted', 0)
            ->where('o.flow_step_id', '>', 1)
            ->where('o.delivery_time', '<=', 0)
            ->where('o.status', 1)
            ->groupBy('oi.product_id')
            ->selectRaw('o.pay_time, o.delivery_time, o.arrival_time, oi.product_id, SUM(oi.amount) as amount');

            if ($query['category_id'] > 0) {
                $category = DB::table('product_category')->where('id', $query['category_id'])->first(['lft', 'rgt']);
                $model->whereRaw('pc.lft BETWEEN '.$category['lft'].' AND '.$category['rgt']);
            }

            if ($query['warehouse_id'] > 0) {
                $warehouse = DB::table('warehouse')->where('id', $query['warehouse_id'])->first(['lft', 'rgt']);
                $model->whereRaw('psw.lft BETWEEN '.$warehouse['lft'].' AND '.$warehouse['rgt']);
            }

            $res = $model->get();

            $order = array();
            foreach ($res as $v) {
                $order['total'] += $v['amount'];
                $order[$v['product_id']]['total'] += $v['amount'];
            }
            unset($res);

            $model = DB::table('product as p')
            ->leftJoin('product_category as pc', 'pc.id', '=', 'p.category_id')
            ->leftJoin('warehouse as psw', 'psw.id', '=', 'p.warehouse_id')
            ->where('pc.type', 1)
            ->where('p.status', 1);
            
            if ($query['category_id'] > 0) {
                $model->whereRaw('pc.lft BETWEEN '.$category['lft'].' AND '.$category['rgt']);
            }

            if ($query['warehouse_id'] > 0) {
                $model->whereRaw('psw.lft BETWEEN '.$warehouse['lft'].' AND '.$warehouse['rgt']);
            }

            $products = $model->orderBy('pc.lft', 'ASC')
            ->orderBy('p.sort', 'ASC')
            ->get(['p.*']);
            
            $products = array_by($products);
            
            // 获取库存结存
            $stocks = Stock::gets();
            
            // 库存总数
            $rows = [];
            foreach ($products as $v) {
                $product_id = $v['id'];
                $inventory[$product_id] = $stocks[1][$product_id] - $stocks[2][$product_id];
                $products['total'] += $inventory[$product_id];
                
                $quantity = $inventory[$product_id] - $order[$product_id]['total'];
                $total = 0;
                if ($quantity < 0) {
                    $total += $quantity;
                }

                $quantity = $quantity < 0 ? '<span style="color:red;">'. $quantity.'</span>' : $quantity;

                $name = $v['spec'] ? $v['name'].$v['spec'] : $v['name'];

                $row = ['id' => $v['id'], 'name' => $name, 'quantity' => $quantity, 'total' => $total];
                $rows[] = $row;
            }
            return response()->json($rows);
        }

        $categorys  = ProductCategory::type('sale')->orderBy('lft', 'asc')->get()->toNested();
        $warehouses = Warehouse::type('sale')->orderBy('lft', 'asc')->get()->toNested();

        return $this->display(array(
            'query'      => $query,
            'products'   => $products,
            'categorys'  => $categorys,
            'warehouses' => $warehouses,
        ));
    }

    /**
     * 生产计划
     */
    public function produceAction()
    {
        // 允许修改生产计划的角色编号
        $editRole = array(28, 1);

        // 提交备注
        if ($post = $this->post()) {
            if (empty($post['value'])) {
                exit('0');
            }

            $data = array('product_id' => $post['product_id'],'add_time' => $post['add_time'],'remark'=> $post['value']);

            $res = DB::table('produce_data')->whereRaw('product_id=? and add_time=?', [$post['product_id'], $post['add_time']])->first();

            if ($res) {
                DB::table('produce_data')->where('id', $res['id'])->update($data);
            } else {
                DB::table('produce_data')->insert($data);
            }
            exit('1');
        }

        // 开始天
        $startDate = date('Y-m-d', strtotime("+0 Day"));
        // 结束天
        $endDate = date('Y-m-d', strtotime("+2 Day"));

        $sdate = Input::get('sdate', $startDate);
        $edate = Input::get('edate', $endDate);
        $date = array_reverse(date_range($sdate, $edate));

        $category_id = Input::get('category_id', 0);
        $warehouse_id = Input::get('warehouse_id', 0);

        $selects['select']['sdate'] = $sdate;
        $selects['select']['edate'] = $edate;
        $selects['select']['warehouse_id'] = $warehouse_id;
        $selects['select']['category_id'] = $category_id;

        // 创建今天的计划主表
        foreach ($date as $row) {
            $produce_time = strtotime($row);

            $produce_day = DB::table('produce')
            ->where('add_time', $produce_time)
            ->first();
            
            if (empty($produce_day)) {
                $insert_id = DB::table('produce')->insertGetId(['add_time' => $produce_time]);
                DB::table('produce_data')->where('add_time', $produce_time)->update(['produce_id' => $insert_id]);
            }
        }

        // 获取入库数据
        $model = DB::table('stock_data as a')
        ->leftJoin('stock as b', 'b.id', '=', 'a.stock_id')
        ->leftJoin('stock_type as c', 'c.id', '=', 'b.type_id')
        ->leftJoin('product as p', 'a.product_id', '=', 'p.id')
        ->leftJoin('product_category as pc', 'pc.id', '=', 'p.category_id')
        ->leftJoin('warehouse as psw', 'psw.id', '=', 'p.warehouse_id')
        ->groupBy('a.product_id')
        ->groupBy('date')
        ->where('c.type', 1)
        ->selectRaw('a.*,SUM(a.amount) as amount, FROM_UNIXTIME(a.add_time,"%Y-%m-%d") as date');

        if ($category_id > 0) {
            $q_category = DB::table('product_category')->where('id', $category_id)->first(['lft', 'rgt']);
            $model->whereRaw('pc.lft BETWEEN '.$q_category['lft'].' AND '.$q_category['rgt']);
        }

        if ($warehouse_id > 0) {
            $q_warehouse = DB::table('warehouse')->where('id', $warehouse_id)->first(['lft', 'rgt']);
            $model->whereRaw('psw.lft BETWEEN '.$q_warehouse['lft'].' AND '.$q_warehouse['rgt']);
        }

        if ($sdate) {
            $model->whereRaw('FROM_UNIXTIME(a.add_time,"%Y-%m-%d") >= ?', [$sdate]);
        }
        if ($edate) {
            $model->whereRaw('FROM_UNIXTIME(a.add_time,"%Y-%m-%d") <= ?', [$edate]);
        }

        $res = $model->orderBy('date', 'ASC')->get()->toArray();

        $warehouse = array();
        foreach ($res as $v) {
            $warehouse['total'] += $v['amount'];
            $warehouse[$v['date']]['total'] += $v['amount'];
            $warehouse[$v['product_id']]['total'] += $v['amount'];
            $warehouse[$v['product_id']][$v['date']] += $v['amount'];
        }
        unset($res);

        // 获取订单总数
        $model = DB::table('order as o')
        ->leftJoin('order_data as oi', 'o.id', '=', 'oi.order_id')
        ->leftJoin('product as p', 'p.id', '=', 'oi.product_id')
        ->leftJoin('product_category as pc', 'pc.id', '=', 'p.category_id')
        ->leftJoin('warehouse as psw', 'psw.id', '=', 'p.warehouse_id')
        ->where('oi.deleted', 0)
        ->where('o.flow_step_id', '>', 3)
        ->where('o.delivery_time', '<=', 0)
        ->where('o.status', 1)
        ->groupBy('oi.product_id')
        ->selectRaw('o.pay_time, o.delivery_time, o.arrival_time, oi.product_id, SUM(oi.amount) as amount');

        if ($category_id > 0) {
            $model->whereRaw('pc.lft BETWEEN '.$q_category['lft'].' AND '.$q_category['rgt']);
        }

        if ($warehouse_id > 0) {
            $model->whereRaw('psw.lft BETWEEN '.$q_warehouse['lft'].' AND '.$q_warehouse['rgt']);
        }

        $res = $model->get()->toArray();

        $order = array();
        foreach ($res as $v) {
            $order['total'] += $v['amount'];
            $order[$v['product_id']]['total'] += $v['amount'];
        }
        unset($res);

        // 计划生产的订单
        $model = DB::table('order_data as oi')
        ->leftJoin('order as o', 'o.id', '=', 'oi.order_id')
        ->leftJoin('product as p', 'p.id', '=', 'oi.product_id')
        ->leftJoin('product_category as pc', 'pc.id', '=', 'p.category_id')
        ->leftJoin('warehouse as psw', 'psw.id', '=', 'p.warehouse_id')
        ->leftJoin('user as c', 'c.id', '=', 'o.client_id')
        ->where('oi.deleted', 0)
        ->where('o.plan_time', '>', 0)
        ->where('o.flow_step_id', '>', 3)
        ->where('o.delivery_time', '<=', 0)
        ->groupBy('oi.product_id')
        ->groupBy('date')
        ->groupBy('o.client_id')
        ->selectRaw('oi.product_id, o.pay_time, SUM(amount) as amount, FROM_UNIXTIME(o.plan_time,"%Y-%m-%d") as date,o.client_id,c.nickname as company_name');

        if ($category_id > 0) {
            $model->whereRaw('pc.lft BETWEEN '.$q_category['lft'].' AND '.$q_category['rgt']);
        }

        if ($warehouse_id > 0) {
            $model->whereRaw('psw.lft BETWEEN '.$q_warehouse['lft'].' AND '.$q_warehouse['rgt']);
        }

        if ($sdate) {
            $model->whereRaw('FROM_UNIXTIME(o.plan_time,"%Y-%m-%d") >= ?', [$sdate]);
        }
        if ($edate) {
            $model->whereRaw('FROM_UNIXTIME(o.plan_time,"%Y-%m-%d") <= ?', [$edate]);
        }

        $res = $model->orderBy('date', 'ASC')->get();

        $plan = array();
        foreach ($res as $v) {
            $plan['total'] += $v['amount'];
            $plan[$v['date']]['total'] += $v['amount'];
            $plan[$v['product_id']]['total'] += $v['amount'];
            $plan[$v['product_id']][$v['date']] += $v['amount'];

            $client[$v['product_id']][$v['date']][$v['client_id']] += $v['amount'];
            $client['name'][$v['client_id']] = $v['company_name'];

            // 计算已经付款的订单
            if ($v['pay_time'] > 0) {
                $plan['pay']['total'] += $v['amount'];
                $plan['pay'][$v['product_id']] += $v['amount'];
            }
        }
        unset($res);
        
        // 读取生产计划
        $model = DB::table('produce as a')
        ->leftJoin('produce_data as pd', 'a.id', '=', 'pd.produce_id')
        ->leftJoin('product as p', 'p.id', '=', 'pd.product_id')
        ->leftJoin('product_category as pc', 'pc.id', '=', 'p.category_id')
        ->leftJoin('warehouse as psw', 'psw.id', '=', 'p.warehouse_id')
        ->groupBy('a.id')
        ->groupBy('pd.product_id')
        ->selectRaw('a.*,SUM(pd.amount) as amount,pd.product_id,FROM_UNIXTIME(a.add_time,"%Y-%m-%d") as date');

        if ($category_id > 0) {
            $model->whereRaw('pc.lft BETWEEN '.$q_category['lft'].' AND '.$q_category['rgt']);
        }

        if ($warehouse_id > 0) {
            $model->whereRaw('psw.lft BETWEEN '.$q_warehouse['lft'].' AND '.$q_warehouse['rgt']);
        }

        if ($sdate) {
            $model->whereRaw('FROM_UNIXTIME(a.add_time,"%Y-%m-%d") >= ?', [$sdate]);
        }
        if ($edate) {
            $model->whereRaw('FROM_UNIXTIME(a.add_time,"%Y-%m-%d") <= ?', [$edate]);
        }

        $res = $model->orderBy('a.add_time', 'ASC')->get();

        $produce = array();
        foreach ($res as $v) {
            $produce['total'] += $v['amount'];
            $produce[$v['date']]['total'] += $v['amount'];
            $produce[$v['product_id']]['total'] += $v['amount'];
            $produce[$v['product_id']][$v['date']] += $v['amount'];

            $produce['main'][$v['date']] = $v;

            $remark[$v['product_id']][$v['date']] = $v['remark'];

            // 计算已经付款的订单
            if ($v['pay_time'] > 0) {
                $plan['pay']['total'] += $v['amount'];
                $plan['pay'][$v['product_id']] += $v['amount'];
            }
        }
        unset($res);
        
        $model = DB::table('product as p')
        ->leftJoin('product_category as pc', 'pc.id', '=', 'p.category_id')
        ->leftJoin('warehouse as psw', 'psw.id', '=', 'p.warehouse_id')
        ->where('pc.type', 1)
        ->where('p.status', 1);
        
        if ($category_id > 0) {
            $model->whereRaw('pc.lft BETWEEN '.$q_category['lft'].' AND '.$q_category['rgt']);
        }

        if ($warehouse_id > 0) {
            $model->whereRaw('psw.lft BETWEEN '.$q_warehouse['lft'].' AND '.$q_warehouse['rgt']);
        }

        $products = $model->orderBy('pc.lft', 'ASC')
        ->orderBy('p.sort', 'ASC')
        ->get(['p.*']);
        
        $products = array_by($products);
        
        $diff_total = $products['total'] = 0;

        // 获取库存结存
        $stocks = Stock::gets();
        
        // 库存总数
        foreach ($products as $v) {
            $product_id = $v['id'];
            $inventory[$product_id] = $stocks[1][$product_id] - $stocks[2][$product_id];
            $products['total'] += $inventory[$product_id];

            $diff_number = $inventory[$product_id] - $order[$product_id]['total'];
            if ($diff_number < 0) {
                $diff_total += $diff_number;
            }
        }

        $query = url().'?'.http_build_query($selects['select']);

        $categorys  = ProductCategory::type('sale')->orderBy('lft', 'asc')->get()->toNested();
        $warehouses = Warehouse::type('sale')->orderBy('lft', 'asc')->get()->toNested();

        // 编辑备注角色编号
        $remarkRole = array(1, 22);

        return $this->display(array(
            'query'    => $query,
            'selects'  => $selects,
            'res'      => $res,
            'inventory'=> $inventory,
            'warehouse'=> $warehouse,
            'diff_total' => $diff_total,
            'plan'     => $plan,
            'order'    => $order,
            'produce'  => $produce,
            'date'     => $date,
            'editRole' => $editRole,
            'products' => $products,
            'categorys'=> $categorys,
            'warehouses'=>$warehouses,
            'client'   => $client,
            'remark'   => $remark,
            'remarkRole'=>$remarkRole,
        ));
    }

    // 添加和编辑生产计划
    public function produce_addAction()
    {
        if ($post = $this->post()) {
            $produce_id = Input::get('produce_id');
            if (empty($produce_id)) {
                return $this->json('生产计划编号无效。');
            }

            $produce = array_filter($post['produce'][$produce_id]);
            if (empty($produce)) {
                return $this->json('生产计划无效。');
            }

            foreach ($produce as $product_id => $amount) {
                $row = DB::table('produce_data')->whereRaw('produce_id=? and product_id=?', [$produce_id, $product_id])->first();

                $data = array('produce_id'=>$produce_id,'product_id'=>$product_id,'amount'=>$amount);
                if (empty($row)) {
                    DB::table('produce_data')->insert($data);
                } else {
                    DB::table('produce_data')->where('id', $row['id'])->update($data);
                }
            }
            return $this->json('生产计划操作成功。', true);
        }
    }

    // 审核和反审生产计划
    public function produce_stateAction()
    {
        if ($post = $this->post()) {
            if (empty($post['produce_id'])) {
                return $this->json('生产计划编号无效。');
            }

            $data['state'] = $post['state'];
            DB::table('produce')->where('id', $post['produce_id'])->update($data);
            return $this->json('生产计划操作成功。', true);
        }
    }

    // 订单统计
    public function countAction()
    {
        //筛选专用函数
        $selects = select::head();
        $where = $selects['where'];

        $page_id = Input::get('page', 1);

        $select_key = array(
            'year'=>date('Y'),
            'month'=>0,
            'day'=>0,
            'time_type' => 'add_time',
            'category_id'=>0,
            'warehouse_id'=>0,
            'product_id'=>0,
            'invoice_type'=>0,
        );
        foreach ($select_key as $k => $v) {
            $selects['select'][$k] = Input::get($k, $v);
        }
        extract($selects['select'], EXTR_PREFIX_ALL, 'select');

        $model = DB::table('order_data as oh');

        if ($select_number) {
            $model->where('o.number', 'like', $select_number);
        }

        if ($select_invoice_type > 0) {
            $model->where('o.invoice_type', $select_invoice_type);
        }

        if ($select_category_id > 0) {
            $q_category = DB::table('product_category')->where('id', $select_category_id)->first(['lft', 'rgt']);
            
            // 产品列表
            $products = DB::table('product as p')
            ->join('product_category as pc', 'pc.id', '=', 'p.category_id')
            ->whereRaw('pc.lft BETWEEN '.$q_category['lft'].' AND '.$q_category['rgt'])->get(['p.*']);

            $model->whereRaw('pc.lft BETWEEN '.$q_category['lft'].' AND '.$q_category['rgt']);
        }

        if ($select_warehouse_id > 0) {
            $q_warehouse = DB::table('warehouse')->where('id', $select_warehouse_id)->first(['lft', 'rgt']);
            $model->whereRaw('psw.lft BETWEEN '.$q_warehouse['lft'].' AND '.$q_warehouse['rgt']);
        }

        if ($select_product_id > 0) {
            $model->where('p.id', $select_product_id);
        }

        $date = "%Y-%m";
        // 选择了月份
        if ($select_month > 0) {
            $date = "%Y-%m-%d";
            $model->whereRaw('FROM_UNIXTIME(o.'.$select_time_type.',"%m")=?', [$select_month]);
        }
        // 选择了日
        if ($select_day > 0) {
            $date = "%Y-%m-%d %H:%i";
            $model->whereRaw('FROM_UNIXTIME(o.'.$select_time_type.',"%d")=?', [$select_day]);
        }

        $amount_type = ($select_time_type == 'delivery_time') ? 'fact_amount' : 'amount';

        $model->leftJoin('order as o', 'o.id', '=', 'oh.order_id')
        ->leftJoin('product as p', 'p.id', '=', 'oh.product_id')
        ->leftJoin('product_category as pc', 'pc.id', '=', 'p.category_id')
        ->leftJoin('warehouse as psw', 'psw.id', '=', 'p.warehouse_id')
        ->leftJoin('user as c', 'c.id', '=', 'o.client_id')
        ->leftJoin('order_type as t', 't.id', '=', 'oh.type')
        ->where('oh.deleted', 0)
        ->where('t.type', 1)
        ->whereRaw('FROM_UNIXTIME(o.'.$select_time_type.',"%Y") = ? ', [$select_year])
        ->where('o.'.$select_time_type, '>', 0)
        ->where('o.status', 1)
        ->where('o.flow_step_id', '>', 1)
        ->where('pc.type', 1)
        ->groupBy('order_date')
        ->orderBy('o.'.$select_time_type, 'DESC')
        ->selectRaw("p.category_id,COUNT(DISTINCT oh.client_id) as order_client, COUNT(DISTINCT oh.order_id) as order_count, SUM(oh.$amount_type * oh.price) as order_money, SUM(oh.$amount_type) as order_amount, SUM(oh.$amount_type * p.weight) as order_weight, o.flow_step_id,FROM_UNIXTIME(o.{$select_time_type},'{$date}') as order_date");

        //获取主数据
        $res = $model->whereRaw($where)->get();

        $query = url().'?'.http_build_query($selects['select']);

        $product_category = ProductCategory::type('sale')->orderBy('lft', 'asc')->get()->toNested();
        $warehouses = Warehouse::type('sale')->orderBy('lft', 'asc')->get()->toNested();

        $years = range(date('Y', $this->setting['setup_at']), date('Y'));
        $months = range(1, 12);
        $days = range(1, 31);
        $order_config = config('order');

        return $this->display(array(
            'years'   => $years,
            'months'  => $months,
            'days'    => $days,
            'product_category' => $product_category,
            'warehouses' => $warehouses,
            'products' => $products,
            'res'      => $res,
            'category' => $category,
            'data'     => $data,
            'query'    => $query,
            'selects'  => $selects,
        ));
    }

    /**
     * 显示运营系数
     */
    public function coefficientAction()
    {
        // 成品库全部代码
        $warehouse_id = ['1','2','4'];
        $data = array();

        $now_year  = Input::get('year', date('Y'));
        $last_year = date('Y', strtotime("-1 year"));

        $months = range(1, 12);

        $cache_key = 'coefficient-'.$now_year;

        if (Cache::has($cache_key)) {
            $month_data = Cache::get($cache_key);
        } else {
            $rows = DB::table('order as o')
            ->leftJoin('order_data as od', 'o.id', '=', 'od.order_id')
            ->leftJoin('order_type as ot', 'ot.id', '=', 'od.type')
            ->leftJoin('product as p', 'p.id', '=', 'od.product_id')
            ->whereIn('p.warehouse_id', $warehouse_id)
            ->where('o.delivery_time', '>', 0)
            ->where('o.status', 1)
            ->where('od.deleted', 0)
            ->where('ot.type', 1)
            ->groupBy('year')
            ->selectRaw('p.warehouse_id,FROM_UNIXTIME(o.delivery_time,"%Y-%c") as year,COUNT(DISTINCT od.order_id) as count,COUNT(DISTINCT od.client_id) as client,SUM(od.price*od.fact_amount) as money,SUM(od.fact_amount) as amount, SUM(od.fact_amount * p.weight) as weight')
            ->get();
            
            foreach ($rows as $row) {
                list($year, $month) = explode('-', $row['year']);
                $data['a'][$year]['count'][$month] = $row['count'];
                $data['a'][$year]['client'][$month] = $row['client'];
                $data['a'][$year]['money'][$month] = $row['money'];
                $data['a'][$year]['amount'][$month] = $row['amount'];
                $data['a'][$year]['weight'][$month] = $row['weight'];
            }
            unset($rows);

            // 成品库泡菜代码
            $warehouse_id = [2];

            $rows = DB::table('order as o')
            ->join('order_data as od', 'o.id', '=', 'od.order_id')
            ->join('order_type as ot', 'ot.id', '=', 'od.type')
            ->join('product as p', 'p.id', '=', 'od.product_id')
            ->whereIn('p.warehouse_id', $warehouse_id)
            ->where('o.delivery_time', '>', 0)
            ->where('od.deleted', 0)
            ->where('o.status', 1)
            ->where('ot.type', 1)
            ->groupBy('year')
            ->selectRaw('p.warehouse_id,FROM_UNIXTIME(o.delivery_time,"%Y-%c") as year,COUNT(DISTINCT od.order_id) as count,COUNT(DISTINCT od.client_id) as client,SUM(od.price*od.fact_amount) as money,SUM(od.fact_amount) as amount, SUM(od.fact_amount * p.weight) as weight')
            ->get();
            
            foreach ($rows as $row) {
                list($year, $month) = explode('-', $row['year']);
                $data['b'][$year]['count'][$month] = $row['count'];
                $data['b'][$year]['client'][$month] = $row['client'];
                $data['b'][$year]['money'][$month] = $row['money'];
                $data['b'][$year]['amount'][$month] = $row['amount'];
                $data['b'][$year]['weight'][$month] = $row['weight'];
            }
            unset($rows);

            if ($last_year == '2013') {
                $last_year_avg['a']['client'] = 268;
                $last_year_avg['a']['count']  = 414;
                $last_year_avg['a']['amount'] = 397377;
                $last_year_avg['a']['weight'] = 2621*1000;
                $last_year_avg['a']['money']  = 2461*10000;
                $last_year_avg['b']['weight'] = 1727*1000;
            } else {
                $last_year_avg = array();
                foreach ($data['a'][$last_year] as $key => $value) {
                    $last_year_avg['a'][$key] = array_sum($value)/count($value);
                }
                foreach ($data['b'][$last_year] as $key => $value) {
                    $last_year_avg['b'][$key] = array_sum($value)/count($value);
                }
            }

            $month_data = array();
            foreach ($months as $month) {
                // 客户课
                $month_data[22][$month] = (($data['a'][$now_year]['client'][$month]/$last_year_avg['a']['client'])+($data['a'][$now_year]['count'][$month]/$last_year_avg['a']['count'])+($data['a'][$now_year]['money'][$month]/$last_year_avg['a']['money']))/3;
                // 推广课
                $month_data[23][$month] = ($data['a'][$now_year]['client'][$month]/$last_year_avg['a']['client']+$data['a'][$now_year]['money'][$month]/$last_year_avg['a']['money'])/2;
                // 生产部
                $month_data[3][$month]  = ($data['a'][$now_year]['amount'][$month]/$last_year_avg['a']['amount']+$data['a'][$now_year]['weight'][$month]/$last_year_avg['a']['weight'])/2;
                // 人事办公室和行政办公室
                $month_data[33][$month] = $month_data[11][$month] = $data['a'][$now_year]['amount'][$month]/$last_year_avg['a']['amount'];
                // 运营课
                $month_data[24][$month] = ($data['a'][$now_year]['count'][$month]/$last_year_avg['a']['count']+$data['a'][$now_year]['amount'][$month]/$last_year_avg['a']['amount'])/2;
                // 财务部
                $month_data[5][$month]  = $data['a'][$now_year]['count'][$month]/$last_year_avg['a']['count'];
                // 品管部
                $month_data[20][$month] = ($data['a'][$now_year]['amount'][$month]/$last_year_avg['a']['amount']+$data['a'][$now_year]['weight'][$month]/$last_year_avg['a']['weight'])/2;
                // 原采课和运采课
                // $month_data[4][$month] = $month_data[35][$month] = $data['a'][$now_year]['weight'][$month]/$last_year_avg['a']['weight'];
                // 致味部
                $month_data[21][$month] = $data['b'][$now_year]['weight'][$month]/$last_year_avg['b']['weight'];
                // 课部管理
                $month_data[0][$month] = $data['a'][$now_year]['money'][$month]/$last_year_avg['a']['money'];
            }
            // 缓存24小时
            Cache::put($cache_key, $month_data, 60*24);
            unset($data);
        }

        $query['url'] = url('', ['year' => $now_year]);
        return $this->display(array(
            'month_data' => $month_data,
            'months'     => $months,
            'now_year'   => $now_year,
            'query'      => $query,
        ));
    }

    /**
     * 生产批号查询
     */
    public function batchAction()
    {
        $order_id = Input::get('order_id', 0);
        $client_id = Input::get('client_id', 0);

        $client = DB::table('user')->where('id', $client_id)->first();
        $order = DB::table('order')->where('id', $order_id)->first();
        
        $money = DB::table('order_data')
        ->where('order_id=? AND deleted=0', [$order_id])
        ->selectRaw('SUM(fact_amount * price) money')
        ->first();
        
        return $this->display([
            'money'  => $money,
            'order'  => $order,
            'client' => $client,
        ]);
    }

    /**
     * 统计客户物料计算金额
     */
    public function materielAction()
    {
        $date = Input::get('date', date('Y-m'));

        $rows = DB::table('order_data')
        ->LeftJoin('product', 'product.id', '=', 'order_data.product_id')
        ->LeftJoin('warehouse', 'warehouse.id', '=', 'product.warehouse_id')
        ->LeftJoin('client', 'client.id', '=', 'order_data.client_id')
        ->LeftJoin('user', 'user.id', '=', 'client.user_id')
        ->where('warehouse.type', 1)
        ->where('warehouse.advert', 1)
        ->where('client.sp_materiel', 1)
        ->whereRaw('FROM_UNIXTIME(order_data.add_time, "%Y-%m") = ?', [$date])
        ->selectRaw('user.username,user.nickname,order_data.client_id,SUM(order_data.fact_amount * order_data.price) as total')
        ->groupBy('order_data.client_id')
        ->get();

        return $this->display(array(
            'rows' => $rows,
            'date' => $date,
        ));
    }
}
