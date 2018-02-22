<?php namespace Aike\Web\Supplier\Controllers;

use Illuminate\Http\Request;
use Validator;
use Auth;
use DB;

use Aike\Web\Supplier\Supplier;
use Aike\Web\Supplier\Settlement;
use Aike\Web\Supplier\SettlementData;
use Aike\Web\Supplier\Product;
use Aike\Web\Supplier\ProductCategory;
use Aike\Web\Supplier\Quality;

use Aike\Web\Index\Attachment;

use Aike\Web\Index\Controllers\DefaultController;

class SettlementController extends DefaultController
{
    public $permission = ['print', 'query'];

    public function indexAction(Request $request)
    {
        $search = search_form([
            'referer' => 1,
            'status'  => 0
        ], [
            ['text','supplier_settlement.sn','单号'],
            ['second','supplier_settlement.created_at','创建时间'],
        ]);

        $query = $search['query'];

        $model = Settlement::stepAt()
        ->leftJoin('supplier', 'supplier.id', '=', 'supplier_settlement.supplier_id')
        ->where('supplier_settlement.status', $query['status'])
        ->orderBy('supplier_settlement.id', 'desc')
        ->select(['supplier_settlement.*']);

        if (authorise() == 1) {
            $model->where('supplier.user_id', Auth::id());
        }

        foreach ($search['where'] as $where) {
            if ($where['active']) {
                $model->search($where);
            }
        }

        $tabs = [
            'name'  => 'status',
            'items' => Settlement::$tabs
        ];

        $rows = $model->paginate()->appends($query);
        return $this->display([
            'rows'   => $rows,
            'search' => $search,
            'tabs'   => $tabs,
        ]);
    }

    // 查询生成供应商商品
    public function queryAction(Request $request)
    {
        $search = search_form([
            'customer' => '',
            'start_at' => '',
            'end_at'   => '',
        ], []);

        $query = $search['query'];

        if ($request->method() == 'POST') {
            if ($query['supplier_id']) {
                $supplier = DB::table('supplier')
                ->leftJoin('user', 'supplier.user_id', '=', 'user.id')
                ->where('supplier.id', $query['supplier_id'])
                ->first();

                $ch = curl_init('http://118.122.82.249:90/yonyou.php?do=settlement&start_at='.$query['start_at'].'&end_at='.$query['end_at'].'&supplier_code='.$supplier['username']);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                $res = curl_exec($ch);
                curl_close($ch);
                $data = json_decode($res, true);

                $codes = [];
                foreach ($data as $row) {
                    $codes[] = $row['code'];
                }

                $products = Product::type('supplier')
                ->whereIn('stock_number', $codes)
                ->get(['product.*'])->keyBy('stock_number');

                // 历史单价
                $_prices = DB::table('supplier_price_data')
                ->leftJoin('product', 'product.id', '=', 'supplier_price_data.product_id')
                ->leftJoin('supplier_price', 'supplier_price.id', '=', 'supplier_price_data.price_id')
                ->whereIn('product.stock_number', $codes)
                ->orderBy('supplier_price_data.date', 'desc')
                ->get(['supplier_price_data.*','product.stock_number']);

                // 生成单价列表
                $prices = [];
                foreach ($_prices as $_price) {
                    $prices[$_price['stock_number']][$_price['date']] = $_price['price'];
                }

                $rows = [];
                foreach ($data as $row) {
                    $row['price'] = '无';

                    $goods_prices = (array)$prices[$row['code']];

                    $dDate = strtotime($row['dDate']);

                    // 寻找单价
                    foreach ($goods_prices as $date => $price) {
                        // 如果收购时间大于等于设定单价时间
                        if ($dDate >= $date) {
                            $row['price']      = $price;
                            $row['price_time'] = date('Y-m-d H:i', $date);
                            break;
                        }
                    }

                    $row['date']  = $row['ymd'];
                    $row['money'] = $row['price'] * $row['quantity'];
                    $row['goods'] = $products[$row['code']]['name'];
                    $row['product_id'] = $products[$row['code']]['id'];
                    $rows[] = $row;
                }

                return response()->json($rows);
            } else {
                return '[]';
            }
        }

        return $this->display();
    }

