<?php namespace Aike\Web\Purchase\Hooks;

use DB;

class OrderData
{
    public $listens = [
        ['onAfterForm'],
    ];

    public function onAfterForm($params)
    {
        $gets = $params['gets'];

        // 计划明细
        $res = DB::table('purchase_plan_data')
        ->where('purchase_plan_data.plan_id', $gets['plan_id'])
        ->where('need_status', 0)
        ->get(['*', 'id as plan_data_id']);

        $rows = [];
        foreach ($res as $row) {
            $row['id']       = 0;
            $row['quantity'] = $row['need_quantity'];
            $rows[]          = $row;
        }

        $params['multiselect'] = true;
        $params['rows'] = $rows;

        return $params;
    }
}
