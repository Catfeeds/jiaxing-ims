<?php namespace Aike\Web\Stock;

use Aike\Web\Index\BaseModel;

class ProductCategory extends BaseModel
{
    protected $table = 'product_category';

    public function products()
    {
        return $this->hasMany('Aike\Web\Stock\Product', 'category_id');
    }
}
