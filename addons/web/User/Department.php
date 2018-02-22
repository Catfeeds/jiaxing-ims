<?php namespace Aike\Web\User;

use Aike\Web\Index\BaseModel;

class Department extends BaseModel
{
    protected $table = 'department';

    public function users()
    {
        return $this->hasMany('Aike\Web\User\User');
    }

    /**
     * 取得部门列表
     */
    public static function getAll($departmentId = 0)
    {
        static $data = null;

        if ($data === null) {
            $data = Department::orderBy('lft', 'asc')->get(['id', 'parent_id', 'title'])->toNested('title');
            //$data = DB::table('department')->get(['id','title']);
            //$data = array_by($data);
        }
        return $departmentId > 0 ? $data[$departmentId] : $data;
    }
}
