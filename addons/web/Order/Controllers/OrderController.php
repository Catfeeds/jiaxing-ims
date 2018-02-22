<?php namespace Aike\Web\Order\Controllers;

use DB;
use Input;
use Request;
use Auth;
use Paginator;

use select;

use Aike\Web\Customer\Customer;
use Aike\Web\Supplier\ProductCategory;
use Aike\Web\Supplier\Warehouse;
use Aike\Web\Order\Order;
use Aike\Web\Product\Stock;
use Aike\Web\Index\Notification;

use Aike\Web\Index\Controllers\DefaultController;

class OrderController extends DefaultController
{
    public $permission = ['product_dialog'];

    /**
      * 订单列表
      */
    public function indexAction()
    {
        // 客户圈权限
        $circle = select::circleCustomer();

        $columns = [
            ['text','order.number','订单号'],
            ['step','order.flow_step_id','审批流程'],
            ['second','order.add_time','订单日期'],
            ['text','user.nickname','客户名称'],
            ['text','user.username','客户代码'],
        ];

        $columns = array_merge($columns, $circle['columns']);

        $search = search_form([
            'referer' => 1
        ], $columns);

        $query  = $search['query'];
        
        // 订单配置数据
        $order_config = config('order');

        $model = Order::orderBy('order.id', 'desc');

        // 配送助理显示自己负责的客户
        if (Auth::user()->role->name == 'transport') {
            $model->where('client.transport_user_id', Auth::id());
        }

        if ($circle['whereIn']) {
            foreach ($circle['whereIn'] as $key => $where) {
                $model->whereIn($key, $where);
            }
        }

        foreach ($search['where'] as $where) {
            if ($where['active']) {
                $model->search($where);
            }
        }

        $model->leftJoin('user', 'user.id', '=', 'order.client_id')
        ->leftJoin('client', 'client.user_id', '=', 'user.id');
        
        // 获取促销指定步骤数据
        $model->with(['promotions' => function ($q) {
            $q->where('status', 0)->where('step_number', 8);
        }]);

        // 获取进店指定步骤数据
        $model->with(['approachs' => function ($q) {
            $q->where('status', 0)->where('step_number', 12);
        }]);

        // 查询订单明细合计
        $model->with(['datas' => function ($q) {
            $q->leftJoin('product', 'order_data.product_id', '=', 'product.id')
            ->where('order_data.deleted', 0)
            ->selectRaw('product.category_id,order_id,order_data.amount,order_data.amount * product.weight as weight');
        }]);

        $rows = $model->selectRaw('`order`.*,user.nickname as company_name,client.circle_id')
        ->paginate()->appends($query);

        // 组合流程步骤
        $steps = [];
        foreach ($order_config['audit'] as $i => $step) {
            $steps[] = ['id' => $i, 'name' => $i.'.'.$step['name']];
        }

        // 计算战略比
        $strategic = [];
        $materiel = [];
        $categorys = ProductCategory::type('sale')->orderBy('lft', 'asc')->get()->toNested();
        foreach ($categorys as $category_id => $category) {
            // 产品类别纳入统计
            if (in_array($category['parent'][0], [20, 498]) !== false) {
                $strategic[] = $category['id'];
            }
            // 物料
            if (in_array($category['parent'][0], [490, 297]) !== false) {
                $materiel[] = $category['id'];
            }
        }

        // 视图设置
        return $this->display(array(
            'audit_config' => $order_config['audit'],
            'strategic'    => $strategic,
            'materiel'     => $materiel,
            'order_config' => $order_config,
            'circle'       => $circle,
            'rows'         => $rows,
            'steps'        => $steps,
            'search'       => $search,
        ));
    }

    public function dataAction()
    {
        $id = (int)Input::get('id');
        $client_id = (int)Input::get('client_id');

        // 确认调用详细表
        if ($id > 0) {
            $model = DB::table('order_data as ot')
            ->selectRaw('p.*,ot.id,ot.product_id,ot.price,ot.type,ot.content,ot.amount,ot.fact_amount,ot.inventory,ot.batch_number,psw.advert')
            ->where('ot.order_id', $id);
        } else {
            $model = DB::table('order_temp as ot')
            ->selectRaw('p.*,ot.id,ot.product_id,ot.price,ot.type,ot.amount,ot.inventory,psw.advert')
            ->where('ot.client_id', $client_id);
        }
        $orderinfo = $model->leftJoin('product as p', 'p.id', '=', 'ot.product_id')
        ->leftJoin('product_category as pc', 'pc.id', '=', 'p.category_id')
        ->leftJoin('warehouse as psw', 'psw.id', '=', 'p.warehouse_id')
        ->where('ot.deleted', 0)
        ->orderBy('pc.lft', 'ASC')
        ->orderBy('p.sort', 'ASC')
        ->groupBy('ot.id')
        ->get();

        $order = DB::table('order')->where('id', $id)->first();

        // 计算历史月销售
        $start = date('Ym', strtotime('-4 month'));
        $end   = date('Ym', strtotime('-1 month'));

        $months = DB::table('order')
        ->LeftJoin('order_data', 'order.id', '=', 'order_data.order_id')
        ->where('order.client_id', $order['client_id'])
        ->whereRaw("FROM_UNIXTIME(order.add_time,'%Y%m') between ? and ?", [$start, $end])
        ->selectRaw('order_data.product_id,sum(fact_amount) as amount')
        ->groupBy('order_data.product_id')
        ->pluck('amount', 'product_id');

        // 合同品项
        $contract = DB::table('customer_contract')->where('client_id', $client_id)->get();
        $contract = is_array($contract[0]) ? $contract[0] : array();

        $contract['product_item'] = empty($contract['product_item']) ? $contract['product'] : $contract['product_item'];
        $productItem = json_decode($contract['product_item'], true);

        // 订单类型科目
        $orderType = DB::table('order_type')->get();
        $orderType = array_by($orderType);
        
        // 订单类型
        $order_config = config('order');
        $json = array();

        $categorys = DB::table('product_category')
        ->where('type', 1)
        ->orderBy('sort', 'ASC')
        ->get();

        $categorys = array_nest($categorys);
        
        foreach ($orderinfo as $k => $v) {
            $history_number = $months[$v['product_id']];
            $history_number = $history_number > 0 ? ($history_number / 3) * 1.5 : 0;
            $v['history_number'] = $history_number;

            $v['type'] = $v['type'] > 0 ? $v['type'] : 1;

            $category = $categorys[$v['category_id']]['parent'][0];
            if ($category) {
                $v['category_name'] = $categorys[$category]['name'];
            }

            $json['rows'][$k] = $v;
            $json['rows'][$k]['itemid'] = $k;
    
            // 单品类型，是要是确认他是否计算金额
            $productType = $orderType[$v['type']];

            $json['rows'][$k]['type'] = '<span style="color:#999;">'.$productType['title'].'</span>';

            $money         = $productType['type'] == 1 ? $v['price'] * $v['amount']      : 0;
            
            $fact_money    = $productType['type'] == 1 ? $v['price'] * $v['fact_amount'] : 0;
            
            $remark_money  = $productType['type'] == 0 ? $v['price'] * $v['fact_amount'] : 0;

            // 运费计算
            $freight_money = $v['freight'] * $v['amount'];

            $customer = Customer::find($client_id);

            // 勾选了强行收费的产品不包含在内
            if ($v['force_charge'] == 0) {
                // 免物料费用的客户
                if ($customer->sp_materiel == 1 && $v['advert'] == 1) {
                    $money = $fact_money = 0;
                }
            }
            
            // 免运费的客户
            if ($customer->freight_type == 1) {
                $freight_money = 0;
            }

            // 产品基点默认为1
            $v['level_amount'] = $v['level_amount'] > 0 ? $v['level_amount'] : 1;

            // 计算基点倍数
            $level_amount = number_format($v['amount'] / $v['level_amount'], 2);

            // 计算产品数量差异化
            $diff_amount = ($v['fact_amount'] > 0) ? $v['fact_amount'] - $v['amount'] : 0;

            $json['rows'][$k]['money'] = $money;
            $json['rows'][$k]['fact_money'] = $fact_money;
            $json['rows'][$k]['diff_amount'] = $diff_amount;
            $json['rows'][$k]['remark_money'] = $remark_money;
            $json['rows'][$k]['freight_money'] = $freight_money;
            $json['rows'][$k]['level_amount'] = $level_amount;
        
            $weight = ($v['weight'] * $v['amount']);
            $json['rows'][$k]['weight'] = $weight / 1000;

            $fact_weight = $v['weight'] * $v['fact_amount'];
            $json['rows'][$k]['fact_weight'] = $fact_weight / 1000;

            $json['rows'][$k]['unit'] = option('goods.unit', $v['unit']);

            // 单品签约情况
            $json['rows'][$k]['contract'] = '无';

            if ($productItem[$v['product_id']] == 1) {
                $json['rows'][$k]['contract'] = '<span style="color:green;">是</span>';
            }

            if ($productItem[$v['product_id']] == 2) {
                $json['rows'][$k]['contract'] = '<span style="color:red;">必</span>';
            }

            $footer['money']         += $money;
            $footer['level_amount']  += $level_amount;
            $footer['fact_money']    += $fact_money;
            $footer['remark_money']  += $remark_money;
            $footer['freight_money'] += $freight_money;
            $footer['weight']        += $weight;
            $footer['fact_weight']   += $fact_weight;
            $footer['inventory']     += $v['inventory'];
            $footer['amount']        += $v['amount'];
            $footer['fact_amount']   += $v['fact_amount'];
            $footer['diff_amount']   += $diff_amount;
        }

        // 数据为空
        if (empty($json)) {
            $json['total'] = 0;
        } else {
            $json['total'] = sizeof($json['rows']);
            $footer['type'] = '合计';
            $json['footer'][] = $footer;
            return json_encode($json);
        }
    }