    // 显示订单
    public function showAction(Request $request)
    {
        $id = $request->input('id');
        $settlement = Settlement::stepAt()->find($id);

        $datas      = SettlementData::where('settlement_id', $id)
        ->selectRaw('*,sum(money) as sum_money,sum(quantity) as sum_quantity')
        ->groupBy('price', 'product_id')
        ->get();

        $categorys = ProductCategory::type('supplier')->orderBy('lft', 'asc')->get()->toNested();
        $products = Product::type('supplier')->get(['product.*'])->KeyBy('id');

        $qualitys = Quality::stepAt()
        ->where('supplier_id', $settlement->supplier_id)
        ->where('status', 0)
        ->get();

        $step = get_step_status($settlement);

        // 附件
        $attach = Attachment::view($price['attachment']);

        return $this->display([
            'qualitys'   => $qualitys,
            'settlement' => $settlement,
            'datas'      => $datas,
            'categorys'  => $categorys,
            'products'   => $products,
            'step'       => $step,
            'attach'     => $attach,
        ]);
    }

    // 打印计划订单
    public function printAction(Request $request)
    {
        $id = $request->input('id');
        $settlement = Settlement::find($id);

        $datas      = SettlementData::where('settlement_id', $id)
        ->selectRaw('*,sum(money) as sum_money,sum(quantity) as sum_quantity')
        ->groupBy('price', 'product_id')
        ->get();

        $categorys = ProductCategory::type('supplier')->orderBy('lft', 'asc')->get()->toNested();
        $products = Product::type('supplier')->get(['product.*'])->KeyBy('id');

        $qualitys = Quality::stepAt()
        ->where('supplier_id', $settlement->supplier_id)
        ->where('status', 0)
        ->get();

        $this->layout = 'layouts.print';

        return $this->display([
            'qualitys'   => $qualitys,
            'settlement' => $settlement,
            'datas'      => $datas,
            'categorys'  => $categorys,
            'products'   => $products,
        ]);
    }

    // 新建订单
    public function createAction(Request $request)
    {
        if ($request->method() == 'POST') {
            $gets = $request->all();

            $rules = $messages = [];

            $v = Validator::make($request->all(), $rules, $messages);
            if ($v->fails()) {
                return json_encode($v->errors()->all());
            }

            $count = Settlement::count();
            $sn    = date('ymd-').$count;

            $_settlement = [
                'sn'          => $sn,
                'supplier_id' => $gets['supplier_id'],
                'start_at'    => $gets['start_at'],
                'end_at'      => $gets['end_at'],
            ];

            if ($gets['attachment']) {
                $_settlement['attachment'] = join(',', $gets['attachment']);
            }

            $settlement = new Settlement;
            $settlement->fill($_settlement)->save();

            foreach ($gets['datas'] as $row) {
                $data = new SettlementData;
                $row['settlement_id'] = $settlement->id;
                $row['date']     = strtotime($row['date']);
                $data->fill($row)->save();
            }

            // 附件发布
            Attachment::publish();

            $referer = url_referer("index");
            return $this->json($referer, true);
        }

        $id  = (int)$request->input('id');
        $settlement = Settlement::with('datas')->where('id', $id)->first();

        $models = [
            ['name' => "id", 'hidden' => true],
            ['name' => 'op', 'label' => '&nbsp;', 'formatter' => 'options', 'width' => 60, 'sortable' => false, 'align' => 'center'],
            ['name' => "product_id", 'label' => '商品ID', 'hidden' => true],
            ['name' => "supplier_id", 'label' => '供应商ID', 'hidden' => true],
            ['name' => "product_name", 'width' => 280, 'label' => '商品', 'rules' => ['required'=>true], 'sortable' => false, 'editable' => true],
            ['name' => "price", 'width' => 100, 'label' => '单价', 'rules' => ['required'=>true], 'formatter' => 'number', 'formatoptions' => ['decimalPlaces'=>4], 'sortable' => false, 'editable' => true, 'align' => 'right'],
            ['name' => "date", 'label' => '生效时间', 'width' => 140, 'rules' => ['required' => true], 'formatter' => 'date', 'formatoptions' => ['srcformat'=>'U','newformat' => 'Y-m-d H:i'], 'sortable' => false, 'editable' => true, 'align' => 'center'],
            ['name' => "description", 'label' => '备注', 'width' => 200, 'sortable' => false, 'editable' => true],
        ];

        $attach = Attachment::edit($settlement->attachment);

        return $this->display([
            'settlement' => $settlement,
            'query'      => $query,
            'models'     => $models,
            'attach'     => $attach,
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

            Settlement::whereIn('id', $id)->delete();
            SettlementData::whereIn('settlement_id', $id)->delete();

            return $this->success('index', '恭喜你，操作成功。');
        }
    }
}
