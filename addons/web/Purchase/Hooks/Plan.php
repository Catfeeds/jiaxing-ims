<?php namespace Aike\Web\Purchase\Hooks;

use App\Hook\Event;

class Plan
{
    public $listens = [
        ['onListButton'],
        ['onAfterShow'],
        ['onAfterForm']
    ];

    // 渲染按钮
    public function onListButton($params)
    {
        $html = $params['html'];
        $row  = $params['row'];

        if (authorise('order.create')) {
            $order = 'flow.quickForm("purchase_order","分单", "'.url('purchase/order/create', ['quick' => 'plan_id','plan_id'=>$row['id']]).'", "lg")';
            $html .= "<a class='option' href='javascript:;' onclick='$order'>分单</a> ";
        }

        $params['html'] = $html;

        return $params;
    }

    public function onAfterShow($params)
    {
        $_replace = $params['_replace'];
        $buttons  = $params['buttons'];
        $row      = $params['row'];

        if (authorise('order.create')) {
            $order = 'flow.quickForm("purchase_order","分单", "'.url('purchase/order/create', ['quick' => 'plan_id','plan_id'=>$row['id']]).'", "lg")';
            $buttons .= "<a class='btn btn-sm btn-info' href='javascript:;' onclick='$order'>分单</a> ";
        }

        $params['buttons'] = $buttons;
        return $params;
    }

    // 表单后执行
    public function onAfterForm($params)
    {
        $_replace = $params['_replace'];
        $_replace['{purchase_plan_data}'] = $_replace['{purchase_plan_data}'].view('plan/editoptions');
        $params['_replace'] = $_replace;
        return $params;
    }
}
