<?php namespace Aike\Web\Stock;

use DB;
use Aike\Web\Index\BaseModel;

class Product extends BaseModel
{
    protected $table = 'product';

    public function stockWarehouses()
    {
        return $this->hasMany('Aike\Web\Stock\StockWarehouse');
    }
}
