<?php namespace Aike\Web\Supplier;

use Aike\Web\Index\BaseModel;

class Supplier extends BaseModel
{
    protected $table = 'supplier';

    public function user()
    {
        return $this->belongsTo('Aike\Web\User\User');
    }

    public function contact()
    {
        return $this->belongsTo('Aike\Web\Supplier\Contact');
    }

    public function products()
    {
        return $this->belongsToMany('Aike\Web\Supplier\Product');
    }

    public function plans()
    {
        return $this->hasMany('Aike\Web\Supplier\Plan');
    }

    public function orders()
    {
        return $this->hasMany('Aike\Web\Supplier\Order');
    }

    public function stocks()
    {
        return $this->hasMany('Aike\Web\Supplier\Stock');
    }
}
