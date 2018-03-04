<?php namespace Aike\Web\Stock\Controllers;

use DB;
use Input;
use Request;
use Validator;

use Aike\Web\Stock\Product;
use Aike\Web\Stock\ProductCategory;
use Aike\Web\Stock\Warehouse;

use Aike\Web\Index\Controllers\DefaultController;

class ServiceController extends DefaultController
{
    public $permission = ['dialog'];

    // 产品列表
    public function indexAction()
    {
        $stores = DB::table('store')->get(['id', 'name as text']);

        $columns = [[
            'name'     => 'name',
            'index'    => 'product.name',
            'search'   => 'text',
            'label'    => '服务名称',
            'minWidth' => 200,
            'align'    => 'left',
        ],[
            'name'     => 'spec',
            'index'    => 'product.spec',
            'search'   => 'text',
            'label'    => '服务规格',
            'width' => 200,
            'align'    => 'left',
        ],[
            'name'     => 'category_name',
            'index'    => 'product.category_id',
            'search'   => [
                'type' => 'select',
                'url'  => 'stock/service-category/dialog',
            ],
            'label'    => '服务类别',
            'width' => 180,
            'align'    => 'center',
        ],[
            'name'     => 'price',
            'index'    => 'product.price',
            'search'   => 'text',
            'label'    => '销售价',
            'width' => 80,
            'align'    => 'right',
        ],[
            'name'     => 'barcode',
            'index'    => 'product.barcode',
            'search'   => 'text',
            'label'    => '条码',
            'width' => 160,
            'align'    => 'center',
        ],[
            'name'     => 'store_name',
            'index'    => 'store.name',
            'search'   => [
                'type' => 'select',
                'data' => $stores,
            ],
            'label'    => '所属门店',
            'width' => 160,
            'align'    => 'center',
        ],[
            'name'     => 'is_share',
            'index'    => 'product.is_share',
            'formatter' => 'is_share',
            'search'   => [
                'type' => 'select',
                'data' => [['id' => 1, 'text' => '是'],['id' => 0, 'text' => '否']],
            ],
            'formatter' => 'select',
            'label'    => '是否共享',
            'width' => 160,
            'align'    => 'center',
        ],[
            'name'     => 'status',
            'index'    => 'product.status',
            'label'    => '状态',
            'width'    => 100,
            'search'   => [
                'type' => 'select',
                'data' => [['id' => 1, 'text' => '启用'],['id' => 0, 'text' => '停用']],
            ],
            'formatter' => 'status',
            'align'     => 'center',
        ],[
            'name'    => 'updated_at',
            'index'   => 'product.updated_at',
            'label'   => '操作时间',
            'width'   => 140,
            'formatter' => 'date',
            'formatoptions' => [
                'srcformat' => 'u',
                'newformat' => 'Y-m-d H:i'
            ],
            'align' => 'center',
        ],[
            'name'  => 'id',
            'index' => 'product.id',
            'label' => 'ID',
            'width' => 60,
            'align' => 'center',
        ]];

        $search_columns = search_columns($columns);
        $search = search_form([
            'referer'  => 1,
            'advanced' => 1,
        ], $search_columns);

        $query = $search['query'];

        if (Input::ajax()) {
            $model = Product::where('product.type', 2)
            ->LeftJoin('product_category', 'product_category.id', '=', 'product.category_id')
            ->LeftJoin('store', 'store.id', '=', 'product.store_id')
            ->orderBy('product.id', 'asc')
            ->select([
                'product.*',
                'product_category.name as category_name',
                'store.name as store_name'
            ]);

            foreach ($search['where'] as $where) {
                if ($where['active']) {
                    if ($where['field'] == 'product.category_id') {
                        $category = ProductCategory::where('id', $where['search'])->first();
                        $model->whereBetween('product_category.lft', [$category->lft, $category->rgt]);
                    } else {
                        $model->search($where);
                    }
                }
            }
            return response()->json($model->paginate($search['limit']));
        }

        return $this->display([
            'search'  => $search,
            'columns' => $columns,
        ]);
    }

    // 添加产品
    public function createAction()
    {
        if (Request::method() == 'POST') {
            $gets = Input::get();
            
            $model = Product::findOrNew($gets['id']);
            
            $rules = [
                'name'         => 'required',
                'category_id'  => 'required',
            ];
            $v = Validator::make($gets, $rules);
            if ($v->fails()) {
                return $this->json($v->errors()->all());
            }
            
            /*
            // 上传图片
            $image = image_create('products', 'image', $model['image']);
            if ($image) {
                $gets['image'] = $image;
            }
            */

            $gets['type'] = 2;
            $model->fill($gets)->save();
            return $this->json('恭喜你，服务更新成功。', true);
        }

        $id = (int)Input::get('id');
        $row = DB::table('product')->where('id', $id)->first();

        $categorys = ProductCategory::where('type', 2)->orderBy('lft', 'asc')->get()->toNested();
        $warehouses = Warehouse::orderBy('lft', 'asc')->get();
        
        return $this->render([
            'warehouse'  => $warehouse,
            'categorys'  => $categorys,
            'warehouses' => $warehouses,
            'row'        => $row,
        ], 'create');
    }

