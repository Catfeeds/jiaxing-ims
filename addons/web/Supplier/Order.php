<?php namespace Aike\Web\Supplier;

use Aike\Web\Index\BaseModel;

class Order extends BaseModel
{
    protected $table = 'supplier_order';

    public static $tabs = [
        ['id' => '', 'name' => '全部', 'color' => 'info'],
        ['id' => 0, 'name' => '待审', 'color' => 'success'],
        ['id' => 1, 'name' => '已审', 'color' => 'default'],
    ];

    public function supplier()
    {
        return $this->belongsTo('Aike\Web\Supplier\Supplier');
    }

    public function datas()
    {
        return $this->hasMany('Aike\Web\Supplier\OrderData');
    }

    public function stock()
    {
        return $this->hasMany('Aike\Web\Supplier\Stock');
    }

    public function plan()
    {
        return $this->belongsTo('Aike\Web\Supplier\Plan');
    }
}
