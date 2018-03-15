<?php namespace Aike\Web\Stock;

use DB;
use Aike\Web\Index\BaseModel;

class StockLine extends BaseModel
{
    protected $table = 'stock_line';

    public function warehouse()
    {
        return $this->belongsTo('Aike\Web\Stock\Warehouse');
    }

    public function product()
    {
        return $this->belongsTo('Aike\Web\Stock\Product');
    }
}
