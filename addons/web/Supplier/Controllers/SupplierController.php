<?php namespace Aike\Web\Supplier\Controllers;

use DB;
use Auth;
use Input;
use Request;
use Validator;

use Totp;

use Aike\Web\User\User;
use Aike\Web\Index\Attachment;
use Aike\Web\Supplier\Supplier;
use Aike\Web\Supplier\Contact;
use Aike\Web\Supplier\Order;
use Aike\Web\Supplier\PriceData;

use Aike\Web\Index\Controllers\DefaultController;

class SupplierController extends DefaultController
{
    public $permission = ['dialog','owner'];

    public function indexAction()
    {
        $search = search_form([
            'status'   => 1,
            'referer'  => 1,
        ], [
            ['text','user.nickname','供应商名称'],
            ['text','user.username','供应商代码'],
            ['text','supplier.nature','公司性质'],
        ]);

        $query = $search['query'];

        $model = Supplier::LeftJoin('user', 'user.id', '=', 'supplier.user_id')
        ->where('supplier.category_id', 1)
        ->where('user.group_id', 4)
        ->where('user.status', $query['status']);

        if (authorise() == 1) {
            $model->where('supplier.user_id', Auth::id());
        }

        foreach ($search['where'] as $where) {
            if ($where['active']) {
                $model->search($where);
            }
        }

        $rows = $model->with('user', 'contact.user')
        ->select(['supplier.*'])
        ->paginate()->appends($query);

        $tabs = [
            'name'  => 'status',
            'items' => [
                ['id' => 1, 'name' => '启用'],
                ['id' => 0, 'name' => '停用'],
            ]
        ];

        return $this->display(array(
            'rows'   => $rows,
            'query'  => $query,
            'tabs'   => $tabs,
            'search' => $search,
        ));
    }
    
    // 供应商资料
    public function showAction()
    {
        $id = Input::get('id');
        $supplier = Supplier::with('user')->where('id', $id)->first();
        $contacts = Contact::with('user')->where('supplier_id', $id)->get();
        $attach   = Attachment::view($supplier->attachment);

        $prices = PriceData::leftJoin('supplier_price', 'supplier_price.id', '=', 'supplier_price_data.price_id')
        ->where('supplier_price_data.supplier_id', $id)
        ->where('supplier_price.status', 1)
        ->groupBy('supplier_price_data.product_id')
        ->selectRaw('max(supplier_price_data.date), supplier_price_data.price, supplier_price_data.product_id')
        ->pluck('price', 'product_id');

        $t = new Totp();
        $secretURL = $t->getURL($supplier->user->username, Request::server('HTTP_HOST'), $supplier->user->auth_secret);

        $products = $supplier->products()->get();

        return $this->display([
            'supplier'  => $supplier,
            'contacts'  => $contacts,
            'files'     => $attach['main'],
            'prices'    => $prices,
            'secretURL' => $secretURL,
            'products'  => $products,
        ]);
    }

