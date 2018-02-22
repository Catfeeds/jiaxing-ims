<?php namespace Aike\Web\Customer;

use Aike\Web\Index\BaseModel;

class Cultivate extends BaseModel
{
    protected $table = 'customer_cultivate';

    /**
     * 设置字段黑名单
     */
    protected $guarded = ['id'];

    public function customer()
    {
        return $this->belongsTo('Aike\Web\Customer\Customer');
    }

    public function contact()
    {
        return $this->belongsTo('Aike\Web\Customer\Contact');
    }
}
