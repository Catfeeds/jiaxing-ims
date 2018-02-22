<?php namespace Aike\Web\Customer;

use Aike\Web\Index\BaseModel;

class Circle extends BaseModel
{
    protected $table = 'customer_circle';

    /**
     * 设置字段黑名单
     */
    protected $guarded = ['id'];

    public function parent()
    {
        return $this->belongsTo('Aike\Web\Customer\Circle');
    }
}
