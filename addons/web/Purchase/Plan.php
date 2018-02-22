<?php namespace Aike\Web\Purchase;

use Aike\Web\Index\BaseModel;

class Plan extends BaseModel
{
    protected $table = 'purchase_plan';

    public static $tabs = [
        ['id' => 0, 'name' => '进行中', 'color' => 'info'],
        ['id' => 1, 'name' => '已结束', 'color' => 'success'],
    ];

    public function datas()
    {
        return $this->hasMany('Aike\Web\Purchase\PlanData');
    }
}
