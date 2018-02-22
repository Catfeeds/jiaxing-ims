<?php namespace Aike\Web\Purchase;

use Aike\Web\Index\BaseModel;

class PlanData extends BaseModel
{
    protected $table = 'purchase_plan_data';
    
    public function purchase()
    {
        return $this->belongsTo('Aike\Web\Purchase\Plan');
    }
}
