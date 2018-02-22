<?php namespace Aike\Web\Supplier;

use Aike\Web\Index\BaseModel;

class Budget extends BaseModel
{
    protected $table = 'supplier_budget';
    
    public function datas()
    {
        return $this->hasMany('Aike\Web\Supplier\BudgetData');
    }
}
