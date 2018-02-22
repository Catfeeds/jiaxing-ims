<?php namespace Aike\Web\Order;

use Aike\Web\Index\BaseModel;

class OrderData extends BaseModel
{
    protected $table = 'order_data';

    public function product()
    {
        return $this->belongsTo('Aike\Web\Product\Product');
    }
}
