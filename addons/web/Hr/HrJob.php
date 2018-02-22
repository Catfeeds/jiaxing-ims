<?php namespace Aike\Web\Hr;

use Aike\Web\Index\BaseModel;

class HrJob extends BaseModel
{
    protected $table = 'hr_job';

    /**
     * 状态
     */
    public static $_status = [
        0 => '待审',
        1 => '已审',
    ];

    public function hr()
    {
        return $this->belongsTo('Aike\Web\Hr\Hr');
    }

    public function department()
    {
        return $this->belongsTo('Aike\Web\User\Department');
    }

    public function role()
    {
        return $this->belongsTo('Aike\Web\User\Role');
    }
}