    // 账户修改
    public function createAction()
    {
        if (Request::method() == 'POST') {
            $gets = Input::get();

            $_user     = $gets['user'];
            $_supplier = $gets['supplier'];

            $rules = [
                'user.nickname'    => 'required',
                'user.username'    => 'required|unique:user,username,'.$_user['id'],
            ];

            $v = Validator::make($gets, $rules);
            if ($v->fails()) {
                return $this->back()->withErrors($v)->withInput();
            }

            $_user['status']    = (int)$_user['status'];
            $_user['auth_totp'] = (int)$_user['auth_totp'];
            $_user['address']   = join("\n", $_user['address']);
            $_user['role_id']   = 54;
            $_user['group_id']  = 4;

            $user = User::findOrNew($_user['id']);
            // 帐号
            $user->username = $_user['username'];
            
            // 账户密码
            if ($_user['password']) {
                $user->password = bcrypt($_user['password']);
            }
            $user->fill($_user)->save();

            $supplier = Supplier::findOrNew($_supplier['id']);
            $supplier->user_id  = $user->id;

            $_supplier['attachment']  = join(',', (array)$gets['attachment']);
            $_supplier['category_id'] = 1;

            // 上传营业执照
            if ($_supplier['image']) {
                $_supplier['image'] = image_create('supplier', 'supplier.image', $supplier->image);
            }

            $supplier->fill($_supplier)->save();

            // 附件发布
            Attachment::publish();

            // 首选联系人
            if ($gets['contact']) {
                $_user    = $gets['contact'];
                $_contact = $gets['supplier_contact'];

                $_user['lunar_birthday'] = (int)$_user['lunar_birthday'];
                $_user['group_id']       = 5;

                $user = new User;
                $user->username = uniqid('sc_');
                $user->fill($_user)->save();

                $contact = new Contact;
                $contact->user_id     = $user->id;
                $contact->supplier_id = $supplier->id;
                $contact->fill($_contact)->save();
            }

            return $this->success('index', '恭喜您，供应商资料提交成功。');
        }

        $id = (int)Input::get('id');
        $supplier = Supplier::with('user')->where('id', $id)->first();
        $contacts = Contact::with('user')->where('supplier_id', $id)->get();

        if ($supplier->user->address) {
            $supplier->user->address = explode("\n", $supplier->user->address);
        }

        // 附件
        $attach = Attachment::edit($supplier->attachment);

        return $this->display([
            'supplier' => $supplier,
            'contacts' => $contacts,
            'attach'   => $attach,
            'query'    => $query,
        ]);
    }

    // 供应商对话框
    public function dialogAction()
    {
        $search = search_form([
            'advanced' => '',
            'sort'     => '',
            'order'    => '',
            'offset'   => 0,
            'limit'    => 10,
        ], [
            ['text','user.nickname','姓名'],
            ['text','user.username','账号'],
            ['text','user.id','编号'],
            ['address','user.address','地址'],
        ]);

        $query = $search['query'];

        if (Request::method() == 'POST') {
            $model = Supplier::LeftJoin('user', 'user.id', '=', 'supplier.user_id');
            $model->where('category_id', 1);

            // 排序方式
            if ($query['sort'] && $query['order']) {
                $model->orderBy($query['sort'], $query['order']);
            }

            // 搜索条件
            foreach ($search['where'] as $where) {
                if ($where['active']) {
                    $model->search($where);
                }
            }

            $json['total'] = $model->count();
            $rows = $model->skip($query['offset'])->take($query['limit'])
            ->get(['supplier.id', 'supplier.user_id', 'user.role_id', 'user.status', 'user.username', 'user.nickname', 'user.email', 'user.mobile']);
            
            $json['data'] = $rows;
            return response()->json($json);
        }
        $get = Input::get();

        return $this->render(array(
            'search' => $search,
            'get'    => $get,
        ));
    }

    // 删除供应商
    public function deleteAction()
    {
        if (Request::method() == 'POST') {
            $id = Input::get('id');
            $id = array_filter((array)$id);

            if (empty($id)) {
                return $this->error('请先选择数据。');
            }

            // 删除供应商和用户用户档案
            $rows = Supplier::whereIn('id', $id)->get();
            foreach ($rows as $row) {
                // 删除供应商关联的用户表
                User::where('id', $row->user_id)->delete();

                // 删除供应商联系人
                Contact::where('supplier_id', $row->id)->delete();

                // 删除供应商联系人用户表
                // User::where('id', $row->user_id)->delete();

                // 删除供应商商品关系表
                $row->products()->sync([]);

                // 删除营业执照
                image_delete($row->image);

                // 删除供应商附件
                Attachment::delete($row->attachment);

                // 删除自己
                $row->delete();
            }
            return $this->success('index', '恭喜你，操作成功。');
        }
    }
}
