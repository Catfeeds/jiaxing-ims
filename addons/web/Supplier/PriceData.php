<?php namespace Aike\Web\Supplier;

use Aike\Web\Index\BaseModel;

class PriceData extends BaseModel
{
    protected $table = 'supplier_price_data';
    
    public function price()
    {
        return $this->belongsTo('Aike\Web\Supplier\Price');
    }

    public function product()
    {
        return $this->belongsTo('Aike\Web\Supplier\Product');
    }
}
