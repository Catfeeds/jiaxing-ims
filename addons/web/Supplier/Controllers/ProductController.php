<?php namespace Aike\Web\Supplier\Controllers;

use DB;
use Auth;
use Input;
use Request;
use Validator;

use Aike\Web\User\User;
use Aike\Web\Supplier\Supplier;
use Aike\Web\Supplier\Product;
use Aike\Web\Supplier\ProductCategory;
use Aike\Web\Supplier\Warehouse;
use Aike\Web\Supplier\Price;
use Aike\Web\Supplier\PriceData;

use Aike\Web\Index\Controllers\DefaultController;

class ProductController extends DefaultController
{
    public $permission = ['dialog', 'dialog_jqgrid', 'price', 'suppliers'];

    // 产品列表
    public function indexAction()
    {
        // 更新排序
        if (Request::method() == 'POST') {
            $sorts = Input::get('sort');
            foreach ($sorts as $id => $sort) {
                Product::where('id', $id)->update([
                    'sort' => $sort
                ]);
            }
            return $this->back('排序完成。');
        }
        
        $search = search_form([
            'referer'  => 1,
            'status'   => 1,
        ], [
            ['text','product.name','名称'],
            ['text','product.stock_number','存货编码'],
            ['text','product.code','存货代码'],
            ['text','product.id','ID'],
            ['category','product.category_id','类别'],
        ]);

        $query = $search['query'];

        $model = Product::type('supplier')
        ->where('product.status', $query['status'])
        ->orderBy('product_category.sort', 'asc')
        ->orderBy('product.sort', 'asc')
        ->orderBy('product.id', 'desc');

        if (authorise() == 1) {
            $supplier = Supplier::where('user_id', Auth::id())->first();
            $model->where('product.supplier_id', $supplier->id);
        }

        foreach ($search['where'] as $where) {
            if ($where['active']) {
                $model->search($where);
            }
        }

        $rows = $model->select(['product.*', 'product_category.title as category_name'])
        ->paginate()->appends($query);

        $product_ids = [];
        foreach ($rows as $key => $row) {
            $product_ids[] = $row['id'];
        }

        $prices = [];

        $_prices = PriceData::whereIn('product_id', $product_ids)
        ->orderBy('date', 'desc')
        ->get();

        foreach ($_prices as $key => $row) {
            if (empty($prices[$row['product_id']])) {
                $prices[$row['product_id']] = $row['price'];
            }
        }

        $_categorys = ProductCategory::type('supplier')->orderBy('lft', 'asc')->get()->toNested();
        $categorys  = [];
        foreach ($_categorys as $key => $_category) {
            $categorys[] = ['id' => $_category['id'], 'name' => $_category['layer_space'].$_category['name']];
        }

        return $this->display([
            'rows'      => $rows,
            'prices'    => $prices,
            'categorys' => $categorys,
            'query'     => $query,
            'search'    => $search,
        ]);
    }

    // 添加产品
    public function createAction()
    {
        if (Request::method() == 'POST') {
            $gets = Input::get();
            
            $model = Product::findOrNew($gets['id']);
            $rules = [
                'name'          => 'required',
                'category_id'   => 'required',
                'stock_number'  => 'required|unique:product,stock_number,'.$gets['id'].',id,stock_type,2',
            ];

            $attrs = [
                'name'         => '产品名称',
                'category_id'  => '产品类别',
                'stock_number' => '存货编码',
            ];

            $v = Validator::make($gets, $rules, [], $attrs);
            if ($v->fails()) {
                return $this->back()->withErrors($v)->withInput();
            }
            
            // 上传图片
            $image = image_create('products', 'image', $model['image']);
            if ($image) {
                $gets['image'] = $image;
            }

            // 物料标记
            $gets['stock_type'] = 2;

            $model->fill($gets)->save();

            // 更新关系表
            $model->suppliers()->sync(explode(',', $gets['supplier_id']));

            return $this->success('index', '恭喜你，产品更新成功。');
        }
        
        $id = (int)Input::get('id');
        $product    = Product::find($id);
        $categorys  = ProductCategory::type('supplier')->orderBy('lft', 'asc')->get()->toNested();
        $warehouses = Warehouse::type('supplier')->orderBy('lft', 'asc')->get()->toNested();

        if ($product) {
            $suppliers = $product->suppliers()->pluck('supplier_id')->implode(',');
        }

        return $this->display(array(
            'warehouses' => $warehouses,
            'suppliers'  => $suppliers,
            'categorys'  => $categorys,
            'product'    => $product,
        ));
    }

