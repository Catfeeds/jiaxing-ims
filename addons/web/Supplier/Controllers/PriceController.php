<?php namespace Aike\Web\Supplier\Controllers;

use Illuminate\Http\Request;
use Validator;
use Auth;

use Hook;

use Aike\Web\User\User;
use Aike\Web\Supplier\Supplier;
use Aike\Web\Supplier\Price;
use Aike\Web\Supplier\PriceData;
use Aike\Web\Supplier\Product;
use Aike\Web\Supplier\ProductCategory;

use Aike\Web\Flow\Form;
use Aike\Web\Flow\Table;

use Aike\Web\Index\Attachment;

use Aike\Web\Index\Controllers\DefaultController;

class PriceController extends DefaultController
{
    public $permission = ['order', 'print'];

    public function indexAction(Request $request)
    {
        $options = [];
        $users = User::authoriseAccess();
        // 用户权限
        if ($users) {
            $options['whereIn'] = [
                // 'purchase_plan.created_by' => $users
            ];
        }
        return Table::make('supplier_price', $options);

        $search = search_form([
            'referer' => 1,
            'status'  => 0
        ], [
            ['text','supplier_price.sn','单号'],
            ['text','user.nickname','供应商'],
            ['second','supplier_price.created_at','创建时间'],
        ]);

        $query = $search['query'];

        $model = Price::stepAt()
        ->with('supplier.user')
        ->leftJoin('supplier', 'supplier.id', '=', 'supplier_price.supplier_id')
        ->leftJoin('user', 'user.id', '=', 'supplier.user_id')
        ->where('supplier_price.status', $query['status'])
        ->orderBy('supplier_price.id', 'desc')
        ->select(['supplier_price.*']);

        foreach ($search['where'] as $where) {
            if ($where['active']) {
                $model->search($where);
            }
        }

        $tabs = [
            'name'  => 'status',
            'items' => Price::$tabs
        ];

        $rows = $model->paginate()->appends($query);
        return $this->display([
            'rows'   => $rows,
            'search' => $search,
            'tabs'   => $tabs,
        ]);
    }

    // 显示订单
    public function showAction(Request $request)
    {
        $options = [];
        return Table::show('supplier_price', $options);
    }

    // 新建订单
    public function createAction(Request $request)
    {
        $options = [
        ];
        return Form::make('supplier_price', $options);
    }

    // 新建订单
    public function editAction(Request $request)
    {
        $options = [];
        return Form::make('supplier_price', $options);
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

            Price::whereIn('id', $id)->delete();
            PriceData::whereIn('price_id', $id)->delete();

            return $this->success('index', '恭喜你，操作成功。');
        }
    }
}
