<?php namespace Aike\Web\User\Controllers;

use DB;
use Input;
use Request;

use Aike\Web\User\UserGroup;

use Aike\Web\Index\Controllers\DefaultController;

class GroupController extends DefaultController
{
    // 用户组
    public function indexAction()
    {
        $columns = [[
            'name'     => 'name',
            'index'    => 'user_group.name',
            'search'   => 'text',
            'label'    => '名称',
            'minWidth' => 200,
            'align'    => 'left',
        ],[
            'name'    => 'sort',
            'index'   => 'user_group.sort',
            'label'   => '排序',
            'width'   => 120,
            'align'   => 'center',
        ]/*,[
            'name'     => 'status',
            'index'    => 'user_group.status',
            'label'    => '状态',
            'width'    => 100,
            'search'   => [
                'type' => 'select',
                'data' => ['1' => '启用', '0' => '停用'],
            ],
            'formatter' => 'status',
            'align'     => 'center',
        ]*/,[
            'name'    => 'updated_at',
            'index'   => 'user_group.updated_at',
            //'search'  => 'second2',
            'label'   => '操作时间',
            'width'   => 140,
            'formatter' => 'date',
            'formatoptions' => [
                'srcformat' => 'u',
                'newformat' => 'Y-m-d H:i'
            ],
            'align' => 'center',
        ],[
            'name'  => 'id',
            'index' => 'user_group.id',
            'label' => 'ID',
            'width' => 60,
            'align' => 'center',
        ]];

        $search_columns = search_columns($columns);
        $search = search_form([
            'referer'  => 1,
            'advanced' => 1,
        ], $search_columns);

        $query = $search['query'];

        $model = UserGroup::orderBy('sort', 'asc');

        foreach ($search['where'] as $where) {
            if ($where['active']) {
                $model->search($where);
            }
        }

        if (Input::ajax()) {
            return response()->json($model->get());
        }

        return $this->display([
            'search'  => $search,
            'columns' => $columns,
        ]);
    }

    // 添加用户组
    public function addAction()
    {
        $id = (int)Input::get('id');

        if (Request::method() == 'POST') {
            $gets = Input::get();

            if (empty($gets['name'])) {
                return $this->back()->with('error', '用户组名称必须填写。');
            }

            if ($gets['id']) {
                DB::table('user_group')->where('id', $gets['id'])->update($gets);
            } else {
                DB::table('user_group')->insert($gets);
            }
            return $this->json('恭喜你，用户组更新成功。', true);
        }

        $row = DB::table('user_group')->where('id', $id)->first();

        return $this->render(array(
            'row'  => $row,
        ));
    }

    // 编辑用户组
    public function editAction()
    {
        $id = (int)Input::get('id');

        if (Request::method() == 'POST') {
            $gets = Input::get();

            if (empty($gets['name'])) {
                return $this->back()->with('error', '用户组名称必须填写。');
            }

            if ($gets['id']) {
                DB::table('user_group')->where('id', $gets['id'])->update($gets);
            } else {
                DB::table('user_group')->insert($gets);
            }
            return $this->json('恭喜你，用户组更新成功。', true);
        }

        $row = DB::table('user_group')->where('id', $id)->first();

        return $this->render(array(
            'row'  => $row,
        ), 'add');
    }

    // 删除用户组
    public function deleteAction()
    {
        if (Request::method() == 'POST') {
            $id = (array)Input::get('id');
            if (empty($id)) {
                return $this->back()->with('error', '最少选择一行记录。');
            }
            UserGroup::whereIn('id', $id)->delete();
            return $this->json('恭喜你，操作成功。', true);
        }
    }
}
