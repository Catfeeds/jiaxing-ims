<?php namespace Aike\Web\Supplier;

use Aike\Web\Index\BaseModel;

class Plan extends BaseModel
{
    protected $table = 'supplier_plan';

    public static $tabs = [
        ['id' => 0, 'name' => '进行中', 'color' => 'info'],
        ['id' => 1, 'name' => '已结束', 'color' => 'success'],
    ];

    public function supplier()
    {
        return $this->belongsTo('Aike\Web\Supplier\Supplier');
    }

    public function datas()
    {
        return $this->hasMany('Aike\Web\Supplier\PlanData');
    }

    public function order()
    {
        return $this->hasMany('Aike\Web\Supplier\Order');
    }
}
