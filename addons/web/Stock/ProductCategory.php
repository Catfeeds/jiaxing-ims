<?php namespace Aike\Web\Stock;

use Aike\Web\Supplier\ProductCategory as BaseCategory;

class ProductCategory extends BaseCategory
{
    protected $table = 'product_category';

    public function products()
    {
        return $this->hasMany('Aike\Web\Stock\Product', 'category_id');
    }
}
