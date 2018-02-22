<?php namespace Aike\Web\Customer;

use Aike\Web\Index\BaseModel;

class Contract extends BaseModel
{
    protected $table = 'customer_contract';

    public function user()
    {
        return $this->belongsTo('Aike\Web\User\User', 'client_id');
    }
}
