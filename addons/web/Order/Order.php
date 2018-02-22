<?php namespace Aike\Web\Order;

use Aike\Web\Index\BaseModel;

class Order extends BaseModel
{
    protected $table = 'order';
    
    public function promotions()
    {
        return $this->hasMany('Aike\Web\Promotion\Promotion', 'customer_id', 'client_id');
    }

    public function approachs()
    {
        return $this->hasMany('Aike\Web\Approach\Approach', 'customer_id', 'client_id');
    }

    public function datas()
    {
        return $this->hasMany('Aike\Web\Order\OrderData');
    }
}
