<?php namespace Aike\Web\Customer;

use Aike\Web\Index\BaseModel;

class Account extends BaseModel
{
    protected $table = 'customer_account';

    /**
     * 设置字段黑名单
     */
    protected $guarded = ['id'];

    public function customer()
    {
        return $this->belongsTo('Aike\Web\Customer\Customer');
    }
}
