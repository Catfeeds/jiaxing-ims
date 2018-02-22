<?php namespace Aike\Web\Task;

use Aike\Web\Index\BaseModel;

class Task extends BaseModel
{
    protected $table = 'instruct';

    public static $tabs = [
        ['id' => 'created', 'name' => '我创建的', 'color' => 'default'],
        ['id' => 'receive', 'name' => '我参与的', 'color' => 'info'],
    ];

    public function comments()
    {
        return $this->hasMany('Aike\Web\Task\Comment', 'instruct_id');
    }
}