    // 选择产品
    public function product_addAction()
    {
        $query = array(
            'order_id'         => 0,
            'client_id'        => 0,
            'category_id'      => 0,
            'search_key'       => '',
            'search_condition' => '',
            'search_value'     => ''
        );
        foreach ($query as $k => $v) {
            $query[$k] = Input::get($k, $v);
        }
        extract($query, EXTR_PREFIX_ALL, 'q');

        if ($post = $this->post('product')) {
            $post = array_filter($post);

            $array = array();
            foreach ($post as $product) {
                $id = $product['product_id'];
                if ($product['amount'] > 0 && $product['inventory'] > 0) {
                    $array[$id] = $product;
                    $array[$id]['type'] = empty($product['type']) ? 1 : $product['type'];
                }
            }

            if (empty($array)) {
                return '基点数量和剩余库存必须填写。';
            }

            // 取得已经存在的产品或者是购物车
            $table = $q_order_id == 0 ? 'order_temp' : 'order_data';
            $model = DB::table($table)
            ->where('client_id', $q_client_id)
            ->whereRaw('deleted=0');
            
            if ($q_order_id > 0) {
                $model->where('order_id', $q_order_id);
            }
            $repeats = $model->get()->toArray();

            if (is_array($repeats)) {
                $data = array();
                foreach ($repeats as $repeat) {
                    $data[$repeat['type']][$repeat['product_id']] = $repeat;
                }
            }
            unset($repeats);

            /*
            // 获取客户资料
            $customer = Customer::find($q_client_id);

            // force_charge

            // 免物料费用的客户
            if($customer->sp_materiel == 1 && $v['advert'] == 1) {
                $money = $fact_money = 0;
            }

            // 免运费的客户
            if($customer->freight_type == 1) {
                $freight_money = 0;
            }
            */

            foreach ($array as $product_id => $_product) {

                // 添加单品时不同类型单独写入产品
                $product = $data[$_product['type']][$product_id];

                // 全部新添加产品
                $product = false;
                if ($product) {
                    $update['amount'] = $product['amount'] + $_product['amount'];
                    DB::table($table)->where('id', $product['id'])->update($update);
                } else {
                    $insert = [
                        'client_id'  => $q_client_id,
                        'product_id' => $_product['product_id'],
                        'price'      => $_product['price'],
                        'amount'     => $_product['amount'],
                        'type'       => $_product['type'],
                        'inventory'  => $_product['inventory'],
                    ];

                    if ($q_order_id > 0) {
                        $insert['order_id'] = $q_order_id;
                        $insert['status'] = 2;
                    } else {
                        $insert['status'] = 1;
                    }
                    DB::table($table)->insert($insert);
                }
            }
            return 1;
        }

        // 获取客户信息
        $client = DB::table('user')->where('id', $q_client_id)->first();

        $contract = DB::table('customer_contract')
        ->whereRaw('end_time > ?', [time()])
        ->where('client_id', $q_client_id)
        ->first();

        if (empty($client['post'])) {
            return $this->alert('客户类型不正确。');
        }

        if (empty($contract)) {
            return $this->alert('合同不存在或已过期。');
        }

        $productItem  = json_decode($contract['product_item'], true);
        $categoryItem = json_decode($contract['category_item'], true);
        $priceItem    = json_decode($contract['price_item'], true);
        
        // 计算类别
        $res = DB::table('product')->where('status', 1)->get();
        $res = array_by($res);
        
        $selectCategoryItem = [];
        
        foreach ($res as $k => $v) {

            // 客户登录时候排除品项授权以外的类别和产品
            if (Auth::user()->role->name == 'client') {
                if ($categoryItem[$v['category_id']] > 0 or $productItem[$k] > 0 or $v['authority'] == 1) {
                    $selectCategoryItem[$v['category_id']] = true;
                }
            } else {
                $selectCategoryItem[$v['category_id']] = true;
            }
        }
        
        $search = array(
            'key'       => $q_search_key,
            'condition' => $q_search_condition,
            'value'     => $q_search_value,
        );
        
        $products = DB::table('product as p')
        ->leftJoin('product_category as b', 'b.id', '=', 'p.category_id')
        ->orderBy('b.lft', 'ASC')
        ->orderBy('p.sort', 'ASC')
        ->where('p.status', 1)
        ->where('b.type', 1)
        ->selectRaw('p.*,b.name as category_name');
        
        if ($q_category_id > 0) {
            $q_category = DB::table('product_category')->where('id', $q_category_id)->first(['lft', 'rgt']);
            $products->whereRaw('b.lft BETWEEN '.$q_category['lft'].' AND '.$q_category['rgt']);
        }
        
        // 搜索产品
        if ($search['key'] && $search['value']) {
            $value = $search['condition'] == 'like' ? '%'.$search['value'].'%' : $search['value'];
            $products->whereRaw($search['key'].' '.$search['condition'].'?', [$value]);
        }
        
        $res = $products->get();
        $res = array_by($res);

        $rows = [];

        foreach ($res as $k => $v) {

            // 按类别排序时使用，目前没有更好的办法
            $category_id = $v['category_id'];

            // 计算单价类型以及自定义单价
            $v['price'] = $priceItem[$k] > 0 ? number_format($priceItem[$k], 2) : $v['price'.$client['post']];
            
            // 客户登录时也只显示他被授权的产品
            if (Auth::user()->role->name == 'client') {
                if (isset($selectCategoryItem[$v['category_id']]) or $v['authority'] == 1) {
                    $rows[$k] = $v;
                }
            } else {
                $rows[$k] = $v;
            }
        }
        
        // 筛选类别如果类别编号不在之前的类别编号中就删除他
        $categorys = DB::table('product_category')->where('type', 1)->orderBy('lft', 'asc')->get();
        $categorys = array_nest($categorys);

        foreach ($categorys as $category) {
            if ($selectCategoryItem[$category['id']]) {
                foreach ($category['parent'] as $parent_id) {
                    $categorys[$parent_id]['selected'] = true;
                }
            }
        }

        $orderType = DB::table('order_type')
        ->orderBy('lft', 'ASC')
        ->get();
        $orderType = array_by($orderType);

        $this->layout = 'layouts.empty';
        return $this->display(array(
            'categoryItem' => $categoryItem,
            'productItem'  => $productItem,
            'orderType'    => $orderType,
            'priceItem'    => $priceItem,
            'categorys'    => $categorys,
            'rows'         => $rows,
            'client'       => $client,
            'query'        => $query,
        ));
    }

