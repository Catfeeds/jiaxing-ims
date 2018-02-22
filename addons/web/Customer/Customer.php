<?php namespace Aike\Web\Customer;

use Aike\Web\Index\BaseModel;

class Customer extends BaseModel
{
    protected $table = 'client';

    public function user()
    {
        return $this->belongsTo(\Aike\Web\User\User::class);
    }

    public function circle()
    {
        return $this->belongsTo(\Aike\Web\Customer\Circle::class);
    }
    
    public function contacts()
    {
        return $this->hasMany(\Aike\Web\Customer\Contact::class);
    }
}
