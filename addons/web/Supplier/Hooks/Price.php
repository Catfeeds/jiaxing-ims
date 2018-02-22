<?php namespace Aike\Web\Supplier\Hooks;

class Price
{
    public $listens = [
        ['onBeforeStore'],
        ['onAfterForm']
    ];

    // 保存前执行
    public function onBeforeStore($params)
    {
        $master = $params['master'];
        $datas  = $params['datas'];
        
        // 保存前格式化时间
        $master['date'] = strtotime($master['date']);

        foreach ($datas as &$rows) {
            foreach ($rows['data'] as &$row) {
                $row['date'] = $master['date'];
            }
        }

        $params['master'] = $master;
        $params['datas']  = $datas;
        return $params;
    }

    // 表单后执行
    public function onAfterForm($params)
    {
        $_replace = $params['_replace'];

        $_replace['{supplier_price_data}'] = $_replace['{supplier_price_data}'].view('price/editoptions');
        $params['_replace'] = $_replace;
        return $params;
    }
}