    // 更新产品字段数据
    public function product_editAction()
    {
        if ($post = $this->post()) {
            $order_id = Input::get('order_id', 0);
            $table = $order_id == 0 ? 'order_temp' : 'order_data';

            if (isset($_POST['updated'])) {
                $updated = json_decode($_POST['updated'], true);
                foreach ($updated as $v) {
                    if ($order_id == 0) {
                        $product = DB::table('product')->where('id', $v['product_id'])->first();
                        $v['amount'] = (int)$v['level_amount'] * $product['level_amount'];
                    }

                    // 订单助理就不转换整型
                    $data = array(
                        'price'       => $v['price'],
                        'inventory'   => $v['inventory'],
                        'amount'      => $v['amount'],
                        'fact_amount' => $v['fact_amount'],
                        'batch_number'=> $v['batch_number'],
                        'content'     => $v['content'],
                    );
                    DB::table($table)->where('id', $v['id'])->update($data);
                }
            }

            if (isset($_POST['deleted'])) {
                $deleted = json_decode($_POST['deleted'], true);
                foreach ($deleted as $v) {
                    // 标记产品已经删除
                    $data['deleted'] = 1;
                    DB::table($table)->where('id', $v['id'])->update($data);
                }
            }
            $r = array('status'=>'1');
            exit(json_encode($r));
        }
    }

    /**
     * 订单删除
     */
    public function product_deleteAction()
    {
        $id = Input::get('id', 0);
        $table = Input::get('table', '');
        if (empty($table)) {
            exit('0');
        }

        if ($id > 0) {
            $_table = $table == 'add' ? 'order_temp' : 'order_data';
            DB::table($_table)->where('id', $id)->delete();
            exit('1');
        }
        exit('0');
    }
 
    // 订单监控管理，历史记录计算
    public function monitorAction()
    {
        $page = Input::get('page', 1);
        
        //筛选专用函数
        $selects = select::head();
        $where = $selects['where'];

        $select_key = array('step_id'=>0,'number'=>'','sdate'=>'','edate'=>date('Y-m-d'));
        foreach ($select_key as $k => $v) {
            $selects['select'][$k] = Input::get($k, $v);
        }
        extract($selects['select'], EXTR_PREFIX_ALL, 'select');

        $model = DB::table('order as o');
        if ($select_number) {
            $model->where('o.number', 'LIKE', $select_number);
        }
        
        if ($select_step_id > 0) {
            $model->where('o.flow_step_id', $select_step_id);
        }

        if ($select_sdate) {
            $model->whereRaw('FROM_UNIXTIME(o.add_time,"%Y-%m-%d") >= ?', [$select_sdate]);
        }

        if ($select_edate) {
            $model->whereRaw('FROM_UNIXTIME(o.add_time,"%Y-%m-%d") <= ?', [$select_edate]);
        }

        $model->leftJoin('order_data as od', 'od.order_id', '=', 'o.id')
        ->leftJoin('user as c', 'c.id', '=', 'o.client_id')
        ->whereRaw($where);
        
        $total = $model->count('o.id');
     
        $data = $model->orderBy('o.id', 'DESC')
        ->groupBy('o.id')
        ->selectRaw('SUM(od.fact_amount) sum_fact_amount,SUM(od.amount) sum_amount,o.pay_time,o.fax_at,o.delivery_time,o.arrival_time,o.status,o.number,o.flow_step_id,o.add_time,c.nickname company_name,o.id')
        ->forPage($page, 15)
        ->get();
        
        $rows = Paginator::make($data, $total, 15)->appends($selects['select']);

        // 审核信息模板
        $order_config = config('order');
        $query = url().'?'.http_build_query($selects['select']);
        //视图设置
        return $this->display(array(
            'audit_config' => $order_config['audit'],
            'rows'         => $rows,
            'query'        => $query,
            'selects'      => $selects,
        ));
    }

    // 订单监控管理，历史记录计算
    public function monitor_dataAction()
    {
        $id = Input::get('id', 0);
        if ($id <= 0) {
            return $this->error('订单编号丢失。');
        }

        $order_data = DB::table('order_data')->where('order_id', $id)->get();
        $order_data = array_by($order_data, 'product_id');

        $product = DB::table('product')->get();
        $product = array_by($product);

        //视图设置
        return $this->display(array(
            'order_data'    => $order_data,
            'product'       => $product,
        ));
    }

