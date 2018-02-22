<?php namespace Aike\Web\Supplier;

use Aike\Web\Index\BaseModel;

class Quality extends BaseModel
{
    protected $table = 'supplier_quality';
    
    public function supplier()
    {
        return $this->belongsTo('Aike\Web\Supplier\Supplier');
    }

    public function product()
    {
        return $this->belongsTo('Aike\Web\Supplier\Product');
    }

    // 流程表单保存时过滤
    public function stepFilter($data)
    {
        // 新建
        if ($data['product_id'] && $data['id'] == '') {
            $product = Product::find($data['product_id']);
            $data['supplier_id'] = $product->supplier_id;
        }
        return $data;
    }
}
