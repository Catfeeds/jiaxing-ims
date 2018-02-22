<?php namespace Aike\Web\Supplier\Controllers;

use Illuminate\Http\Request;

use Aike\Web\Supplier\Product;
use Aike\Web\Supplier\ProductCategory;
use Aike\Web\Supplier\Warehouse;
use Aike\Web\Index\Controllers\DefaultController;

class ProductCategoryController extends DefaultController
{
    public $permission = ['dialog'];

    // 产品分类列表
    public function indexAction(Request $request)
    {
        $search = search_form([
            'referer' => 1,
        ]);
        
        if ($request->method() == 'POST') {
            $gets = $request->input('id');

            foreach ($gets as $id => $sort) {
                ProductCategory::where('id', $id)->update(['sort' => $sort]);
            }
            ProductCategory::treeRebuild();
            return $this->back('排序完成。');
        }

        $rows = ProductCategory::type('supplier')->orderBy('lft', 'asc')->get()->toNested();
        return $this->display([
            'rows' => $rows,
        ]);
    }

    // 新建产品类别
    public function createAction(Request $request)
    {
        $id = (int)$request->input('id');

        if ($request->method() == 'POST') {
            $gets = $request->all();

            $this->validate($request, [
                'name' => 'required',
                'code' => 'required|unique:product_category,code,'.$gets['id'],
            ], [
                'name.required' => '类别名称必须填写。',
                'code.required' => '类别代码必须填写。',
                'code.unique' => '类别代码已经存在。',
            ]);

            $gets['type'] = 2;

            $category = ProductCategory::findOrNew($gets['id']);
            $category->fill($gets)->save();
            // 重构树形结构
            $category->treeRebuild();

            return $this->success('index', '恭喜你，操作成功。');
        }

        $categorys = ProductCategory::type('supplier')->orderBy('lft', 'asc')->get()->toNested();
        $category = ProductCategory::find($id);

        $warehouses = Warehouse::type('supplier')->orderBy('lft', 'asc')->get()->toNested();

        return $this->display(array(
            'categorys'  => $categorys,
            'warehouses' => $warehouses,
            'category'   => $category,
        ));
    }

    // 删除商品类别
    public function deleteAction(Request $request)
    {
        $id = $request->input('id');
        if (!$id) {
            return $this->error('编号不正确无法显示。');
        }

        $product = Product::where('category_id', $id)->first();
        if ($product) {
            return $this->error('类别下面存在商品，无法删除。');
        }

        ProductCategory::where('id', $id)->delete();

        return $this->success('index', '恭喜你，操作成功。');
    }

    /**
     * 弹出层信息
     */
    public function dialogAction(Request $request)
    {
        $gets = $request->all();

        if ($request->ajax()) {
            $maps = ProductCategory::type('supplier')->orderBy('lft', 'asc')->get()->toNested();

            $rows = [];
            foreach ($maps as $row) {
                $rows[] = $row;
            }
            $json = ['total' => count($rows), 'rows' => $rows];
            return response()->json($json);
        }
        return $this->render(array(
            'gets' => $gets,
        ));
    }
}
