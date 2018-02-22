<?php namespace Aike\Web\Supplier\Controllers;

use DB;
use Input;
use Illuminate\Http\Request;
use Validator;
use Auth;

use Aike\Web\User\User;
use Aike\Web\Supplier\Supplier;
use Aike\Web\Supplier\Inventory;
use Aike\Web\Supplier\InventoryData;
use Aike\Web\Supplier\Product;
use Aike\Web\Supplier\ProductCategory;
use Aike\Web\Supplier\Stock;
use Aike\Web\Index\Controllers\DefaultController;

class InventoryController extends DefaultController
{
    public function indexAction()
    {
        $search = search_form([
            'referer' => 1,
        ], [
            ['text','supplier_inventory.number','单号'],
        ]);

        $query  = $search['query'];

        $model = Inventory::with('datas', 'supplier.user')->orderBy('id', 'desc');

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
        $id        = $request->input('id');
        $inventory = Inventory::with('datas')->find($id);
        $datas     = [];
        foreach ($inventory->datas as $data) {
            $datas[$data->product_id] += $data->quantity;
        }

        $categorys = ProductCategory::type('supplier')->orderBy('lft', 'asc')->get()->toNested();
        $products = Product::type('supplier')->get(['product.*'])->KeyBy('id');

        return $this->display([
            'inventory' => $inventory,
            'datas'     => $datas,
            'categorys' => $categorys,
            'products'  => $products,
        ]);
    }

    // 新建库存登记
    public function createAction(Request $request)
    {
        if ($request->method() == 'POST') {
            $datas = $request->input('datas', []);

            $rules = $messages = [];
            
            foreach ($datas as $i => $data) {
                if ($data['quantity']) {
                    $rules['datas.'.$i.'.quantity']   = 'required|numeric';
                    $rules['datas.'.$i.'.plan_cycle'] = 'required|numeric';
                    $rules['datas.'.$i.'.plan_date']  = 'required|date';
                    $messages['datas.'.$i.'.quantity.required'] = '['.$i.']入库数量必须填写。';
                    $messages['datas.'.$i.'.quantity.numeric']  = '['.$i.']入库数量必须是数字。';
                } else {
                    unset($datas[$i]);
                }
            }

            if (empty($rules)) {
                return $this->back()->withInput()->with('error', '库存商品不能为空。');
            }

            $v = Validator::make($request->all(), $rules, $messages);
            if ($v->fails()) {
                return $this->back()->withErrors($v)->withInput();
            }

            $_inventory['number'] = date('ymd-').Inventory::count();
            $_inventory['supplier_id'] = $request->input('supplier_id');

            $inventory = new Inventory;
            $inventory->fill($_inventory)->save();

            foreach ($datas as $row) {
                $data = new InventoryData;
                $data->inventory_id = $inventory->id;
                $data->fill($row)->save();
            }
            return $this->success('supplier/inventory/show', ['id'=>$inventory->id], '恭喜您，库存登记单提交成功。');
        }
        
        $id = auth()->id();
        
        $supplier = Supplier::with('user', 'products')->where('user_id', $id)->first();
        $categorys = ProductCategory::type('supplier')->orderBy('lft', 'asc')->get()->toNested();
        $products = Product::type('supplier')->get(['product.*'])->KeyBy('id');

        return $this->display([
            'supplier'  => $supplier,
            'categorys' => $categorys,
            'products'  => $products,
        ]);
    }

    // 库存汇总
    public function reportAction(Request $request)
    {
        $query = [
            'sdate'       => date('Y-m-01'),
            'edate'       => date('Y-m-d'),
            'category_id' => 0,
            'product_id'  => 0
        ];
        foreach ($query as $k => $v) {
            $query[$k] = Input::get($k, $v);
        }

        $products = $balance = [];

        // 获取上期库存
        $balance['last'] = Stock::balance($query['sdate']);

        // 获取本期库存
        $balance['now'] = Stock::balance($query['sdate'], $query['edate']);

        $rows = DB::table('product as p')
        ->LeftJoin('product_category as pc', 'p.category_id', '=', 'pc.id')
        ->whereRaw('p.status=1')
        ->where('pc.type', 2)
        ->orderBy('pc.lft', 'asc')
        ->orderBy('p.sort', 'asc');

        // 选择产品类别
        if ($query['category_id'] > 0) {
            $rows->where('pc.id', $query['category_id']);
        }

        // 查询产品
        $products = DB::table('product as p')
        ->LeftJoin('product_category as pc', 'p.category_id', '= ', 'pc.id')
        ->where('pc.id', $query['category_id'])
        ->whereRaw('p.status=1')
        ->where('pc.type', 2)
        ->orderBy('pc.lft', 'asc')
        ->orderBy('p.sort', 'asc')
        ->get(['p.*', 'pc.name as category_name']);

        // 选择产品
        if ($query['product_id'] > 0) {
            $rows->where('p.id', $query['product_id']);
        }

        // 获取产品列表
        $maps = $rows->get(['p.*', 'pc.name as category_name']);
        $rows = [];
        foreach ($maps as $map) {
            $rows[$map['id']] = $map;
        }

        // 循环当前产品
        foreach ($rows as $product_id => $product) {
            // 上期结存
            $balance['a'][$product_id] = (int)$balance['last'][1][$product_id] - $balance['last'][2][$product_id];

            // 本期入库
            $balance['b'][$product_id] = (int)$balance['now'][1][$product_id];
            
            // 本期出库
            $balance['c'][$product_id] = (int)$balance['now'][2][$product_id];
            
            // 期末结存
            $now_diff = (int)$balance['now'][1][$product_id] - $balance['now'][2][$product_id];

            $balance['d'][$product_id] = (int)$balance['a'][$product_id] + $now_diff;
        }

        // 读取产品类别
        $categorys = ProductCategory::type('supplier')->orderBy('lft', 'asc')->get()->toNested();

        return $this->display(array(
            'balance'   => $balance,
            'products'  => $products,
            'categorys' => $categorys,
            'query'     => $query,
            'rows'      => $rows,
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

            Inventory::whereIn('id', $id)->delete();
            InventoryData::whereIn('inventory_id', $id)->delete();

            return $this->success('index', '恭喜你，库存登记单删除成功。');
        }
    }
}
