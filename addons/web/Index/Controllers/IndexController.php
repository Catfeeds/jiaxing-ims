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
    public $permission = ['dashboard','help','index','home','profile', 'unsupportedBrowser'];

    // 电脑版
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
    
    // 首页登录指南页面
    public function helpAction()
    {
        return $this->render();
    }
}
