<?php namespace Aike\Web\Workflow\Controllers;

use Input;
use Request;

use Aike\Web\Workflow\Process;
use Aike\Web\Workflow\ProcessData;

use Aike\Web\Index\Controllers\DefaultController;

class MonitorController extends DefaultController
{
    public $permission = [];

    public function indexAction()
    {
    }

    // 监控流程汇总
    public function summaryAction()
    {
        $search = search_form([], [
            ['text','user.nickname','主办人'],
            ['department','user.department_id','主办人部门'],
        ]);

        $query = $search['query'];

        $model = Process::LeftJoin('work_process_data', 'work_process.id', '=', 'work_process_data.process_id')
        ->LeftJoin('work', 'work.id', '=', 'work_process.work_id')
        ->LeftJoin('user', 'user.id', '=', 'work_process_data.user_id')
        ->where('work_process.end_time', 0)
        ->where('work_process.state', 1)
        ->where('work_process_data.flag', 1)
        ->where('work.state', 1)
        ->where('user.status', 1);

        foreach ($search['where'] as $where) {
            if ($where['active']) {
                $model->search($where);
            }
        }

        $rows = $model->get(['user.nickname as name', 'work_process_data.*']);

        $items = [];
        foreach ($rows as $row) {
            $user_id = $row['user_id'];

            $time = time() - $row['add_time'];

            // 大于三十天
            if ($time > 2592000) {
                $items[$user_id]['c'] ++;

            // 大于三天
            } elseif ($time > 259200) {
                $items[$user_id]['b'] ++;

            // 大于一天
            } elseif ($time > 86400) {
                $items[$user_id]['a'] ++;
            }

            $items[$user_id]['total'] ++;

            $items[$user_id]['name']    = $row->user->nickname ? $row->user->nickname : $user_id;
            $items[$user_id]['user_id'] = $user_id;
        }

        $rows = [];
        foreach ($items as $user_id => $item) {
            $rows[] = $item;
        }

        return $this->display([
            'rows'   => $rows,
            'search' => $search,
        ]);
    }
}
