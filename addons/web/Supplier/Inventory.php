<?php namespace Aike\Web\Supplier;

use Aike\Web\Index\BaseModel;

class Inventory extends BaseModel
{
    protected $table = 'supplier_inventory';
    
    public function supplier()
    {
        return $this->belongsTo('Aike\Web\Supplier\Supplier');
    }

    public function datas()
    {
        return $this->hasMany('Aike\Web\Supplier\InventoryData');
    }
}
