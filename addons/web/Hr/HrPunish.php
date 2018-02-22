<?php namespace Aike\Web\Hr;

use Aike\Web\Index\BaseModel;

class HrPunish extends BaseModel
{
    protected $table = 'hr_punish';

    /**
     * 状态
     */
    public static $_status = [
        0 => '待审',
        1 => '已审',
    ];
}
