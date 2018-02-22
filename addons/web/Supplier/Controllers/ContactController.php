<?php namespace Aike\Web\Supplier\Controllers;

use DB;
use Auth;
use Input;
use Request;
use Validator;

use Aike\Web\User\User;
use Aike\Web\Supplier\Supplier;
use Aike\Web\Supplier\Contact;
use Aike\Web\Index\Controllers\DefaultController;

class ContactController extends DefaultController
{
    // 供应商联系人
    public function indexAction()
    {
        $search = search_form([
            'referer' => 1
        ], [
            ['text','supplier_contact_user.nickname','姓名'],
            ['date','supplier_contact_user.birthday','生日'],
            ['contact.post','supplier_contact_user.post','职位'],
        ]);
        $query  = $search['query'];

        $model = Contact::LeftJoin('user as supplier_contact_user', 'supplier_contact_user.id', '=', 'supplier_contact.user_id');
        
        if (authorise() == 1) {
            $supplier = Supplier::where('user_id', Auth::id())->first();
            $model->where('supplier_contact.supplier_id', $supplier->id);
        }

        foreach ($search['where'] as $where) {
            if ($where['active']) {
                $model->search($where);
            }
        }

        $rows = $model->select(['supplier_contact.*'])
        ->with('user', 'supplier.user')->paginate()->appends($query);

        return $this->display([
            'rows'    => $rows,
            'search'  => $search,
            'owners'  => $filter['owner'],
        ]);
    }

    // 新建供应商联系人
    public function createAction()
    {
        $id = Input::get('id');

        $row = Contact::findOrNew($id);

        if (Request::method() == 'POST') {
            $gets = Input::get();

            $gets['user']['group_id'] = 5;

            $rules = [
                'contact.supplier_id' => 'required',
                'user.nickname' => 'required',
            ];

            $v = Validator::make($gets, $rules);
            if ($v->fails()) {
                return $this->back()->withErrors($v)->withInput();
            }

            if ($row->user) {
                $user = $row->user;
            } else {
                $user = new User;
                $user->username = uniqid('sc_');
            }
            $user->fill($gets['user'])->save();

            $row->user_id = $user->id;
            $row->fill($gets['contact'])->save();

            return $this->success('index', '恭喜你，操作成功。');
        }

        $supplier_id = Input::get('supplier_id');
        if ($supplier_id > 0 && $row->supplier_id == 0) {
            $row->supplier_id = $supplier_id;
        }

        return $this->display([
            'row' => $row,
        ]);
    }

    // 显示供应商联系人
    public function showAction()
    {
        $id = Input::get('id');
        $row = Contact::find($id);

        return $this->display([
            'row' => $row,
        ]);
    }

    // 删除供应商联系人
    public function deleteAction()
    {
        if (Request::method() == 'POST') {
            $id = (array)Input::get('id');
            Contact::whereIn('id', $id)->delete();
            return $this->back('删除成功。');
        }
    }
}
