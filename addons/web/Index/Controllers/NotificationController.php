<?php namespace Aike\Web\Index\Controllers;

use DB;
use Auth;
use Input;
use Request;

use Aike\Web\User\User;

class NotificationController extends Controller
{
    public $permission = ['status'];

    /**
     * 消息列表
     */
    public function indexAction()
    {

        $columns = [[
            'name'     => 'content',
            'index'    => 'message.content',
            'search'   => 'text',
            'label'    => '内容',
            'minWidth' => 320,
            'align'    => 'left',
        ],[
            'name'  => 'nickname',
            'index' => 'user.nickname',
            'search'=> 'text',
            'label' => '来自',
            'width' => 120,
            'align' => 'center',
        ],[
            'name'  => 'status',
            'index' => 'message.status',
            'label' => '状态',
            'width' => 100,
            'formatter' => 'status',
            'align' => 'center',
        ],[
            'name'  => 'created_at',
            'index' => 'message.created_at',
            'search'=> 'second',
            'label' => '创建时间',
            'width' => 140,
            'formatter' => 'date',
            'formatoptions' => [
                'srcformat' => 'u',
                'newformat' => 'Y-m-d H:i'
            ],
            'align' => 'center',
        ],[
            'name'  => 'id',
            'index' => 'message.id',
            'label' => '编号',
            'width' => 60,
            'align' => 'center',
        ],[
            'name'      => 'actionlink',
            'label'     => '&nbsp;',
            'sortable'  => false,
            'width'     => 80,
            'formatter' => 'actionlink',
            'align'     => 'center',
            'formatoptions' => [
                'view'  => '查看',
            ],
        ]];

        $searchColumns = [];
        foreach ($columns as $column) {
            if ($column['search']) {
                $searchColumns[] = [$column['search'], $column['index'], $column['label']];
            }
        }

        $search = search_form([
            'status' => 0,
            'limit'  => 25,
        ], $searchColumns);

        $query = $search['query'];

        $model = DB::table('user_message')
        ->LeftJoin('user', 'user.id', '=', 'user_message.created_by')
        ->where('user_message.read_by', Auth::id())
        ->where('user_message.status', $query['status']);

        if ($query['sort'] && $query['order']) {
            $model->orderBy($query['sort'], $query['order']);
        } else {
            $model->orderBy('user_message.id', 'desc');
        }

        foreach ($search['where'] as $where) {
            if ($where['active']) {
                $model->search($where);
            }
        }

        // 搜索
        $rows = $model->select(['user_message.*','user.name'])
        ->paginate($search['limit'])->appends($query);

        if (Input::ajax()) {
            return response()->json($rows);
        }

        $tabs = [
            'name'  => 'status',
            'items' => [
                ['id'=>0, 'name'=>'未读'],
                ['id'=>1, 'name'=>'已读'],
            ]
        ];

        return $this->display([
            'rows'    => $rows,
            'search'  => $search,
            'tabs'    => $tabs,
            'columns' => $columns,
        ]);
    }

    /*
     * 消息状态标记
     */
    public function statusAction()
    {
        $id   = (array)Input::get('id');

        $rows = DB::table('user_message')
        ->whereIn('id', $id)->get();

        if (is_array($rows)) {
            foreach ($rows as $row) {
                $row['status'] = $row['status'] == 1 ? 0 : 1;
                DB::table('user_message')->where('id', $row['id'])->update($row);
            }
        }
        return $this->back('操作成功。');
    }

    /**
     * 新建提醒
     */
    public function createAction()
    {
        $id  = Input::get('id');

        // $row = DB::table('user_message')->find($id);

        $row = User::find($id);
        return $this->render([
            'row' => $row,
        ]);
    }

    /**
     * 显示提醒
     */
    public function showAction()
    {
        $id = Input::get('id');

        $row = DB::table('user_message')->find($id);

        if ($row['status'] == 0) {
            DB::table('user_message')->where('id', $id)->update(['status' => 1, 'read_at' => time()]);
        }
        return $this->render([
            'row' => $row,
        ]);
    }

    /**
     * 提醒设置
     */
    public function countAction()
    {
        $count = DB::table('user_message')
        ->where('read_by', Auth::id())
        ->where('status', 0)
        ->count();
        return response()->json($count);
    }

    /**
     * 删除提醒
     */
    public function deleteAction()
    {
        if (Request::method() == 'POST') {
            $id = (array)Input::get('id');
            DB::table('user_message')->whereIn('id', $id)->delete();
            return $this->json('删除成功。', true);
        }
    }
}
