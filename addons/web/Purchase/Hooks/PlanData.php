<?php namespace Aike\Web\Purchase\Hooks;

class PlanData
{
    public $listens = [
        ['onBeforeForm'],
    ];

    // 表前执行
    public function onBeforeForm($params)
    {
        $views  = $params['views'];
        $fields = $params['fields'];

        // 动态添加视图字段
        $views[] = [
            'field' => 'last_year',
        ];
        $views[] = [
            'field' => 'month_4',
        ];
        $views[] = [
            'field' => 'month_6',
        ];
        $fields['last_year'] = [
            'name'  => '去年同期使用量',
            'field' => 'last_year',
            'setting' => '{"align":"right","width":"100"}',
        ];
        $fields['month_4'] = [
            'name'  => '本年上月使用量',
            'field' => 'month_4',
            'setting' => '{"align":"right","width":"100"}',
        ];
        $fields['month_6'] = [
            'name'  => '目前包材库存量',
            'field' => 'month_6',
            'setting' => '{"align":"right","width":"100"}',
        ];

        $params['views']  = $views;
        $params['fields'] = $fields;
        return $params;
    }
}
