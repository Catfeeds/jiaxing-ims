<?php namespace Aike\Web\Supplier;

use Aike\Web\Index\BaseModel;

class BudgetData extends BaseModel
{
    protected $table = 'supplier_budget_data';
    
    public function budget()
    {
        return $this->belongsTo('Aike\Web\Supplier\Budget');
    }

    public function product()
    {
        return $this->belongsTo('Aike\Web\Supplier\Product');
    }
}
