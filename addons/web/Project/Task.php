<?php namespace Aike\Web\Project;

use Aike\Web\Index\BaseModel;

class Task extends BaseModel
{
    protected $table = 'project_task';

    public function project()
    {
        return $this->belongsTo('Aike\Web\Project\Project');
    }

    public function users()
    {
        return $this->belongsToMany('Aike\Web\User\User', 'project_task_user', 'task_id', 'user_id');
    }

    public function syncUsers($gets)
    {
        $users = $gets[$gets['type'].'_users'];
        $users = $users == '' ? [] : explode(',', $users);
        $this->users()->sync($users);
    }
}
