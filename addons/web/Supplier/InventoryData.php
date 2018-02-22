<?php namespace Aike\Web\Supplier;

use Aike\Web\Index\BaseModel;

class InventoryData extends BaseModel
{
    protected $table = 'supplier_inventory_data';
    
    public function inventory()
    {
        return $this->belongsTo('Aike\Web\Supplier\Inventory');
    }

    public function product()
    {
        return $this->belongsTo('Aike\Web\Supplier\Product');
    }
}
