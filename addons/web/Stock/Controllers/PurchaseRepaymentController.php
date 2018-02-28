<?php namespace Aike\Web\Stock\Controllers;

use DB;
use Input;
use Request;
use Validator;

use Aike\Web\Stock\Stock;
use Aike\Web\Stock\StockLine;
use Aike\Web\Stock\Repayment;

use Aike\Web\Index\Controllers\DefaultController;

class PurchaseRepaymentController extends DefaultController
{
    // 还款列表
    public function indexAction()
    {
        $suppliers = DB::table('supplier')->get(['id', 'name as text']);
        $stores    = DB::table('store')->get(['id', 'name as text']);

        $columns = [[
            'name'     => 'sn',
            'index'    => 'stock.sn',
            'search'   => 'text',
            'label'    => '单号',
            'width' => 160,
            'align'    => 'center',
        ],[
            'name'    => 'arear_money',
            'index'   => 'stock_repayment.arear_money',
            'label'   => '欠款金额',
            'width'   => 80,
            'align'   => 'right',
        ],[
            'name'    => 'money',
            'index'   => 'stock_repayment.money',
            'label'   => '还款金额',
            'width'   => 80,
            'align'   => 'right',
        ],[
            'name'    => 'supplier_name',
            'index'   => 'supplier.id',
            'label'   => '供应商',
            'search'   => [
                'type' => 'select',
                'data' => $suppliers,
            ],
            'width'    => 160,
            'align'    => 'center',
        ],[
            'name'    => 'supplier_personal_mobile',
            'index'   => 'supplier.personal_mobile',
            'label'   => '联系电话',
            'search'   => 'text',
            'width'    => 160,
            'align'    => 'center',
        ],[
            'name'     => 'store_name',
            'index'    => 'store.id',
            'label'    => '门店',
            'search'   => [
                'type' => 'select',
                'data' => $stores,
            ],
            'width'   => 160,
            'align'   => 'center',
        ],[
            'name'    => 'date',
            'index'   => 'stock.date',
            'search'  => 'date2',
            'label'   => '采购日期',
            'width'   => 100,
            'formatter' => 'date',
            'formatoptions' => [
                'srcformat' => 'Y-m-d',
                'newformat' => 'Y-m-d'
            ],
            'align' => 'center',
        ],[
            'name'    => 'created_at',
            'index'   => 'stock_repayment.created_at',
            'label'   => '还款时间',
            'width'   => 140,
            'formatter' => 'date',
            'formatoptions' => [
                'srcformat' => 'u',
                'newformat' => 'Y-m-d H:i'
            ],
            'align' => 'center',
        ],[
            'name'    => 'remark',
            'index'   => 'stock_repayment.remark',
            'label'   => '备注',
            'minWidth'   => 140,
            'align'   => 'left',
        ],[
            'name'  => 'id',
            'index' => 'stock_repayment.id',
            'label' => 'ID',
            'width' => 60,
            'align' => 'center',
        ]];

        $search_columns = search_columns($columns);
        $search = search_form([
            'referer'  => 1,
            'advanced' => 1,
        ], $search_columns);

        $model = Repayment::orderBy('stock.id', 'desc')
        ->leftJoin('stock', 'stock_repayment.stock_id', '=', 'stock.id')
        ->leftJoin('supplier', 'supplier.id', '=', 'stock.supplier_id')
        ->leftJoin('store', 'store.id', '=', 'stock.store_id')
        ->select([
            'stock_repayment.*',
            'store.name as store_name',
            'supplier.name as supplier_name',
            'supplier.personal_mobile as supplier_personal_mobile',
            'stock.date',
            'stock.sn',
        ])->where('stock.status', 1)
        ->orderBy('stock.id', 'desc');

        foreach ($search['where'] as $where) {
            if ($where['active']) {
                if ($where['field'] == 'stock.arear_money') {
                    if ($where['search'] == 0) {
                        $model->where($where['field'], 0);
                    } else {
                        $model->where($where['field'], '>', 0);
                    }
                } else {
                    $model->search($where);
                }
            }
        }

        if (Input::ajax()) {
            return response()->json($model->paginate($search['limit']));
        }

        return $this->display([
            'search'  => $search,
            'columns' => $columns,
        ]);
    }

    // 新建采购单据还款
    public function createAction()
    {
        $stock_id = (int)Input::get('stock_id');
        
        if (Request::method() == 'POST') {
            $gets = Input::get();
            
            $stock = Stock::where('id', $stock_id)->first();
            $arear_money = $stock->arear_money;
            $rules = [
                'money' => 'required|numeric|between:1,'.$arear_money,
            ];
            $v = Validator::make($gets, $rules, [], [
                'money' => '还款金额',
            ]);
            if ($v->fails()) {
                return $this->json(join('<br>', $v->errors()->all()));
            }
            $model = new Repayment;
            $model->arear_money = $arear_money;
            $model->fill($gets)->save();

            // 减少欠款金额
            $stock->arear_money = $arear_money - $gets['money'];
            // 增加付款金额
            $stock->pay_money = $stock->pay_money + $gets['money'];
            $stock->save();

            return $this->json('恭喜你，采购入库单还款成功。', true);
        }
        return $this->render(['stock_id' => $stock_id]);
    }

    // 删除采购单据还款
    public function deleteAction()
    {
        if (Request::method() == 'POST') {
            $id = (array)Input::get('id');
            if (empty($id)) {
                return $this->json('最少选择一行记录。');
            }
            Repayment::whereIn('stock_id', $id)->delete();
            return $this->json('恭喜你，采购单据删除成功。', true);
        }
    }
}
