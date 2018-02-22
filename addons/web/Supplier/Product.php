<?php namespace Aike\Web\Supplier;

use Aike\Web\Index\BaseModel;

class Product extends BaseModel
{
    protected $table = 'product';

    public function category()
    {
        return $this->belongsTo('Aike\Web\Supplier\ProductCategory');
    }

    public function suppliers()
    {
        return $this->belongsToMany('Aike\Web\Supplier\Supplier');
    }

    public function boms()
    {
        return $this->belongsToMany('Aike\Web\Supplier\Product', 'product_bom', 'product_id', 'goods_id');
    }

    public function scopeType($query, $type = 1)
    {
        $types['sale']     = 1;
        $types['supplier'] = 2;
        return $query->LeftJoin('product_category', 'product_category.id', '=', 'product.category_id')
        ->where('product_category.type', $types[$type]);
    }

    public function warehouse($query)
    {
        return $this->belongsTo('Aike\Web\Supplier\Warehouse');
    }
}