    public function addAction()
    {
        // 筛选专用函数
        $selects = select::head();
        $where = $selects['where'];

        $client_id = (int)$selects['select']['client_id'];
        $selects['select']['step_id'] = Input::get('step_id', 1);

        // 保存订单
        if ($post = $this->post()) {
            $res = DB::table('order_temp as ot')
            ->leftJoin('product as p', 'p.id', '=', 'ot.product_id')
            ->where('ot.client_id', $post['client_id'])
            ->where('ot.deleted', 0)
            ->orderBy('p.sort', 'ASC')
            ->selectRaw('p.*,ot.price,ot.type,ot.amount,ot.inventory')
            ->get();
            
            if (empty($res)) {
                return $this->error('没有产品不能保存订单。');
            }

            if (empty($post['invoice_company'])) {
                return $this->error('发票单位名称或打款人必须填写。');
            }
            
            if (empty($post['transport_car_type'])) {
                return $this->error('送货车长度必须选择。');
            }

            if (empty($post['invoice_type'])) {
                return $this->error('发票类型必须选择。');
            }

            if (empty($post['order_people'])) {
                return $this->error('下单人姓名必须填写。');
            }

            if (empty($post['order_people_phone'])) {
                return $this->error('下单人电话必须填写。');
            }

            $total_num = DB::table('order')->count('id');
            $mian                 = $post;
            $mian['client_id']    = $post['client_id'];
            $mian['flow_step_id'] = $selects['select']['step_id'];
            $mian['number']       = date('Y-m').'-'.($total_num + 1);
            $mian['description']  = $post['description'];
            $mian['add_time']     = time();
            
            unset($mian['sms']);
            
            $insert_id = DB::table('order')->insertGetId($mian);

            foreach ($res as $k => $v) {
                $data = array(
                    'order_id'   => $insert_id,
                    'client_id'  => $post['client_id'],
                    'product_id' => $v['id'],
                    'price'      => $v['price'],
                    'amount'     => $v['amount'],
                    'type'       => $v['type'],
                    'inventory'  => $v['inventory'],
                );
                DB::table('order_data')->insert($data);
            }
            DB::table('order_temp')->where('client_id', $post['client_id'])->delete();

            // 短信提醒
            if ($post['sms'] == 'on') {
                $client = DB::table('user')->where('id', $post['client_id'])->first();
                // 获取订单数量统计
                $amount = DB::table('order_data')->whereRaw('order_id=? and deleted=0', [$insert_id])->sum('amount');
                $sender = array(
                    'sender'    => $client['nickname'],
                    'time'      => date('Y-m-d H:i'),
                    'amount'    => $amount,
                    'client_id' => $post['client_id'],
                );
                $r = $this->sms(0, $sender);
            }
            return $this->success('view', ['id' => $insert_id], '恭喜你，订单提交成功。');
        }

        // 客户扩展信息
        $client = DB::table('user')->where('id', $client_id)->first();

        // 客户开票公司信息
        $client['bank'] = DB::table('customer_bank')->where('client_id', $client_id)->get();
        
        $order_config = config('order');
        $query = url().'?'.http_build_query($selects['select']);
        $flow = $order_config['audit'][0];
        $flow['fields']['hidden'] = [];
        // 获取默认角色编辑字段
        foreach ($order_config['defaults'] as $key => $defaults) {
            foreach ($defaults as $field => $roles) {
                if (in_array(Auth::user()->role->name, $roles)) {
                    $flow['fields'][$key][] = $field;
                }
            }
        }

        $customer = Customer::find($client_id);

        //视图设置
        return $this->display(array(
            'audit_config' => $order_config['audit'],
            'flow'         => $flow,
            'client'       => $client,
            'query'        => $query,
            'selects'      => $selects,
            'order'        => $order,
            'customer'     => $customer,
            'table'        => 'add',
        ));
    }

    /**
     * 查看单个订单,浏览订单详情
     */
    public function viewAction()
    {
        if ($post = $this->post()) {
            if (isset($post['number']) && empty($post['number'])) {
                return $this->error('订单号必须填写。');
            }

            if (isset($post['invoice_type']) && empty($post['invoice_type'])) {
                return $this->error('开票类型必须选择。');
            }

            if (isset($post['invoice_company']) && empty($post['invoice_company'])) {
                return $this->error('开票抬头必须选择或填写。');
            }
            
            $order_id = $post['order_id'];
            
            unset($post['order_id']);
            
            $order = DB::table('order')->where('id', $order_id)->first();
            if ($order['add_time'] == 0) {
                $post['add_time'] = time();
            }

            DB::table('order')->where('id', $order_id)->update($post);
            
            return $this->success('view', ['id'=> $order_id], '订单编辑操作成功');
        }

        // 获得订单id
        $id = (int)Input::get('id', 0);

        // 订单信息
        $order = DB::table('order')->where('id', $id)->first();

        if (empty($order)) {
            return $this->error('没有订单数据。');
        }

        if (Auth::id() != $order['client_id'] && Auth::user()->role->name == 'client') {
            return $this->error('非法请求资源。');
        }

        $audit_info = DB::table('order_audit as a')
        ->leftJoin('user as b', 'b.id', '=', 'a.add_user_id')
        ->leftJoin('role as c', 'c.id', '=', 'b.role_id')
        ->where('a.order_id', $id)
        ->orderBy('a.add_time', 'ASC')
        ->selectRaw('a.*, b.nickname, c.title AS role_name')
        ->get();
        
        // 当前订单表的客户信息
        $client = DB::table('user')->where('id', $order['client_id'])->first();

        // 获得当前审核模板
        $order_config = config('order');
        $audit_now_config = $order_config['audit'][$order['flow_step_id']];
        $audit_now_config['fields']['hidden'] = [];

        // 角色匹配时才可编辑
        if (Auth::user()->role->name != $audit_now_config['role']) {
            $audit_now_config['fields']['edit'] = [];
        }

        // 获取默认角色编辑字段
        foreach ($order_config['defaults'] as $key => $defaults) {
            foreach ($defaults as $field => $roles) {
                if (in_array(Auth::user()->role->name, $roles)) {
                    $audit_now_config['fields'][$key][] = $field;
                }
            }
        }

        // 获取运输信息
        $transport = DB::table('order_transport')->where('order_id', $id)->first();

        // 客户开票公司信息
        $client['bank'] = DB::table('customer_bank')->where('client_id', $order['client_id'])->get();
        
        //登录者是客户就禁用修改订单
        $access = $this->access;

        if (Auth::user()->role->name == 'client') {
            unset($access['product_edit'], $access['product_add'], $access['product_delete']);
        }

        $selects = select::head();
        $where = $selects['where'];
        if ($where == 1) {
            $where = '';
        }

        $first = DB::table('order as o')
        ->LeftJoin('user as c', 'c.id', '=', 'o.client_id');
        if ($where) {
            $first->whereRaw($where);
        }
        $after = clone $first;

        $page['first'] = $first->whereRaw('o.id < ?', [$id])
        ->orderBy('o.id', 'desc')
        ->first(['o.id']);

        $page['after'] = $after->whereRaw('o.id > ?', [$id])
        ->whereRaw('o.id > ?', [$id])
        ->orderBy('o.id', 'asc')
        ->first(['o.id']);
        
        $customer = Customer::find($order['client_id']);

        // 当前客户未完成的促销列表
        $promotions = DB::table('promotion')
        ->where('customer_id', $customer['id'])
        ->where('status', 0)
        ->get();

        // 视图设置
        return $this->display(array(
            'transport'  => $transport,
            'order'      => $order,
            'client'     => $client,
            'audit_info' => $audit_info,
            'user_data'  => $user_data,
            'pagelink'   => $pagelink,
            'query'      => $query,
            'selects'    => $selects,
            'table'      => 'edit',
            'page'       => $page,
            'flow'       => $audit_now_config,
            'access'     => $access,
            'customer'   => $customer,
            'promotions' => $promotions,
        ));
    }