    // 编辑商品
    public function editAction()
    {
        if (Request::method() == 'POST') {
            $gets = Input::get();
            
            $model = Product::findOrNew($gets['id']);
            
            $rules = [
                'name'         => 'required',
                'category_id'  => 'required',
            ];
            $v = Validator::make($gets, $rules);
            if ($v->fails()) {
                return $this->json($v->errors()->all());
            }
            
            /*
            // 上传图片
            $image = image_create('products', 'image', $model['image']);
            if ($image) {
                $gets['image'] = $image;
            }
            */
            
            $gets['type'] = 2;
            $model->fill($gets)->save();
            return $this->json('恭喜你，服务更新成功。', true);
        }

        $id = (int)Input::get('id');
        $row = DB::table('product')->where('id', $id)->first();

        $categorys = ProductCategory::where('type', 2)->orderBy('lft', 'asc')->get()->toNested();
        $warehouses = Warehouse::orderBy('lft', 'asc')->get();
        
        return $this->render([
            'warehouse'  => $warehouse,
            'categorys'  => $categorys,
            'warehouses' => $warehouses,
            'row'        => $row,
        ], 'create');
    }

    // 导出产品信息
    public function exportAction()
    {
        $columns = [[
            'name'  => 'name',
            'index' => 'product.name',
            'label' => '产品名称',
        ],[
            'name'  => 'spec',
            'index' => 'product.spec',
            'label' => '产品规格',
        ],[
            'name'  => 'category_name',
            'index' => 'product_category.name as category_name',
            'join'  => ['product_category','product_category.id','=','product.category_id'],
            'label' => '产品类别',
        ],[
            'name'  => 'price1',
            'index' => 'product.price1',
            'label' => '销售价',
        ],[
            'name'  => 'price4',
            'index' => 'product.price4',
            'label' => '销售(k/a)价',
        ],[
            'name'  => 'price2',
            'index' => 'product.price2',
            'label' => '经销价',
        ],[
            'name'  => 'price3',
            'index' => 'product.price3',
            'label' => '直营价',
        ]];

        $_columns = $_joins = [];
        foreach ($columns as $column) {
            if ($column['join']) {
                $on = $column['join'][1].$column['join'][2].$column['join'][3];
                $_joins[$on] = $column['join'];
            }

            if (is_array($column['index'])) {
                array_merge($_columns, $column['index']);
            } else {
                $_columns[] = $column['index'];
            }
        }

        $model = DB::table('product')
        ->where('product_category.type', 1);

        foreach ($_joins as $_join) {
            $model->leftJoin($_join[0], $_join[1], $_join[2], $_join[3]);
        }

        $status = Input::get('status', 1);
        $model->where('product.status', $status);
        
        $rows = $model->get($_columns);
        writeExcel($columns, $rows, date('y-m-d').'-产品档案');
    }

