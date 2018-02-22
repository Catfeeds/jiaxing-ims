<?php namespace Aike\Web\Supplier\Controllers;

use DB;
use Request;
use Input;

use Aike\Web\Index\Controllers\DefaultController;
use Aike\Web\Supplier\Warehouse;

use Aike\Web\Supplier\Budget;

class BudgetController extends DefaultController
{
    public $permission = ['store'];

    public $validate = [
        'rules' => [
            'number'  => 'required',
            'type_id' => 'required',
            'date'    => 'required'
        ],
        'attrs' => [
            'number'  => '单据编号',
            'type_id' => '库存类型',
            'date'    => '单据日期'
        ],
    ];

    // 进出库列表
    public function indexAction()
    {
        $search = search_form([
            'referer' => 1,
        ], [
            ['text','supplier_budget.number','单据编号'],
            ['second','supplier_budget.add_time','单据日期'],
        ]);

        $query = $search['query'];

        $model = Budget::with('datas');

        /*
        $access = User::authoriseAccess('index');
        if($access) {
            $model->whereIn('supplier_budget.add_user_id', $access);
        }
        */

        foreach ($search['where'] as $where) {
            if ($where['active']) {
                $model->search($where);
            }
        }
        
        $rows = $model->orderBy('supplier_budget.id', 'desc')
        ->paginate($search['limit'])->appends($query);

        foreach ($rows as &$row) {
            $row['quantity'] = $row->datas->sum('quantity');
        }

        // 返回json
        if (Request::wantsJson()) {
            return $rows->toJson();
        }

        return $this->display([
            'rows'   => $rows,
            'search' => $search,
        ]);
    }

    // 成品出入库单
    public function createAction()
    {
        // 统计数量
        $budget_count = DB::table('supplier_budget')->count('id');

        // 预算编号
        $budget['sn'] = date('ym-').($budget_count + 1);

        $models = [
            ['name' => "id", 'hidden' => true],
            ['name' => 'os', 'label' => '&nbsp;', 'formatter' => 'options', 'width' => 60, 'sortable' => false, 'align' => 'center'],
            ['name' => "product_id", 'hidden' => true, 'label' => '商品编号'],
            ['name' => "product_name", 'width' => 280, 'label' => '商品', 'rules' => ['required'=>true], 'sortable' => false, 'editable' => true],
            ['name' => "quantity", 'label' => '数量', 'width' => 140, 'rules' => ['required' => true, 'minValue' => 1,'integer' => true], 'formatter' => 'integer', 'sortable' => false, 'editable' => true, 'align' => 'right'],
            ['name' => "description", 'label' => '备注', 'width' => 200, 'sortable' => false, 'editable' => true]
        ];

        return $this->display([
            'budget'   => $budget,
            'validate' => $this->validate,
            'models'   => $models,
        ]);
    }

    // 保存数据
    public function storeAction()
    {
        if (Request::method() == 'POST') {
            $gets = Input::get();

            // 检查产品是否没有选择
            $products = is_array($gets['products']) ? $gets['products'] : json_decode($gets['products'], true);

            if (empty($products)) {
                return $this->json('商品列表不能为空。');
            }

            if ($gets['sn']) {
                // 写入库或出入主表
                $budget['sn'] = $gets['sn'];
            } else {
                // 统计数量
                $budget_count = DB::table('supplier_budget')->count('id');
                // 写入库或出入主表
                $budget['sn'] = date('ym-').($budget_count + 1);
            }

            // 入库日期
            $budget['date'] = $gets['date'] == '' ? date('Y-m-d') : $gets['date'];
            
            $insert_id = DB::table('supplier_budget')->insertGetId($budget);
 
            foreach ($products as $row) {
                $data = [
                    'budget_id'   => $insert_id,
                    'product_id'  => $row['product_id'],
                    'quantity'    => $row['quantity'],
                    'description' => (string)$row['description'],
                ];
                // 写入库数据表
                DB::table('supplier_budget_data')->insert($data);
            }

            notify()->sms(['18990305012'], '采购周期预算 - 有新的单据：'.$budget['sn'].'，日期：'.$budget['date'].'，请查看。');

            return $this->json('数据提交成功。', true);
        }
    }

    // 入库记录
    public function showAction()
    {
        $id = Input::get('id');
        
        $rows = DB::table('supplier_budget_data')
        ->leftJoin('product', 'product.id', '=', 'supplier_budget_data.product_id')
        ->leftJoin('product_category', 'product_category.id', '=', 'product.category_id')
        ->where('supplier_budget_data.budget_id', $id)
        ->orderBy('product_category.id', 'ASC')
        ->orderBy('product.id', 'ASC')
        ->selectRaw('supplier_budget_data.*,supplier_budget_data.description budget_description,product.name,product.spec,product_category.name as category_name')
        ->get();

        // 返回json
        if (Request::wantsJson()) {
            return response()->json($rows);
        }
        
        return $this->render([
            'rows' => $rows,
        ]);
    }

    // 删除周期预算
    public function deleteAction()
    {
        if (Request::method() == 'POST') {
            $id = Input::get('id');

            DB::table('supplier_budget')->whereIn('id', $id)->delete();
            DB::table('supplier_budget_data')->whereIn('budget_id', $id)->delete();

            return $this->success('index', '恭喜你，删除成功。');
        }
    }
}
