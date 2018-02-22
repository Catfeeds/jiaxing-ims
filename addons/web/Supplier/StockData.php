<?php namespace Aike\Web\Supplier;

use Aike\Web\Index\BaseModel;

class StockData extends BaseModel
{
    protected $table = 'supplier_stock_data';
    
    public function stock()
    {
        return $this->belongsTo('Aike\Web\Supplier\Stock');
    }

    public function product()
    {
        return $this->belongsTo('Aike\Web\Supplier\Product');
    }
}
