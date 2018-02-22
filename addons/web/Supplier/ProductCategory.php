<?php namespace Aike\Web\Supplier;

use Aike\Web\Index\BaseModel;

class ProductCategory extends BaseModel
{
    protected $table = 'product_category';

    public function scopeType($query, $type = 1)
    {
        $types['sale']     = 1;
        $types['supplier'] = 2;
        return $query->where('type', $types[$type]);
    }
}
