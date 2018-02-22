<?php namespace Aike\Web\Supplier;

use Aike\Web\Index\BaseModel;

class PlanData extends BaseModel
{
    protected $table = 'supplier_plan_data';
    
    public function plan()
    {
        return $this->belongsTo('Aike\Web\Supplier\Plan');
    }
}
