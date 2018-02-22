<?php namespace Aike\Mobile\Index\Controllers;

use Illuminate\Http\Request;
use Input;
use DB;

use Aike\Web\Index\Controllers\DefaultController;

class IndexController extends DefaultController
{
    /**
      * 设置可直接访问的方法
      */
    public $permission = ['dashboard','help','index','home','profile', 'unsupportedBrowser'];

    // 桌面菜单
    public function indexAction(Request $request)
    {
        $url = Input::get('i', 'index/index/dashboard');
        $url = url($url);
        return $this->render([
            'url' => $url,
        ]);
    }

    // 电脑版桌面
    public function dashboardAction(Request $request)
    {
        $widgets = DB::table('widget')
        ->where('status', 1)
        ->permission('receive_id')
        ->orderBy('sort', 'asc')
        ->get();

        $panel = Input::get('panel');

        // 沟通管理
        $communicate = DB::table('communicate')->whereRaw("reply_text = '' and to_user_id=?", [Auth::id()])->count('id');

        $task_count = DB::table('project_task')
        ->where('user_id', Auth::id())
        ->where('progress', '<', 1)
        ->count('id');

        $workflow_count = DB::table('work_process')
        ->LeftJoin('work_process_data', 'work_process.id', '=', 'work_process_data.process_id')
        ->where('work_process_data.user_id', Auth::id())
        ->where('work_process_data.flag', 1)
        ->where('work_process.state', 1)
        ->where('work_process.end_time', 0)
        ->count('work_process.id');

        $article_count = DB::table('article')
        ->permission('receive_id')
        ->whereNotExists(function ($q) {
            $q->selectRaw('1')
            ->from('article_reader')
            ->whereRaw('article_reader.article_id = article.id')
            ->where('article_reader.created_by', auth()->id());
        })->count('id');

        $panels = [
            'communicate' => [
                $communicate,
                url('message/message/index'),
                '等待回复沟通信件',
                'fa-clock-o',
                'bg-info',
            ],
            'project_task' => [
                $task_count,
                url('project/project/index'),
                '等待落实的项目事宜',
                'fa-cubes',
                'bg-success',
            ],
            'article' => [
                $article_count,
                url('article/article/index', ['read' => 'unread']),
                '等待阅读的公告',
                'fa-bullhorn',
                'bg-primary',
            ],
            'workflow' => [
                $workflow_count,
                url('workflow/workflow/index', ['option' => 'todo']),
                '等待处理的流程',
                'fa-code-fork',
                'bg-dark',
            ],
        ];

        return $this->display([
            'widgets' => $widgets,
            'panel'   => $panel,
            'panels'  => $panels,
        ]);
    }
    
    // 首页登录指南页面
    public function helpAction()
    {
        return $this->render();
    }
}
