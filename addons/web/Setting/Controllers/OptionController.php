<?php namespace Aike\Web\Setting\Controllers;

use DB;
use Input;
use Request;
use Validator;

use Aike\Web\Index\Controllers\DefaultController;

class OptionController extends DefaultController
{
    // 选项列表
    public function indexAction()
    {
        if (Request::method() == 'POST') {
            $sorts = Input::get('sort');
            foreach ($sorts as $id => $sort) {
                DB::table('option')->where('id', $id)->update(['sort' => $sort]);
            }
            return $this->success('index', '恭喜你，操作成功。');
        }

        $parent_id = (int)Input::get('parent_id');
        $search = search_form([
            'referer'  => 1,
        ], []);
        
        $rows = DB::table('option')
        ->where('parent_id', $parent_id)
        ->orderBy('sort', 'asc')
        ->paginate($search['limit']);

        $parent = DB::table('option')->where('id', $parent_id)->first();
        return $this->display([
            'rows'      => $rows,
            'parent'    => $parent,
            'parent_id' => $parent_id,
        ]);
    }

    // 新建字典
    public function createAction()
    {
        $id = (int)Input::get('id');

        if (Request::method() == 'POST') {
            $gets = Input::get();

            $rules = [
                'name'  => 'required',
                'value' => 'required',
            ];
            $v = Validator::make($gets, $rules);
            if ($v->fails()) {
                return $this->back()->withErrors($v)->withInput();
            }

            if ($gets['id']) {
                DB::table('option')->where('id', $gets['id'])->update($gets);
            } else {
                DB::table('option')->insert($gets);
            }
            return $this->success('index', '恭喜你，操作成功。');
        }
        $row = DB::table('option')->where('id', $id)->first();
        $parent_id = (int)Input::get('parent_id');
        return $this->display([
            'row'       => $row,
            'parent_id' => $parent_id,
        ]);
    }

    // 删除字典
    public function deleteAction()
    {
        if (Request::method() == 'POST') {
            $id = Input::get('id');

            $rows = DB::table('option')->whereIn('parent_id', $id)->get();
            if ($rows->count()) {
                return $this->error('存在子选项无法删除。');
            }
            $row = DB::table('option')->whereIn('id', $id)->delete();
            return $this->back('删除成功。');
        }
    }
}
