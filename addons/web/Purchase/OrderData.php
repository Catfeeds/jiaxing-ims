<?php namespace Aike\Web\Purchase;

use Aike\Web\Index\BaseModel;

class OrderData extends BaseModel
{
    protected $table = 'purchase_order_data';
    
    public function order()
    {
        return $this->belongsTo('Aike\Web\Purchase\Order');
    }
}
