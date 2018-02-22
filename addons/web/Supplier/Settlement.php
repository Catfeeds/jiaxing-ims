<?php namespace Aike\Web\Supplier;

use Aike\Web\Index\BaseModel;

class Settlement extends BaseModel
{
    protected $table = 'supplier_settlement';

    public static $tabs = [
        ['id' => 0, 'name' => '审核中', 'color' => 'info'],
        ['id' => 1, 'name' => '已审核', 'color' => 'success'],
    ];

    public function supplier()
    {
        return $this->belongsTo('Aike\Web\Supplier\Supplier');
    }

    public function datas()
    {
        return $this->hasMany('Aike\Web\Supplier\SettlementData');
    }
}
