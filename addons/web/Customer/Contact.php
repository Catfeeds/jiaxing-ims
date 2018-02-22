<?php namespace Aike\Web\Customer;

use Aike\Web\Index\BaseModel;

class Contact extends BaseModel
{
    protected $table = 'customer_contact';

    protected $guarded = ['id', 'user_id'];

    public function user()
    {
        return $this->belongsTo('Aike\Web\User\User');
    }

    public function customer()
    {
        return $this->belongsTo('Aike\Web\Customer\Customer');
    }
}
