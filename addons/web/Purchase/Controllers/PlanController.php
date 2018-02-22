<?php namespace Aike\Web\Purchase\Controllers;

use Illuminate\Http\Request;

use DB;
use Input;
use Validator;
use Auth;

use Aike\Web\Flow\Form;
use Aike\Web\Flow\Table;

use Aike\Web\User\User;
use Aike\Web\Purchase\Plan;
use Aike\Web\Purchase\PlanData;
use Aike\Web\Supplier\Product;
use Aike\Web\Supplier\ProductCategory;
use Aike\Web\Index\Controllers\DefaultController;

class PlanController extends DefaultController
{
    public $permission = [];
    
    public function indexAction()
    {
        $options = [];
        $users = User::authoriseAccess();

        // 用户权限
        if ($users) {
            $options['whereIn'] = [
                // 'purchase_plan.created_by' => $users
            ];
        }
        return Table::make('purchase_plan', $options);
    }

    // 计划显示
    public function showAction(Request $request)
    {
        $options = [];
        return Table::show('purchase_plan', $options);
    }

    // 新建计划
    public function createAction(Request $request)
    {
        $options = [];
        return Form::make('purchase_plan', $options);
    }

    // 新建计划
    public function editAction(Request $request)
    {
        $options = [];
        return Form::make('purchase_plan', $options);
    }

    // 删除计划
    public function deleteAction(Request $request)
    {
        $options = [];
        return Form::remove('purchase_plan', $options);
    }
}