    // 导入产品单价
    public function importAction()
    {
        if (Input::hasfile('myfile')) {
            $file = Input::file('myfile');
            $temps = readExcel($file);
            foreach ($temps as $i => $temp) {
                if ($i > 1 && $temp[0]) {
                    $data = [
                        'stock_number' => $temp[0],
                        'price1'       => $temp[4],
                    ];
                    DB::table('product')->where('stock_number', $temp[0])->update($data);
                }
            }
            return $this->success('index', '恭喜你，更新成功。');
        }
        return $this->display();
    }

    // 商品供应商
    public function suppliersAction()
    {
        $product_id = (int)Input::get('product_id');

        $product = Product::find($product_id);
        if ($product) {
            $supplier_ids = $product->suppliers()->pluck('supplier_id');
        }

        $rows = Supplier::leftJoin('user', 'user.id', '=', 'supplier.user_id')
        ->whereIn('supplier.id', $supplier_ids)
        ->select(['supplier.id', 'user.nickname as text'])
        ->paginate();

        return response()->json($rows);
    }

    // 历史价格
    public function priceAction()
    {
        $id = (int)Input::get('id');

        $prices = PriceData::leftJoin('supplier_price', 'supplier_price.id', '=', 'supplier_price_data.price_id')
        ->orderBy('supplier_price_data.date', 'desc')
        ->where('supplier_price_data.product_id', $id)
        ->where('supplier_price.status', 1)
        ->get(['supplier_price_data.*']);

        return $this->render(array(
            'prices' => $prices,
        ));
    }

    /**
     * 弹出商品列表
     */
    public function dialog_jqgridAction()
    {
        $gets = Input::get();

        $search = search_form([
            'advanced'  => '',
            'owner_id'  => 0,
            'type'      => 2,
            'page'      => 1,
            'status'    => '',
            'sort'      => '',
            'order'     => '',
            'limit'     => '',
        ], [
            ['text','product.name','产品名称'],
            ['text','product.spec','产品规格'],
            ['text','product.barcode','产品条码'],
            ['status','product.status','产品状态'],
            ['text','product.stock_number','存货编码'],
            ['category','product.category_id','产品类别'],
            ['text','product.id','产品ID'],
        ]);
        
        $query = $search['query'];

        if (Request::method() == 'POST') {
            $model = DB::table('product')
            ->leftJoin('product_category', 'product_category.id', '=', 'product.category_id')
            ->where('product_category.type', $query['type'])
            ->where('product.status', 1)
            ->orderBy('product_category.lft', 'asc');

            // 商品状态
            if (is_numeric($query['status'])) {
                $model->where('product.status', $query['status']);
            }

            if ($this->access['dialog'] == 1) {
                // 仓库负责人
                if ($query['owner_id']) {
                    $warehouse_id = DB::table('warehouse')->where('user_id', $query['owner_id'])->pluck('id');
                    $model->whereIn('product.warehouse_id', $warehouse_id);
                }
            }

            // 排序方式
            if ($query['sort'] && $query['order']) {
                $model->orderBy($query['sort'], $query['order']);
            } else {
                $model->orderBy('product.sort', 'asc');
            }

            // 搜索条件
            foreach ($search['where'] as $where) {
                if ($where['active']) {
                    $model->search($where);
                }
            }

            $rows = $model->selectRaw("product.*,product_category.name as category_name, IF(product.spec='', product.name, concat(product.name,' - ', product.spec)) as text")
            ->paginate($query['limit']);

            // 读取用友库存
            /*
            $yonyou_data = DB::table('stock_yonyou_data')
            ->whereBetween('ym', [$sdate, $edate])
            ->whereIn('code', $stock_number)
            ->get();

            $yonyou_history = DB::table('stock_yonyou')
            ->whereIn('code', $stock_number)
            ->pluck('quantity', 'code');

            $yonyous = [];
            foreach ($yonyou_data as $key => $yonyou) {
                $code = $yonyou['code'];
                $yonyous['quantity_in'][$code]    += $yonyou['quantity_in'];
                $yonyous['quantity_out'][$code]   += $yonyou['quantity_out'];
                $yonyous['quantity_start'][$code] += $yonyou['quantity_start'];
                $yonyous['quantity_end'][$code]   += $yonyou['quantity_end'];
            }

            foreach ($stock_number as $code) {
                if(!isset($yonyous['quantity_in'][$code])) {
                    $yonyous['quantity_start'][$code] = $yonyou_history[$code];
                    $yonyous['quantity_end'][$code]   = $yonyou_history[$code];
                }
            }

            // 去年上个月
            $yonyou_data = DB::table('stock_yonyou_data')
            ->whereBetween('ym', [$sdate, $edate])
            ->whereIn('code', $stock_number)
            ->get();
            */

            /*
            // 获取库存现存量
            $totals = Stock::total();

            // 设置现存量
            $items = $rows->map(function($row) use($totals) {
                $row['stock_total'] = (int)$totals[$row['id']];
                return $row;
            });
            $rows->setCollection($items);
            */

            return response()->json($rows);
        }
        return $this->render(array(
            'search' => $search,
            'gets'   => $gets,
        ), 'jqgrid');
    }

