<?php namespace Aike\Web\Supplier\Controllers;

use DB;
use Auth;
use Input;
use Request;
use Validator;

use Aike\Web\User\User;
use Aike\Web\Supplier\Supplier;
use Aike\Web\Supplier\Quality;
use Aike\Web\Supplier\Product;

use Aike\Web\Index\Controllers\DefaultController;

class QualityController extends DefaultController
{
    public $permission = ['print', 'edit'];

    // 质量列表
    public function indexAction()
    {
        $search = search_form([
            'referer' => 1,
        ], [
            ['text','supplier_quality.name','主题'],
            ['category','supplier_quality.category_id','类别'],
            ['supplier','supplier_quality.supplier_id','供应商'],
        ]);
        $query  = $search['query'];

        $model = Quality::stepAt();

        if (authorise() == 1) {
            $supplier = Supplier::where('user_id', Auth::id())->first();
            $model->where('supplier_id', $supplier->id);
        }

        foreach ($search['where'] as $where) {
            if ($where['active']) {
                $model->search($where);
            }
        }
       
        $rows = $model->with('supplier.user', 'product')
        ->orderBy('id', 'desc')
        ->paginate()
        ->appends($query);
        
        $_suppliers = Supplier::with('user')->get();
        $suppliers = [];
        foreach ($_suppliers as $supplier) {
            $suppliers[] = [
                'id'   => $supplier->id,
                'name' => $supplier->user->nickname
            ];
        }

        return $this->display([
            'rows'      => $rows,
            'suppliers' => $suppliers,
            'search'    => $search,
        ]);
    }

    // 新建质量
    public function createAction()
    {
        $id = Input::get('id');

        $row  = Quality::stepAt()->findOrNew($id);
        $step = get_step_status($row);

        $rules = [
            'name'        => 'required',
            'product_id'  => 'required',
            'category_id' => 'required',
        ];

        if (Request::method() == 'POST') {
            $gets = Input::get();

            $product = Product::find($gets['product_id']);
            if (!$product->supplier_id) {
                return $this->back('商品未指定供应商。');
            }
            
            $gets['supplier_id'] = $product->supplier_id;

            $v = Validator::make($gets, $rules);
            if ($v->fails()) {
                return $this->back()->withErrors($v)->withInput();
            }
            $row->fill($gets)->save();

            return $this->success('index', '恭喜你，操作成功。');
        }

        $fields = get_step_field($row->step->field);
        return $this->display([
            'row'    => $row,
            'step'   => $step,
            'rules'  => $rules,
            'fields' => $fields,
        ], 'create');
    }

    // 编辑质量
    public function editAction()
    {
        return $this->createAction();
    }

    // 显示质量
    public function showAction()
    {
        $id = Input::get('id');
        $row = Quality::find($id);
        $step = get_step_status($row);

        return $this->display([
            'row'  => $row,
            'step' => $step,
        ]);
    }

    // 打印
    public function printAction()
    {
        $id = Input::get('id');
        $row = Quality::find($id);

        $this->layout = 'layouts.print';
        return $this->display([
            'row' => $row,
        ]);
    }

    // 删除质量
    public function deleteAction()
    {
        if (Request::method() == 'POST') {
            $id = (array)Input::get('id');
            Quality::whereIn('id', $id)->delete();
            return $this->back('删除成功。');
        }
    }
}