    /**
     * 审核信息输入方法
     *
     */
    public function auditAction()
    {
        //订单配置数据
        $order_config = config('order');

        if ($post = $this->post()) {
            $order = DB::table('order as o')
            ->LeftJoin('client', 'client.user_id', '=', 'o.client_id')
            ->where('o.id', $post['order']['id'])
            ->first(['o.id','o.status','client.circle_id','o.client_id','o.pay_time','o.delivery_time','o.number','o.flow_step_id','o.add_time']);

            // 当前审核信息
            $flowStep = $order_config['audit'][$order['flow_step_id']];

            if (Auth::user()->role->name != $flowStep['role']) {
                exit('已经审核过了或不是你审核状态。');
            }

            if (Auth::user()->role->name == 'salesman') {

                // 客户圈权限
                $circle = select::circleCustomer();
                if (!in_array($order['circle_id'], $circle['owner_user'])) {
                    exit('只有客户圈审阅人才能审核。');
                }
            }

            $data = array();
            foreach ($post as $key => $value) {
                if (is_string($value)) {
                    continue;
                }

                foreach ($value as $k => $v) {
                    if ((strpos($k, '_time') > 0 || strpos($k, '_at') > 0) && $v) {
                        $v = strtotime($v);
                    }

                    $form = $flowStep['forms'][$k];
                    if ($form['required'] == 1 && empty($v)) {
                        exit($form['title'].'必须填写。');
                    }

                    if (is_array($v)) {
                        $content = array();
                        foreach ($v as $k2 => $v2) {
                            $form = $flowStep['forms'][$k2];

                            // 审核意见
                            if ($k2 == 'text' && empty($v2)) {
                                exit('审核意见必须填写。');
                            }

                            // 检查必填
                            if ($form['required'] == 1 && empty($v2)) {
                                exit($form['title'].'必须填写。');
                            }

                            if ($v2 && $form) {
                                $content[] = $form['title'].': '.$v2;
                            } elseif ($v2) {
                                $content[] = $v2;
                            }
                        }
                        $v = join('，', $content);
                    }
                    $data[$key][$k] = strtolower($v);
                }
            }

            // 出库处理
            if ($flowStep['outStock'] == 1) {
                // 获取订单单品
                $stock_data = DB::table('order_data')
                ->where('order_id', $order['id'])
                ->where('deleted', 0)
                ->groupBy('product_id')
                ->selectRaw('product_id, SUM(fact_amount) amount')
                ->get();
                $stock_data = array_by($stock_data, 'product_id');
                
                // 自动创建销售出库单
                Stock::setAdd($stock_data, 3, 2);
                
                unset($stock_data);
            }

            // 其他处理
            $flowData      = $data['flow'];
            $orderData     = $data['order'];
            $transportData = $data['transport'];

            // 处理物流信息
            if ($transportData) {
                $transportData['client_id'] = $order['client_id'];
                $transportData['order_id'] = $order['id'];

                $res = DB::table('order_transport')->where('order_id', $order['id'])->first();
                if ($res) {
                    DB::table('order_transport')->where('order_id', $order['id'])->update($transportData);
                } else {
                    DB::table('order_transport')->insert($transportData);
                }
            }

            // 订单编号到审核表
            $flowData['order_id'] = $order['id'];
            // 审核类型
            $stepState = $orderData['flow_step_state'];
            // 审核步骤编号
            $stepId = $orderData[$stepState];

            // 流程编号
            $flowData['step_state'] = $stepState;

            // 如果是拒绝，那就没有步骤编号
            if ($stepId > 0) {
                $orderData['flow_step_id'] = $stepId;
                $flowData['step_id'] = $stepId;
            }
            
            unset($orderData['end'], $orderData['last'], $orderData['next']);
            
            $flowData['add_user_id'] = Auth::id();
            $flowData['add_time']    = time();
            
            // 写入订单信息
            DB::table('order')->where('id', $order['id'])->update($orderData);

            // 写入审核信息
            DB::table('order_audit')->insert($flowData);

            $user = DB::table('user')->where('id', $order['client_id'])->first();

            // 短信通知
            if ($post['sms'] == 'true') {
                
                // 获取订单详情统计
                $orderinfo = DB::table('order_data')
                ->where('order_id', $order['id'])
                ->where('deleted', 0)
                ->selectRaw('SUM(fact_amount) as fact_amount')
                ->first();
                
                $sender = array(
                    // 发送人
                    'sender'       => $user['nickname'],
                    // 订单产品数量
                    'amount'       => $orderinfo['amount'],
                    // 订单发货数量
                    'fact_amount'  => $orderinfo['fact_amount'],
                    // 订单创建时间
                    'add_time'     => date('Y-m-d H:i:s', $order['add_time']),
                    // 订单发货时间
                    'delivery_time'=> date('Y-m-d H:i:s', $order['delivery_time']),
                    // 此订单的经销商代码
                    'client_id'    => $order['client_id'],
                );
                // 审核内容作为短信内容
                $this->sms($order['flow_step_id'], $sender, null, true);
            }
            exit('1');
        }

        $order_id = (int)Input::get('order_id');
        if ($order_id <= 0) {
            return $this->error('订单编号不正确。');
        }

        // 获取订单主表信息
        $order = DB::table('order as o')
        ->selectRaw('o.status,o.client_id,o.pay_time,o.number,o.flow_step_id,o.add_time,o.id')
        ->where('o.id', $order_id)
        ->first();

        // 获取此订单的物料统计
        $materielCount = DB::table('order_data as a')
        ->leftJoin('product as b', 'b.id', '=', 'a.product_id')
        ->where('a.deleted', 0)
        ->where('a.order_id', $order['id'])
        ->where('b.warehouse_id', 3)
        ->selectRaw('SUM(a.fact_amount) as amount')
        ->count('amount');
        
        // 判断是否是客户第一张订单
        $clientOrderCount = DB::table('order')->where('client_id', $order['client_id'])->count();
        $materielCount = $clientOrderCount == 1 ? 1 : $materielCount;

        // 判断客户是否必须要总经理审批
        $client = DB::table('client')->where('id', $order['client_id'])->first();
        $materielCount = $client['order_approve'] == 1 ? 1 : $materielCount;

        // 暂时不需要总经理审批订单
        $materielCount = 0;

        // 当前审核信息
        $flowStep = $order_config['audit'][$order['flow_step_id']];

        $flowSteps = array();
        foreach ($order_config['audit'] as $k => $v) {
            if (in_array($k, (array)$flowStep['last_step_ids'])) {
                $flowSteps['last'][$k] = $v['name'];
            }

            if (in_array($k, (array)$flowStep['next_step_ids'])) {
                $flowSteps['next'][$k] = $v['name'];
            }
        }
        $transport = DB::table('order_transport')->where('order_id', $order_id)->first();

        $this->layout = 'layouts.empty';
        return $this->display(array(
            'transport'     => $transport,
            'order'         => $order,
            'flow'          => $flowStep,
            'steps'         => $flowSteps,
            'materielCount' => $materielCount,
        ), 'audit_input');
    }

