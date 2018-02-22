<?php namespace Aike\Web\Supplier;

use Aike\Web\Index\BaseModel;

class SettlementData extends BaseModel
{
    protected $table = 'supplier_settlement_data';
    
    public function settlement()
    {
        return $this->belongsTo('Aike\Web\Supplier\Settlement');
    }
}
