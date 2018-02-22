<?php namespace Aike\Web\Customer\Controllers;

use DB;
use Input;
use Request;
use Validator;

use Aike\Web\Index\Controllers\DefaultController;

class BankController extends DefaultController
{
    /**
     * 添加和编辑银行信息
     */
    public function addAction()
    {
        $gets = Input::get();

        $status = array(0=>'-', 1=>'启用', 2=>'停用');

        if (Request::method() == 'POST') {
            $rules = array(
                'tax_name'   => 'required',
                'tax_number' => 'required',
                'status'     => 'required',
            );
            $v = Validator::make($gets, $rules);

            if ($v->fails()) {
                return $this->back()->withErrors($v)->withInput();
            }

            if ($gets['id'] > 0) {
                DB::table('customer_bank')->where('id', $gets['id'])->update($gets);
            } else {
                DB::table('customer_bank')->insert($gets);
            }

            $customer = DB::table('client')->where('user_id', $gets['client_id'])->first();
            return $this->success('customer/view', ['id'=>$customer['id']], '恭喜你，操作成功。');
        }

        $row = DB::table('customer_bank')->where('id', $gets['id'])->first();

        return $this->display([
            'gets'   => $gets,
            'row'    => $row,
            'status' => $status,
        ], 'add');
    }

    /**
     * 添加和编辑银行信息
     */
    public function editAction()
    {
        return $this->addAction();
    }

    /**
     * 删除银行信息
     */
    public function deleteAction()
    {
        $gets = Input::get();
        if ($gets['id'] > 0) {
            DB::table('customer_bank')->where('id', $gets['id'])->delete();
            return $this->success('customer/view', ['id'=>$gets['client_id']], '恭喜你，操作成功。');
        }
    }
}