    // 订单短信发送方法
    public function sms($flow_step_id, $value, $sms_text = null, $error = false)
    {
        $order_config = config('order');
        $audit_config = $order_config['audit'][$flow_step_id];

        if (empty($audit_config['sms'])) {
            return '没有短信发送角色信息';
        }

        // 获取订单助理人员列表，同组的每一个人发生一条信息，目前没有筛选是否有不需要接受短信的选项
        foreach ($audit_config['sms'] as $k => $v) {
            $users = array();
            //客户，或者是业务员，就直接查询单个人, 根据不同的用户组进行筛选
            switch ($k) {
                // 只能发送给一个经销商，这里需要根据订单数据发送给客户
                case 'client':
                    $users[] = DB::table('user')->where('id', $value['client_id'])->first();

                    break;
                case 'salesman':
                    // 这里需要根据订单的数据发送给业务员
                    $client = DB::table('user')->where('id', $value['client_id'])->first();
                    $users[] = DB::table('user')->where('id', $client['salesman_id'])->first();

                    break;
                default:
                    $users = DB::table('user')
                    ->leftJoin('role', 'role.id', '=', 'user.role_id')
                    ->where('role.name', $k)
                    ->get(['user.*']);
            }
            foreach ($users as $user) {
                // 替换内容中的信息
                $repalce = array(
                    // 替换经销商名称
                    '%sender%'        => $value['sender'],
                    // 订单创建时间
                    '%created%'       => $value['add_time'],
                    // 发货时间
                    '%delivery_time%' => $value['delivery_time'],
                    // 发货数量
                    '%fact_amount%'   => $value['fact_amount'],
                    // 订单数量
                    '%amount%'        => $value['amount'],
                    // 替换当前时间
                    '%time%'          => date('Y-m-d H:i', time()),
                );
                // 如开启了自定义短信内容，将审核内容赋给短信内容
                if (empty($sms_text)) {
                    // 使用默认定义的短信内容，执行替换
                    $text = str_replace(array_keys($repalce), array_values($repalce), $v);
                } else {
                    $text = $sms_text;
                }
                // $user['mobile'] = '15182223008';
                // 发送短信
                if (empty($user['mobile'])) {
                    exit('用户档案中没有手机号码。');
                }

                Notification::sms([$user['mobile']], $text);
                return true;
            }
        }
    }

    /**
     * 配送信息
     */
    public function transportAction()
    {
        if ($post = $this->post()) {

            //附加其他参数
            $transport = $post['transport'];
            $transport['order_id']  = $post['order_id'];
            $transport['client_id'] = $post['client_id'];

            //更新订单表，对时间进行特殊处理
            foreach ($transport as $key => $value) {
                if (strpos($key, '_time') > 0 && $value) {
                    $transport[$key] = strtotime($value);
                }
            }

            // 更新发货时间
            $order = $post['order'];
            $order['delivery_time'] = strtotime($order['delivery_time']);
            DB::table('order')->where('id', $post['order_id'])->update($order);

            $_transport = DB::table('order_transport')->where('order_id', $post['order_id'])->first();
            // 物流信息
            if ($_transport['order_id'] > 0) {
                DB::table('order_transport')->where('order_id', $post['order_id'])->update($transport);
            } else {
                DB::table('order_transport')->insert($transport);
            }
            return $this->back('transport', ['order_id'=>$post['order_id'],'client_id'=>$post['client_id']], "物流信息更新成功。");
        }

        $id = Input::get('order_id');
        $client_id = Input::get('client_id');

        if ($id <= 0) {
            return $this->error('对不起，订单编号丢失，无法完成请求。');
        }
        if ($client_id <= 0) {
            return $this->error('对不起，经销商编号丢失，无法完成请求。');
        }

        // 查询当前配送方式
        $transport = DB::table('order_transport')->where('order_id', $id)->first();

        // 获取订单主表信息
        $order = DB::table('order')
        ->where('id', $id)
        ->first();

        // 订单审核模板
        $order_config = config('order');
        $audit_now_config = $order_config['audit'][$order['flow_step_id']];

        $this->layout = 'layouts.empty';
        return $this->display(array(
            'transport' => $transport,
            'order'     => $order,
        ));
    }

    /**
     * 订单合并，可合并多个同客户的订单
     */
    public function mergeAction()
    {
        $post = $this->post();
        $count = $post['order_id'];

        if ($count > 0) {
            if ($count >= 2) {
                $order = DB::table('order as o')
                ->leftJoin('order_data as oi', 'oi.order_id', '=', 'o.id')
                ->whereIn('o.id', $post['order_id'])
                ->groupBy('o.id')
                ->selectRaw('o.client_id,o.add_time,o.id,o.number,SUM(oi.amount) as amount,SUM(oi.fact_amount) as fact_amount')
                ->get();

                // 判断选中的是几个客户
                $client = array();
                foreach ($order as $row) {
                    $client[$row['client_id']] = $row['client_id'];
                }
                if (count($client) > 1) {
                    return $this->error('合并必须是同一客户订单。');
                }
            }
            // 视图设置
            return $this->display(array(
                'order' => $order,
                'json'  => join(',', $post['order_id']),
            ));
        } elseif ($post['json']) {
            if ($post['subject'] <= 0) {
                return $this->error('主订单必须选择。');
            }

            $json = explode(',', $post['json']);
            $order_data = DB::table('order_data')
            ->whereIn('order_id', $json)
            ->groupBy('id')
            ->groupBy('type')
            ->selectRaw('*,SUM(amount) as amount,SUM(fact_amount) as fact_amount')
            ->get();

            // 删除所有订单数据
            foreach ($json as $order_id) {
                
                // 订单主表不删除主订单
                if ($post['subject'] != $order_id) {
                    DB::table('order')->where('id', $order_id)->delete();
                    DB::table('order_transport')->where('order_id', $order_id)->delete();
                    DB::table('order_audit')->where('order_id', $order_id)->delete();
                }
                // 删除订单两个子表
                DB::table('order_data')->where('order_id', $order_id)->delete();
            }

            // 插入订单子表信息
            foreach ($order_data as $row) {
                $row['order_id'] = $post['subject'];
                DB::table('order_data')->insert($row);
            }
            return $this->success('view', ['id'=> $post['subject']], '恭喜你，订单合并成功。');
        }
    }

    /**
     * 订单分割
     */
    public function partAction()
    {
        $order_id = Input::get('order_id');

        if (Request::method() == 'POST') {
            $gets = Input::get();

            $order_id  = $gets['order_id'];

            $goods_ids = $gets['goods_ids'];

            if (empty($goods_ids)) {
                return $this->error('最少选择一个产品。');
            }

            $order     = DB::table('order')->where('id', $order_id)->first();
            $transport = DB::table('order_transport')->where('order_id', $order_id)->first();
            $audits    = DB::table('order_audit')->where('order_id', $order_id)->get();

            // 分单写入
            $order['id']     = 0;
            $order['number'] = $order['number'].'-拆分';
            $insert_id = DB::table('order')->insertGetId($order);

            DB::table('order_data')->whereIn('id', $goods_ids)->update(['order_id' => $insert_id]);

            $transport['id']       = 0;
            $transport['order_id'] = $insert_id;
            DB::table('order_transport')->insert($transport);

            foreach ($audits as $key => $audit) {
                $audit['id']       = 0;
                $audit['order_id'] = $insert_id;
                DB::table('order_audit')->insert($audit);
            }

            return $this->success('view', ['id'=> $order_id], '恭喜你，拆分订单成功。');
        }
 
        $datas = DB::table('order_data')
         ->leftJoin('product', 'order_data.product_id', '=', 'product.id')
         ->leftJoin('product_category', 'product_category.id', '=', 'product.category_id')
         ->where('order_data.order_id', $order_id)
         ->where('order_data.deleted', 0)
         ->selectRaw('order_data.*,product.name,product.spec')
         ->orderBy('product_category.lft', 'ASC')
         ->orderBy('product.sort', 'ASC')
         ->get();

        $order = DB::table('order')->where('id', $order_id)->first();

        // 视图设置
        return $this->display(array(
            'order' => $order,
            'datas' => $datas,
        ));
    }