    /**
     * 弹出层信息
     */
    public function dialogAction()
    {
        $gets = Input::get();

        $search = search_form([
            'advanced' => '',
            'offset'   => '',
            'sort'     => '',
            'order'    => '',
            'limit'    => '',
        ], [
            ['text','product.name','名称'],
            ['text','product.spec','规格'],
            ['text','product.stock_number','存货编码'],
            ['category','product.category_id','类别'],
            ['text','product.id','ID'],
        ]);

        $query = $search['query'];

        if (Request::method() == 'POST') {
            $model = Product::type('supplier')
            ->where('product.status', 1);

            // 排序方式
            if ($query['sort'] && $query['order']) {
                $model->orderBy('product.'.$query['sort'], $query['order']);
            }

            foreach ($search['where'] as $where) {
                if ($where['active']) {
                    $model->search($where);
                }
            }

            $rows = $model->select(['product.*'])->paginate();
            
            $category = ProductCategory::type('supplier')->orderBy('lft', 'asc')->get()->toNested();

            // 读取今日的周期计划
            $product = DB::table('product')->where('id', $id)->first();

            $budgets = DB::table('supplier_budget')
            ->leftJoin('supplier_budget_data', 'supplier_budget.id', '=', 'supplier_budget_data.budget_id')
            ->selectRaw('supplier_budget_data.product_id,sum(supplier_budget_data.quantity) as budget_quantity')
            ->groupBy('supplier_budget_data.product_id')
            ->where('date', date('Y-m-d'))->pluck('budget_quantity', 'product_id');

            foreach ($rows as &$row) {
                $row['text'] = $row['name'].($row['spec'] ? ' - '.$row['spec'] : '');
                // $row['supplier_name'] = $row->supplier->user->nickname;
                $row['category'] = $category[$row['category_id']]['text'];
                $row['budget'] = $budgets[$row['id']];
            }
            return response()->json($rows);
        }

        $tpl = 'dialog';
        if ($gets['ng'] == 1) {
            $tpl = 'ng_dialog';
        }
        return $this->render(array(
            'search' => $search,
            'get'    => $gets,
        ), $tpl);
    }

    // 删除产品
    public function deleteAction()
    {
        $id = Input::get('id');
        if ($id <= 0) {
            return $this->error('编号不正确。');
        }

        // 查询商品
        $product = Product::find($id);

        // 删除图片
        image_delete($product['image']);

        // 删除供应商关系表
        $product->suppliers()->sync([]);

        // 删除商品数据
        $product->delete();

        return $this->success('index', '恭喜你，产品删除成功。');
    }
}
