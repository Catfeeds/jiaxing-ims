<?php namespace Aike\Web\Workflow;

use Aike\Web\Index\BaseModel;

class ProcessData extends BaseModel
{
    protected $table = 'work_process_data';

    public function user()
    {
        return $this->belongsTo('Aike\Web\User\User');
    }
}
