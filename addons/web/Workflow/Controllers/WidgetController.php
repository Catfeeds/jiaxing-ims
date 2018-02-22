<?php namespace Aike\Web\Workflow\Controllers;

use DB;
use Auth;
use Input;
use Request;

use Aike\Web\Index\Controllers\DefaultController;

class WidgetController extends DefaultController
{
    public $permission = ['index'];
    
    public function indexAction()
    {
        if (Request::isJson()) {
            $rows = DB::table('work_process as p')
            ->LeftJoin('work_process_data as d', 'p.id', '=', 'd.process_id')
            ->LeftJoin('work_step as s', 's.id', '=', 'd.step_id')
            ->where('d.user_id', Auth::id())
            ->where('d.flag', 1)
            ->where('p.state', 1)
            ->where('p.end_time', 0)
            ->orderBy('p.id', 'desc')
            ->selectRaw('p.*,s.number step_number,s.title step_title,d.add_time turn_time')
            ->get();

            $json['total'] = sizeof($rows);
            $json['data'] = $rows;
            return response()->json($json);
        }
        return $this->render();
    }
}
