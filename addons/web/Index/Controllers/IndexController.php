<?php namespace Aike\Web\Index\Controllers;

use Illuminate\Http\Request;
use Input;
use DB;
use Auth;

class IndexController extends DefaultController
{
    /**
      * 设置可直接访问的方法
      */
    public $permission = ['dashboard','help','index','unsupportedBrowser','welcome'];

    public function indexAction(Request $request)
    {
        $url = Input::get('i', 'index/index/dashboard');
        $url = url($url);
        return $this->render([
            'url' => $url,
        ]);
    }
    
    public function dashboardAction(Request $request)
    {
        $widgets = DB::table('widget')
        ->where('status', 1)
        ->permission('receive_id')
        ->orderBy('sort', 'asc')
        ->get();

        $panel = Input::get('panel');

        $panels = [
            'communicate' => [
                (int)$communicate,
                url('message/message/index'),
                '收银开单',
                'fa-clock-o',
                'bg-info',
            ],
            'project_task' => [
                (int)$task_count,
                url('project/project/index'),
                '会员办卡',
                'fa-cubes',
                'bg-success',
            ],
            'article' => [
                (int)$article_count,
                url('article/article/index', ['read' => 'unread']),
                '会员报表',
                'fa-bullhorn',
                'bg-primary',
            ],
            'workflow' => [
                (int)$workflow_count,
                url('workflow/workflow/index', ['option' => 'todo']),
                '今日提醒',
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
    
    public function welcomeAction()
    {
        return $this->render();
    }

    // 首页登录指南页面
    public function helpAction()
    {
        return $this->render();
    }
}
