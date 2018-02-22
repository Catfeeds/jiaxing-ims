<?php namespace Aike\Web\Supplier\Hooks;

class PriceData
{
    public $listens = [
        ['onBeforeShow'],
    ];

    // 显示前
    public function onBeforeShow($params)
    {
        $q = $params['q'];
        $q->leftJoin('product', 'product.id', '=', 'supplier_price_data.product_id')
        ->selectRaw("supplier_price_data.*,IF(product.spec='', product.name, concat(product.name,' - ', product.spec)) as product_name, product.stock_number");
        $params['q'] = $q;

        $views  = $params['views'];
        $fields = $params['fields'];

        $view = [
            'name'  => '存货编码',
            'field' => 'stock_number',
        ];

        $views = array_merge([$view], $views);

        $fields['stock_number'] = [
            'field'     => 'stock_number',
            'form_type' => 'text',
            'setting'   => '{"align":"center"}',
        ];

        $params['views']  = $views;
        $params['fields'] = $fields;
        
        return $params;
    }
}
