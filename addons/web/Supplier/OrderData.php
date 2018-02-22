<?php namespace Aike\Web\Supplier;

use Aike\Web\Index\BaseModel;

class OrderData extends BaseModel
{
    protected $table = 'supplier_order_data';
    
    public function order()
    {
        return $this->belongsTo('Aike\Web\Supplier\Order');
    }

    public function stock_data()
    {
        return $this->hasMany('Aike\Web\Supplier\StockData');
    }
}
