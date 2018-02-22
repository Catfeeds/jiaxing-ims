<?php namespace Aike\Web\Purchase\Hooks;

use DB;

class Order
{
    public $listens = [
        ['onAfterForm'],
        ['onAfterShow'],
        ['onAfterStore'],
        ['onBeforeDelete']
    ];

    // 表单后执行
    public function onAfterForm($params)
    {
        $_replace = $params['_replace'];
        $_replace['{purchase_order_data}'] = $_replace['{purchase_order_data}'].view('order/editoptions');
        $params['_replace'] = $_replace;

        return $params;
    }

    // 显示后执行
    public function onAfterShow($params)
    {
        $form_groups = $params['form_groups'];
        $row = $params['row'];

        if ($params['view_type'] == 'print') {
            array_unshift($form_groups, [
                'tpl'   => '<div class="row"><div class="col-sm-12 control-text text-center"><h4>四川省川南酿造有限公司原辅料采购订单</h4></div></div>',
                'title' => '',
            ]);
        }

        // 添加开票模版
        if ($row['billing_type']) {
            $dot = $params['view_type'] == 'print' ? '：' : '';

            $html = '
            <div class="row">
            <div class="col-sm-2 control-label">名称'.$dot.'</div>
            <div class="col-sm-4 control-text">四川省川南酿造有限公司</div>
            <div class="col-sm-2 control-label">税号'.$dot.'</div>
            <div class="col-sm-4 control-text">9151 1402 2073 1188 55</div>
            </div>
            <div class="row">
            <div class="col-sm-2 control-label">地址'.$dot.'</div>
            <div class="col-sm-4 control-text">眉山经济开发东区</div>
            <div class="col-sm-2 control-label">电话'.$dot.'</div>
            <div class="col-sm-4 control-text">028-38229888</div>
            </div>
            <div class="row">
            <div class="col-sm-2 control-label">开户行'.$dot.'</div>
            <div class="col-sm-4 control-text">中国农业发展银行眉山市分行营业室</div>
            <div class="col-sm-2 control-label">帐号'.$dot.'</div>
            <div class="col-sm-4 control-text">2035 1149 9001 0000 0199 391</div>
            </div>';

            $form_groups[] = [
                'tpl'   => $html,
                'title' => '开票信息',
            ];
        }

        $params['form_groups'] = $form_groups;
        return $params;
    }

    // 保存后执行
    public function onAfterStore($params)
    {
        $datas = $params['datas'];
        $ids = [];
        foreach ($datas as $rows) {
            foreach ($rows['data'] as $row) {
                $ids[] = $row['plan_data_id'];
            }
        }

        // 更新分单状态
        if ($ids) {
            DB::table('purchase_plan_data')->whereIn('id', $ids)->update(['need_status' => 1]);
        }
        return $params;
    }

    // 删除前执行
    public function onBeforeDelete($params)
    {
        $gets = $params['gets'];
        if ($gets['id']) {
            $plans = DB::table('purchase_order_data')->whereIn('order_id', $gets['id'])->pluck('plan_data_id');
            DB::table('purchase_plan_data')->whereIn('id', $plans)->update(['need_status' => 0]);
        }
        return $params;
    }
}
