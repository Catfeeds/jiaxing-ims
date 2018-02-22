<?php namespace Aike\Web\Supplier;

use Aike\Web\Index\BaseModel;

class Contact extends BaseModel
{
    protected $table = 'supplier_contact';
    
    protected $guarded = ['id', 'user_id'];

    public function user()
    {
        return $this->belongsTo('Aike\Web\User\User');
    }

    public function supplier()
    {
        return $this->belongsTo('Aike\Web\Supplier\Supplier');
    }
}