    /**
     * 弹出商品
     */
    public function dialogAction()
    {
        $gets = Input::get();

        $abc = [
            ['text','product.name','产品名称'],
            ['text','product.spec','产品规格'],
            ['text','product.barcode','产品条码'],
            ['text','product.barcode','存货编码'],
            ['status','product.status','产品状态'],
            ['category','product.category_id','产品类别'],
            ['text','product.id','产品ID'],
        ];

        if ($gets['type'] == 2) {
            $abc[] = ['supplier','product.supplier_id','供应商'];
        }

        $search = search_form([
            'advanced'    => '',
            'owner_id'    => 0,
            'supplier_id' => 0,
            'type'        => 1,
            'page'        => 1,
            'sort'        => '',
            'order'       => '',
            'limit'       => '',
        ], $abc);
        
        $query = $search['query'];

        if (Request::method() == 'POST') {
            $model = DB::table('product')
            ->leftJoin('product_category', 'product_category.id', '=', 'product.category_id')
            ->where('product_category.type', $query['type'])
            ->where('product.status', 1)
            ->orderBy('product_category.lft', 'asc');

            if ($this->access['dialog'] == 1) {
                // 仓库负责人
                if ($query['owner_id']) {
                    $warehouse_id = DB::table('warehouse')->where('user_id', $query['owner_id'])->pluck('id');
                    $model->whereIn('product.warehouse_id', $warehouse_id);
                }
            }

            // 指定了供应商
            if ($query['supplier_id'] > 0) {
                $model->leftJoin('product_supplier', 'product_supplier.product_id', '=', 'product.id')
                ->where('product_supplier.supplier_id', $query['supplier_id']);
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

            if ($query['stock'] == 'a') {
                // 获取库存现存量
                $totals = Stock::total();

                // 设置现存量
                $items = $rows->map(function ($row) use ($totals) {
                    $row['stock_total'] = (int)$totals[$row['id']];
                    return $row;
                });
                $rows->setCollection($items);
            }

            if ($query['yonyou'] == 'a') {
                $time = time();

                // 去年 上月本月下月
                $last_a = date('Ym', strtotime('-1 year -1 month', $time));
                $last_b = date('Ym', strtotime('-1 year +1 month', $time));

                // 今年上月本月
                $now_a = date('Ym', strtotime('-1 month', $time));
                $now_b = date('Ym', $time);

                // 去年同期使用量
                $yonyou_data_last = DB::table('stock_yonyou_data')
                ->whereRaw('DATE_FORMAT(date,"%Y%m") between ? and ?', [date('Y', strtotime('-1 year')).'01', date('Ym', strtotime('-1 year'))])
                ->groupBy('code')
                ->selectRaw('code,sum(quantity_get) as quantity')
                ->pluck('quantity', 'code');

                $yonyou_data1 = DB::table('stock_yonyou_data')
                ->whereRaw('DATE_FORMAT(date,"%Y%m") between ? and ?', [$last_a, $last_b])
                ->groupBy('ym', 'code')
                ->selectRaw('DATE_FORMAT(date,"%Y%m") as ym,code,sum(quantity_get) as quantity')
                ->get();

                // 去年月范围
                $months1 = [];
                $_months1 = range($last_a, $last_b);
                foreach ($_months1 as $k => $v) {
                    $months1[$v] = 'month_'.($k + 1);
                }

                // 组合去年用量数据
                $_yonyou_data1 = [];
                foreach ($yonyou_data1 as $k => $v) {
                    $kk = $months1[$v['ym']];
                    $_yonyou_data1[$v['code']][$kk] = $v['quantity'];
                }

                $yonyou_data2 = DB::table('stock_yonyou_data')
                ->whereRaw('DATE_FORMAT(date,"%Y%m") between ? and ?', [$now_a, $now_b])
                ->groupBy('ym', 'code')
                ->selectRaw('DATE_FORMAT(date,"%Y%m") as ym,code,sum(quantity_get) as quantity')
                ->get();

                // 去年月范围是
                $months2 = [];
                $_months2 = range($now_a, $now_b);
                foreach ($_months2 as $k => $v) {
                    $months2[$v] = 'month_'.($k + 1);
                }

                // 组合去年用量数据
                $_yonyou_data2 = [];
                foreach ($yonyou_data2 as $k => $v) {
                    $kk = $months2[$v['ym']];
                    $_yonyou_data2[$v['code']][$kk] = $v['quantity'];
                }

                // 目前使用量
                $yonyou_data_now = DB::table('stock_yonyou_data')
                ->groupBy('code')
                ->selectRaw('sum(quantity_set - quantity_get) as quantity,code')
                ->pluck('quantity', 'code');

                // 本年最低价
                $yonyou_low_price = DB::table('stock_yonyou_data')
                ->groupBy('code')
                ->where('price', '>', 0)
                ->whereRaw('year(date)=year(now())')
                ->selectRaw('min(price) as min_price,code')
                ->pluck('min_price', 'code');

                // 周期订单预算
                $budgets = DB::table('supplier_budget')
                ->leftJoin('supplier_budget_data', 'supplier_budget.id', '=', 'supplier_budget_data.budget_id')
                ->selectRaw('supplier_budget_data.product_id,sum(supplier_budget_data.quantity) as budget_quantity')
                ->groupBy('supplier_budget_data.product_id')
                ->where('date', date('Y-m-d'))
                ->pluck('budget_quantity', 'product_id');

                $items = $rows->map(function ($row) use ($_yonyou_data1, $_yonyou_data2, $yonyou_data_now, $budgets, $yonyou_low_price, $yonyou_data_last) {
                    $code = $row['stock_number'];

                    $row['month_1'] = $_yonyou_data1[$code]['month_1'];
                    $row['month_2'] = $_yonyou_data1[$code]['month_2'];
                    $row['month_3'] = $_yonyou_data1[$code]['month_3'];

                    $row['month_4'] = $_yonyou_data2[$code]['month_1'];
                    $row['month_5'] = $_yonyou_data2[$code]['month_2'];

                    $row['month_6'] = $yonyou_data_now[$code];

                    // 去年同期使用量
                    $row['last_year'] = $yonyou_data_last[$code];

                    // 本年历史最低价
                    $row['low_price'] = $yonyou_low_price[$code];

                    $row['budget'] = $budgets[$row['id']];

                    return $row;
                });
                $rows->setCollection($items);
            }

            return response()->json($rows);
        }
        return $this->render(array(
            'search' => $search,
            'gets'   => $gets,
        ), 'jqgrid');
    }

    // 删除产品
    public function deleteAction()
    {
        $id = Input::get('id');
        if (empty($id)) {
            return $this->error('最少选择一行记录。');
        }

        $products = DB::table('product')->whereIn('id', $id)->get();
        foreach ($products as $product) {
            // 删除图片
            image_delete($product['image']);
        }
        // 删除数据
        DB::table('product')->whereIn('id', $id)->delete();
        
        return $this->success('index', '恭喜你，服务删除成功。');
    }
}
