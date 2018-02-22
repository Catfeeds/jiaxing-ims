<?php namespace Aike\Web\Purchase\Controllers;

use DB;
use Input;
use Illuminate\Http\Request;
use Validator;
use Auth;

use Aike\Web\User\User;
use Aike\Web\Purchase\Order;
use Aike\Web\Purchase\OrderData;
use Aike\Web\Supplier\Product;
use Aike\Web\Supplier\ProductCategory;

use Aike\Web\Index\Controllers\DefaultController;

class OrderController extends DefaultController
{
    public $permission = ['print'];
    
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
        $options['disableCreate'] = true;
        return \Aike\Web\Flow\Table::make('purchase_order', $options);
    }

    // 显示分单
    public function showAction(Request $request)
    {
        $options = [];
        return \Aike\Web\Flow\Table::show('purchase_order', $options);
    }

    // 打印分单
    public function printAction(Request $request)
    {
        $options = [];
        return \Aike\Web\Flow\Table::print('purchase_order', $options);
    }

    // 新建分单
    public function createAction(Request $request)
    {
        $options = [];
        return \Aike\Web\Flow\Form::make('purchase_order', $options);
    }

    // 删除分单
    public function deleteAction(Request $request)
    {
        $options = [];
        return \Aike\Web\Flow\Form::remove('purchase_order', $options);
    }
}
