<?php namespace Aike\Web\Project;

use Aike\Web\Index\BaseModel;

class Project extends BaseModel
{
    protected $table = 'project';

    public static $tabs = [
        ['id' => 0, 'name' => '进行中', 'color' => 'info'],
        ['id' => 1, 'name' => '已结束', 'color' => 'success'],
    ];

    public function tasks()
    {
        return $this->hasMany('Aike\Web\Project\Task');
    }
}
