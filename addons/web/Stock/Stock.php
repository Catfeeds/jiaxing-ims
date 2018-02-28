<?php namespace Aike\Web\Stock;

use Aike\Web\Index\BaseModel;

class Stock extends BaseModel
{
    protected $table = 'stock';

    public function lines()
    {
        return $this->hasMany('Aike\Web\Stock\StockLine');
    }
}