    /**
     * 订单打印功能
     */
    public function printAction()
    {
        $id = Input::get('order_id', 0);
        if ($id <= 0) {
            return $this->error('订单编号不正确。');
        }

        // 查询当前配送方式
        $transport = DB::table('order_transport')->where('order_id', $id)->first();

        // 获取订单主表信息
        $order = DB::table('order')->where('id', $id)->first();

        //当前订单表的客户信息
        $client = DB::table('user')->where('id', $order['client_id'])->first();

        // 生成订单列表
        $model = DB::table('order_data as oi')
        ->leftJoin('product as p', 'p.id', '=', 'oi.product_id')
        ->leftJoin('product_category as pc', 'pc.id', '=', 'p.category_id')
        ->leftJoin('warehouse as psw', 'psw.id', '=', 'p.warehouse_id')
        ->where('oi.order_id', $id)
        ->where('oi.deleted', 0)
        ->orderBy('pc.lft', 'ASC')
        ->orderBy('p.sort', 'ASC')
        ->selectRaw('p.*,psw.type warehouse_type,oi.id,oi.price,oi.type,oi.amount,oi.fact_amount,oi.content,oi.batch_number,psw.advert');
        
        $warehouse_id = Input::get('warehouse_id', 0);
        if ($warehouse_id > 0) {
            $model->where('p.warehouse_id', $warehouse_id);
        }

        $orderinfo = $model->get();

        // 取得本订单单品总数
        $totalSum = sizeof($orderinfo);

        $category_dis = Input::get('category');
        if (is_array($category_dis)) {
            $data = array();
            foreach ($orderinfo as $k => $v) {
                if (isset($category_dis[$v['category_id']])) {
                    $data[$k] = $v;
                }
            }
            $orderinfo = $data;
            // 取得当前筛选品类单品总数
            $nowTotalSum = sizeof($data);
        }

        $selects['total'] = array(
             $totalSum,
             $nowTotalSum,
        );
        $selects['select']['category'] = $category_dis;
        $selects['select']['warehouse_id'] = $warehouse_id;
        $selects['select']['order_id'] = $id;

        $order_config = config('order');
        $product_type = $order_config['type'];

        $_templates = [
            'default'   => '销售订单',
            'follow'    => '随货单',
            'delivery'  => '发货单',
            'transport' => '发货通知单',
            'shipping'  => '出货单',
        ];

        // 获取当前角色配置的特殊限制
        $flowPrint = array();
        $flow = $order_config['audit'][$order['flow_step_id']];

        if ($flow['role'] == Auth::user()->role->name) {
            $flowPrint = $flow['prints'];
        }

        $rolePrint = $order_config['role']['print'][Auth::user()->role_id];

        $tpl = array_merge((array)$flowPrint, (array)$rolePrint);

        $templates = array();
        foreach ($_templates as $k => $v) {
            if (in_array($k, $tpl)) {
                $templates[$k] = $v;
            }
        }

        $selects['select']['tpl'] = Input::get('tpl', array_shift($tpl));

        if (empty($templates)) {
            return $this->alert('没有可用的打印模板。');
        }

        $query = url().'?'.http_build_query($selects['select']);

        $categorys = ProductCategory::type('sale')->orderBy('lft', 'asc')->get()->toNested();

        // 订单类型科目
        $orderType = DB::table('order_type')->get();
        $orderType = array_by($orderType);

        $warehouses = Warehouse::type('sale')->orderBy('lft', 'asc')->get()->toNested();

        $customer = Customer::find($order['client_id']);

        return $this->display([
            'title'          => $_templates[$selects['select']['tpl']],
            'salesman'       => $salesman,
            'client_contact' => $client_contact,
            'client'         => $client,
            'orderType'      => $orderType,
            'transport'      => $transport,
            'product_type'   => $product_type,
            'order'          => $order,
            'orderinfo'      => $orderinfo,
            'select'         => $selects,
            'query'          => $query,
            'templates'      => $templates,
            'categorys'      => $categorys,
            'warehouses'     => $warehouses,
            'customer'       => $customer,
            'sizes'          => $sizes,
        ], 'order.print.'.$selects['select']['tpl'], 'order.print.master');
    }

