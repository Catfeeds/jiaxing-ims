<?php namespace Aike\Web\Stock\Controllers;

use DB;
use Input;
use Request;
use Validator;

use Aike\Web\Stock\Product;
use Aike\Web\Stock\ProductCategory;
use Aike\Web\Stock\Warehouse;

use Aike\Web\Index\Controllers\DefaultController;

class ProductController extends DefaultController
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
            'label'    => '商品名称',
            'minWidth' => 200,
            'align'    => 'left',
        ],[
            'name'     => 'spec',
            'index'    => 'product.spec',
            'search'   => 'text',
            'label'    => '商品规格',
            'width' => 200,
            'align'    => 'left',
        ],[
            'name'     => 'category_name',
            'index'    => 'product.category_id',
            'search'   => [
                'type' => 'select',
                'url'  => 'stock/product-category/dialog',
            ],
            'label'    => '商品类别',
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
            'formatter' => 'select',
            'label'    => '所属门店',
            'width' => 160,
            'align'    => 'center',
        ],[
            'name'     => 'is_share',
            'index'    => 'product.is_share',
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

        /*
        $rows = readExcel(base_path('/src/3.xls'));
        foreach ($rows as $row) {
            DB::table('product_category')->insert([
                'name' => $row[0],
                'type' => 2,
            ]);
        }
        exit;
        */

        //print_r($rows);
        //exit;
        /*
        $rows = DB::table('product_category')->get();
        foreach ($rows as $row) {
            if ($row['remark'] != '顶级分类') {
                $cat = DB::table('product_category')->where('name', $row['remark'])->first();
                DB::table('product_category')->where('id', $row['id'])->update(['parent_id' => $cat['id']]);
            }
        }
        */

        /*
        // 导入商品
        $categorys = DB::table('product_category')->where('type', 2)->get()->keyBy('name');
        
        $rows = readExcel(base_path('/src/4.xls'));

        foreach ($rows as $row) {

            if ($row[0]) {
                DB::table('product')->insert([
                    'name' => $row[0],
                    'spec' => $row[1],
                    'unit' => $row[2],
                    'category_id' => (int)$categorys[$row[4]]['id'],
                    'price' => $row[3],
                    'type' => 2,
                ]);
            }
        }

        exit;
        */
        
        //print_r($rows);
        //exit;
        /*
        $rows = DB::table('product_category')->get();
        foreach ($rows as $row) {
            if ($row['remark'] != '顶级分类') {
                $cat = DB::table('product_category')->where('name', $row['remark'])->first();
                DB::table('product_category')->where('id', $row['id'])->update(['parent_id' => $cat['id']]);
            }
        }
        */

        if (Input::ajax()) {
            $model = Product::where('product.type', 1)
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

            $gets['type'] = 1;
            $model->fill($gets)->save();
            return $this->json('恭喜你，产品更新成功。', true);
        }

        $id = (int)Input::get('id');
        $row = DB::table('product')->where('id', $id)->first();

        $categorys = ProductCategory::where('type', 1)->orderBy('lft', 'asc')->get()->toNested();
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

            $gets['type'] = 1;
            $model->fill($gets)->save();
            return $this->json('恭喜你，产品更新成功。', true);
        }

        $id = (int)Input::get('id');
        $row = DB::table('product')->where('id', $id)->first();

        $categorys = ProductCategory::where('type', 1)->orderBy('lft', 'asc')->get()->toNested();
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
        $search = search_form([
            'advanced'    => 1,
            'page'        => 1,
            'category_id' => 0,
            'sort'        => '',
            'order'       => '',
            'limit'       => '',
        ], [
            ['text2','product.name|product.spec|product.barcode|product.id','名称 / 规格 / 条码 / ID'],
        ]);

        $query = $search['query'];

        if (Request::method() == 'POST') {
            $model = Product::with('stockWarehouses')
            ->leftJoin('product_category', 'product_category.id', '=', 'product.category_id')
            ->leftJoin('warehouse', 'warehouse.id', '=', 'product.warehouse_id')
            ->where('product.type', 1)
            ->where('product.status', 1)
            ->orderBy('product_category.lft', 'asc');

            // 排序方式
            if ($query['sort'] && $query['order']) {
                $model->orderBy($query['sort'], $query['order']);
            } else {
                $model->orderBy('product.sort', 'asc');
            }

            if ($query['category_id']) {
                $category = DB::table('product_category')->where('id', $query['category_id'])->first(['lft', 'rgt']);
                $model->whereBetween('product_category.lft', [$category['lft'], $category['rgt']]);
            }

            // 搜索条件
            foreach ($search['where'] as $where) {
                if ($where['active']) {
                    $model->search($where);
                }
            }

            $model->selectRaw("
                product.id as id,
                product.name as text,
                product.name as product_name,
                product.unit as product_unit,
                product.spec as product_spec,
                product.price as product_price,
                product.barcode as product_barcode,
                product_category.name as category_name,
                warehouse.name as warehouse_name,
                warehouse.id as warehouse_id,
                1 as quantity
            ");

            $rows = $model->paginate($query['limit']);

            $rows->transform(function ($item, $v) {
                $item['last_price'] = $item->stockWarehouses[0]->last_price;
                $item['stock_quantity'] = $item->stockWarehouses->sum('stock_quantity');
                return $item;
            });

            return response()->json($rows);
        }
        $gets = Input::get();
        return $this->render([
            'search' => $search,
            'gets'   => $gets,
        ]);
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
        
        return $this->success('index', '恭喜你，产品删除成功。');
    }
}
