<?php namespace Aike\Web\Task\Controllers;

use DB;
use Auth;
use Input;
use Paginator;
use Request;
use Sms;

use Aike\Web\Index\Notification;
use Aike\Web\User\User;
use Aike\Web\Task\Task;

use Aike\Web\Index\Controllers\DefaultController;

class TaskController extends DefaultController
{
    // 发起的工作任务
    public function indexAction()
    {
        $search = search_form([
            'type' => 'created',
        ], [
            ['text','instruct.title','主题'],
            ['department','user.department_id','相关人部门'],
        ]);
        $query = $search['query'];

        $model = Task::with('comments')
        ->LeftJoin('instruct_comment', 'instruct_comment.instruct_id', '=', 'instruct.id')
        ->LeftJoin('user', 'instruct_comment.add_user_id', '=', 'user.id')
        ->orderBy('instruct.id', 'desc')
        ->select(['instruct.*']);

        if ($query['type'] == 'created') {
            $model->where('instruct.add_user_id', auth()->id());
        } else {
            $model->whereRaw('FIND_IN_SET(?, instruct.user_ids)', [auth()->id()]);
        }
        
        foreach ($search['where'] as $where) {
            if ($where['active']) {
                $model->search($where);
            }
        }

        $rows = $model->paginate()->appends($query);

        // 得到任务回报结果
        foreach ($rows as $key => $row) {
            $count['all'] = count(explode(',', $row->user_ids));

            foreach ($row->comments as $comment) {
                $count[$comment->status] =+ 1;
            }

            $row->count = $count;
            $rows->put($key, $row);
        }

        $tabs = [
            'name'  => 'type',
            'items' => Task::$tabs
        ];

        return $this->display([
            'rows'   => $rows,
            'query'  => $query,
            'search' => $search,
            'tabs'   => $tabs,
        ]);
    }

    // 添加工作任务
    public function addAction()
    {
        $id = (int)Input::get('id');
        
        $res = DB::table('instruct')->where('id', $id)->first();

        // 写入部分
        if ($post = $this->post()) {
            $post['content']     = $_POST['content'];
            $post['attachment']  = join(',', (array)$post['attachment']);
            $post['add_user_id'] = auth()->id();
            $post['add_time']    = strtotime($post['add_time']);
            $post['end_time']    = strtotime($post['end_time']);


            if (empty($post['user_ids'])) {
                return $this->error('任务执行人必须选择。');
            }

            if (empty($post['title'])) {
                return $this->error('任务主题必须填写。');
            }

            if (empty($post['content'])) {
                return $this->error('任务正文必须填写。');
            }

            if ($post['id'] > 0) {
                DB::table('instruct')->where('id', $post['id'])->update($post);
            } else {
                DB::table('instruct')->insert($post);
            }

            // 设置附件为已经使用
            attachment_store('attachment', $_POST['attachment']);

            // 短信通知
            if ($post['sms'] == 'true') {
                $user_array = DB::table('user')->whereIn('id', explode(',', $post['user_ids']))->get(['id','mobile']);

                $user = array();
                foreach ($user_array as $v) {
                    if ($v['mobile']) {
                        $user[$v['id']] = $v['mobile'];
                    }
                }
                Notification::sms($user, Auth::user()->nickname."发起了[{$post['title']}]的工作任务,请尽快登录查阅。");
            }
            return $this->success('index', '恭喜您，新工作任务提交成功。');
        }

        $attachList = attachment_edit('attachment', $res['attachment']);

        return $this->display(array(
            'attachList' => $attachList,
            'res'        => $res,
        ));
    }

    public function commentAction()
    {
        $instructReply = DB::table('instruct_comment');

        if ($post = $this->post()) {
            $post['content'] = $_POST['content'];

            if (empty($post['content'])) {
                return $this->error('回复内容必须填写。');
            }
            
            $post['add_time']    = time();
            $post['add_user_id'] = auth()->id();

            $post['attachment'] = join(',', (array)$post['attachment']);

            $instruct = DB::table('instruct')->where('id', $post['instruct_id'])->first();

            if ($post['id'] > 0) {
                $instructReply->where('id', $post['id'])->update($post);
            } else {
                $instructReply->insert($post);
            }

            // 修改附件状态
            attachment_store('attachment', $_POST['attachment']);

            // 短信通知
            if ($post['sms'] == 'true') {
                $user = DB::table('user')->where('id', $instruct['add_user_id'])->first();
                
                Notification::sms([$user['mobile']], Auth::user()->nickname."回复了[{$instruct['title']}]工作任务,请尽快登录查阅。");
            }
            return $this->success('comment', ['id' => $post['instruct_id']], '恭喜您，任务提交成功。');
        }

        $id = (int)Input::get('id', 0);

        $instruct = DB::table('instruct as a')
        ->leftJoin('user AS b', 'b.id', '=', 'a.add_user_id')
        ->where('a.id', $id)
        ->first(['a.*','b.nickname']);
        
        $reply = DB::table('instruct_comment')->whereRaw('add_user_id=? and instruct_id=?', [Auth::id(), $id])->first();

        // 信件附件
        $attachList = attachment_edit('attachment', $instruct['attachment']);
        $attachList['reply'] = attachment_get('attachment', $reply['attachment']);

        return $this->display(array(
            'attachList'=> $attachList,
            'scope'     => $scope,
            'instruct'  => $instruct,
            'reply'     => $reply,
        ));
    }