    /**
     * 订单导出功能,将订单导入用友
     *
     */
    public function exportAction()
    {
        $order_id = Input::get('id', 0);

        // 检查数据，和登录组
        if ($order_id <= 0) {
            return $this->error('缺少订单编号，请检查后再导出。');
        }
        
        $order = DB::table('order as o')
        ->leftJoin('user as u', 'u.id', '=', 'o.client_id')
        ->leftJoin('client', 'u.id', '=', 'client.user_id')
        ->where('o.id', $order_id)
        ->selectRaw('o.*,u.username AS customer,client.circle_id')
        ->first();

        $circle = DB::table('customer_circle')
        ->where('id', $order['circle_id'])
        ->first();

        $orderinfo = DB::table('order_data as oi')
        ->leftJoin('product as p', 'p.id', '=', 'oi.product_id')
        ->where('oi.order_id', $order_id)
        ->where('oi.deleted', 0)
        ->selectRaw('oi.price,oi.fact_amount,oi.type,p.stock_code,p.weight,oi.amount')
        ->get()->toArray();

        if (empty($orderinfo) && empty($order)) {
            return $this->error('没有数据，请检查后再导出。');
        }
        
        $created = date('Y-m-d H:i:s', $order['add_time']);
        // &验证码|备注|随货同行|经销商代码|订单日期|订单号  &订单单品类型|存货代码|单价|重量|数量#
        $r = "&123456|{$order['description']}|{$order['goods']}|{$order['customer']}|{$created}|{$circle['name']}|{$order['number']}";
        
        // 订单类型科目
        $orderType = DB::table('order_type')->get();
        $orderType = array_by($orderType);
        
        foreach ($orderinfo as $v) {
            // 订单单品类型
            $type = $orderType[$v['type']];

            // &订单单品类型|存货代码|单价|重量|数量
            $r .= "&{$type['type']}|{$type['title']}|{$v['stock_code']}|{$v['price']}|{$v['weight']}|{$v['amount']}";
        }
        $r .= '#';
        $r = mb_convert_encoding($r, "gbk", "utf-8");
        
        // 发送数据到用友
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, config('default.ufida.export_order'));
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $r);
        $data = curl_exec($ch);
        curl_close($ch);

        if (!$data) {
            return $this->error('连接用友插件失败。');
        }

        // 将gbk转换成UTF-8
        $data = mb_convert_encoding($data, "utf-8", "gbk");
        $r = explode('|', $data);
        if ($r[1] == '1') {
            $d['export'] = $order['export'] + 1;
            DB::table('order')->where('id', $order_id)->update($d);
            
            return $this->success('index', '恭喜你，数据导入成功。');
        } else {
            return $this->error('数据导入失败，用友返回的错误信息：'.$r[2]);
        }
    }

    /**
     * 订单同步到用友(外账)
     *
     */
    public function syncyonyouAction()
    {
        $order_id = Input::get('id', 0);

        $order = DB::table('order as o')
        ->leftJoin('user as customer', 'customer.id', '=', 'o.client_id')
        ->leftJoin('user as salesman', 'salesman.id', '=', 'customer.salesman_id')
        ->leftJoin('client', 'customer.id', '=', 'client.user_id')
        ->where('o.id', $order_id)
        ->selectRaw('o.*,customer.username as customer_user,customer.nickname as customer_name,salesman.nickname as salesman_name,client.circle_id')
        ->first();

        $circle = DB::table('customer_circle')
        ->where('id', $order['circle_id'])
        ->first();

        $orderinfo = DB::table('order_data as oi')
        ->leftJoin('product as p', 'p.id', '=', 'oi.product_id')
        ->where('oi.order_id', $order_id)
        ->where('oi.deleted', 0)
        ->selectRaw('oi.price,oi.amount,oi.fact_amount,oi.type,p.stock_number,p.weight')
        ->get()->toArray();

        if (empty($orderinfo) || empty($order)) {
            return $this->error('没有数据，请检查后再导出。');
        }
        
        $created = date('Y-m-d H:i:s', $order['add_time']);
        // &验证码|备注|随货同行|业务员名称|客户代码|客户名称|订单日期|订单号  &订单单品类型|存货代码|单价|重量|数量#
        $r = "&123456|{$order['description']}|{$order['goods']}|{$order['salesman_name']}|{$order['customer_user']}|{$order['invoice_company']}|{$created}|{$circle['name']}|{$order['number']}";
        
        // 订单类型科目
        $orderType = DB::table('order_type')->get();
        $orderType = array_by($orderType);
        
        foreach ($orderinfo as $v) {
            // 订单单品类型
            $type = $orderType[$v['type']];

            // &订单单品类型|存货代码|单价|重量|数量
            $r .= "&{$type['type']}|{$type['title']}|{$v['stock_number']}|{$v['price']}|{$v['weight']}|{$v['amount']}";
        }
        $r .= '#';

        $r = mb_convert_encoding($r, "gbk", "utf-8");
        
        // 发送数据到用友
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, config('default.ufida.sync_yonyou'));
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $r);
        $data = curl_exec($ch);
        curl_close($ch);

        if (!$data) {
            return $this->error('连接用友插件失败。');
        }

        // 将gbk转换成UTF-8
        $data = mb_convert_encoding($data, "utf-8", "gbk");
        $r = explode('|', $data);
        if ($r[1] == '1') {
            $d['yonyou'] = $order['yonyou'] + 1;
            DB::table('order')->where('id', $order_id)->update($d);
            
            return $this->success('index', '恭喜你，数据导入成功。');
        } else {
            return $this->error('数据导入失败，用友返回的错误信息：'.$r[2]);
        }
    }

    /**
     * 订单废除方法
     */
    public function repealAction()
    {
        // 获取会话信息
        $id = Input::get('id', 0);
        if ($id > 0) {
            DB::table('order')->where('id', $id)->update(['status' => 0]);
            return $this->success('index', '恭喜你，订单废除成功。');
        }
    }

    /**
     * 同步订单
     */
    public function syncAction()
    {
        if ($id = Input::get('id')) {
            $order = DB::table('order')->where('id', $id)->first();
            $transport = DB::table('order_transport')->where('order_id', $id)->first();
            $client = DB::table('user')->where('id', $order['client_id'])->first();
            
            $order_data = DB::table('order_data as oi')
            ->leftJoin('product as p', 'p.id', '=', 'oi.product_id')
            ->where('oi.deleted', 0)
            ->where('oi.order_id', $id)
            ->selectRaw('oi.product_id,oi.type,oi.price,oi.amount,oi.fact_amount,p.stock_code,p.weight,p.warehouse_id,p.name')
            ->get();
            
            // 订单类型科目
            $orderType = DB::table('order_type')->get();
            $orderType = array_by($orderType);

            $main["main"] = array(
                "number"          => $order['number'],
                "flow_step_id"    => 1,
                "province_id"     => $client['province_id'],
                "city_id"         => $client['city_id'],
                "county_id"       => $client['county_id'],
                "client_id"       => $client['id'],
                "company_code"    => $client['username'],
                //"company_name"  => $client['company_name'],
                "company_name"    => $order['invoice_company'].($order['invoice_type'] == 3 ? '-GA' : ''),
                "company_address" => $client['address'],
                "company_warehouse_address" => $client['warehouse_address'],
                "company_tel"     => $client['tel'],
                "company_fax"     => $client['fax'],
                "invoice_type"    => $order['invoice_type'],
                "add_time"        => date('Y-m-d H:i:s', $order['add_time']),
                "advance_warehouse_location" => $transport['advance_depot'],
                "advance_warehouse_number" => $transport['advance_depot_number'],
                "advance_time"      => $transport['advance_time'],
                "transport_company" => $transport['advance_car_company'],
                "transport_contact" => $transport['phone'],
                "transport_plate"   => $transport['advance_car_number'],
            );

            $data = array();
            foreach ($order_data as $v) {
                $data[] = array(
                    'type'        => $orderType[$v['type']]['type'] == 1 ? 1 : 10,
                    'product_id'  => $v['product_id'],
                    'warehouse_id'=> $v['warehouse_id'],
                    'price'       => $v['price'],
                    'amount'      => $v['amount'],
                    'fact_amount' => $v['fact_amount'],
                    'add_time'    => date('Y-m-d H:i:s', $v['add_time']),
                );
            }
            $main['data'] = $data;
            
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, "http://www.cnnzfood.com/oa/ajax/data/import");
            curl_setopt($ch, CURLOPT_HEADER, 0);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array('Expect:'));
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($main));
            $output = curl_exec($ch);
            curl_close($ch);

            if ($output == 'true') {
                return $this->json('同步成功。');
            } else {
                return $this->json('订单重复。');
            }
        }
    }

    /**
     * 在线支付方法
     */
    public function payAction()
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
            'money' => $money,
            'order' => $order,
            'client' => $client,
        ]);
    }

    /**
     * 订单删除
     */
    public function deleteAction()
    {
        if (Request::method() == 'POST') {
            $order_id = Input::get('order_id');
            $ids = array_filter((array)$order_id);

            foreach ($ids as $id) {
                DB::table('order')->where('id', $id)->delete();
                DB::table('order_transport')->where('order_id', $id)->delete();
                DB::table('order_audit')->where('order_id', $id)->delete();
                DB::table('order_data')->where('order_id', $id)->delete();
                
                return $this->back('恭喜你，订单删除成功。');
            }
        }
    }
}
