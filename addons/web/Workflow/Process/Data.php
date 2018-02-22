<?php namespace Aike\Web\Workflow\Workflow\Process;

use BaseModel;

class Data extends BaseModel
{
    protected $table = 'work_process_data';

    public function user()
    {
        return $this->belongsTo('Aike\Web\User\User');
    }
}