    // 指令查看
    public function viewAction()
    {
        $id = (int)Input::get('id', 0);
        
        $instruct = DB::table('instruct AS a')
        ->leftJoin('user AS b', 'b.id', '=', 'a.add_user_id')
        ->where('a.id', $id)
        ->select(['a.*','b.nickname'])
        ->first();

        if (empty($instruct)) {
            return $this->error('任务不存在。');
        }

        $replys = DB::table('instruct_comment AS a')
        ->leftJoin('user AS b', 'b.id', '=', 'a.add_user_id')
        ->where('a.instruct_id', $id)
        ->select(['a.*','b.nickname'])
        ->get();
        $replys = array_by($replys, 'add_user_id');
        
        foreach ($replys as $k => $v) {
            $replys[$k]['attachment'] = attachment_get('attachment', $v['attachment']);
        }

        // 获取对象列表
        $userIds = array_filter(explode(',', $instruct['user_ids']));
        $users = DB::table('user')->whereIn('id', $userIds)->get(['id', 'nickname']);
        
        foreach ($users as $user) {
            $userId = $user['id'];
            $scope['user']['id'][$userId] = $user['id'];
            $scope['user']['name'][$userId] = $user['nickname'];
        }
        $scope['user']['id_text'] = join(',', $scope['user']['id']);
        $scope['user']['name_text'] = join(',', $scope['user']['name']);

        // 信件附件
        $attachList = attachment_view('attachment', $instruct['attachment']);

        return $this->display(array(
            'attachList' => $attachList,
            'draft'      => $draft,
            'scope'      => $scope,
            'instruct'   => $instruct,
            'replys'     => $replys,
        ));
    }

    public function auditAction()
    {
        if ($post = $this->post()) {
            $post['audit_time'] = time();
            DB::table('instruct_comment')->where('id', $post['id'])->update($post);
            exit('1');
        }

        $id = (int)Input::get('id');
        $reply = DB::table('instruct_comment')->where('id', $id)->first();

        $this->layout = 'layouts.empty';
        return $this->display(array(
            'reply' => $reply,
        ));
    }

    public function analysisAction()
    {
        $page = Input::get('page', 1);
        $search = search_form([], [
            ['text','user.nickname','相关人姓名'],
            ['department','user.department_id','相关人部门'],
        ]);
        $query = $search['query'];

        $model = DB::table('instruct_comment as ic')
        ->leftJoin('user as u', 'u.id', '=', 'ic.add_user_id')
        ->where('ic.status', 1)
        ->selectRaw('ROUND(AVG(ic.vote),2) avg,COUNT(ic.id) count, u.nickname, u.id');

        foreach ($search['where'] as $where) {
            if ($where['active']) {
                $model->search($where);
            }
        }

        $count = $model->count('u.id');

        $rows = $model->forPage($page, $search['limit'])
        ->groupBy('u.id')
        ->orderBy('count', 'DESC')
        ->get();

        $rows = Paginator::make($rows, $count, $search['limit'])->appends($query);
        return $this->display(array(
            'rows'   => $rows,
            'query'  => $query,
            'search' => $search,
        ));
    }

    public function deleteAction()
    {
        $id = (int)Input::get('id', 0);

        $res = DB::table('instruct')->where('id', $id)->first();
        $resReply = DB::table('instruct_comment')->where('instruct_id', $id)->get();

        if (empty($res)) {
            return $this->error('数据不存在。');
        }

        // 删除任务表附件
        attachment_delete('attachment', $res['attachment']);

        // 删除回复表附件
        if (is_array($resReply)) {
            foreach ($resReply as $v) {
                attachment_delete('attachment', $v['attachment']);
            }
        }

        DB::table('instruct')->where('id', $id)->delete();
        DB::table('instruct_comment')->where('instruct_id', $id)->delete();

        return $this->success('index', '恭喜您，任务删除成功。');
    }
}
