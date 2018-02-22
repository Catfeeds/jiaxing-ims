<?php namespace Aike\Web\Customer\Controllers;

use Auth;
use Input;
use Session;
use Request;

use Aike\Web\Index\Access;
use Aike\Web\User\User;
use Aike\Web\Customer\Customer;
use Aike\Web\Customer\Contact;

use Aike\Web\User\Controllers\TokenController as Controller;

class TokenController extends Controller
{
    public function salesmanAction()
    {
        if (Input::isJson()) {
            $gets = json_decode(Request::getContent(), true);
        } else {
            $gets = Input::get();
        }
        
        $username = trim($gets['username']);
        $password = trim($gets['password']);

        if ($username == '') {
            return response()->json(['message'=>'客户代码不能为空。'], 401);
        }

        if ($password == '') {
            return response()->json(['message'=>'联系人手机不能为空。'], 401);
        }
        
        // 获取登录用户
        $user = User::where('username', $username)
        ->where('group_id', 2)
        ->where('status', 1)
        ->first();
        
        if ($user) {
            // 获取客户档案
            $customer = Customer::where('user_id', $user->id)->first();
            
            // 登录的客户业务员信息
            $contact = Contact::leftJoin('user', 'user.id', '=', 'customer_contact.user_id')
            ->where('customer_contact.customer_id', $customer->id)
            ->where('user.mobile', $password)
            ->first(['user.*', 'customer_contact.id as contact_id']);
            
            if ($contact) {
                $assets = Access::getRoleAssets($user->role_id);
                return response()->json([
                    'token'      => $this->createToken($user->id),
                    'contact_id' => $contact->contact_id,
                    'access'     => $assets,
                ]);
            } else {
                return response()->json(['message'=>'客户代码或联系人手机错误。'], 401);
            }
        }
        return response()->json(['message'=>'客户代码或联系人手机错误。'], 401);
    }
}
