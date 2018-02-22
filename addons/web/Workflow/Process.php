<?php namespace Aike\Web\Workflow;

use Aike\Web\Index\BaseModel;

class Process extends BaseModel
{
    protected $table = 'work_process';

    public function user()
    {
        return $this->belongsTo('Aike\Web\User\User');
    }
}
