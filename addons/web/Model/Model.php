<?php namespace Aike\Web\Model;

use Aike\Web\Index\BaseModel;

class Model extends BaseModel
{
    protected $table = 'model';

    public function fields()
    {
        return $this->hasMany('Aike\Web\Model\Field');
    }

    public function children()
    {
        return $this->hasMany('Aike\Web\Model\Model', 'parent_id');
    }

    public function regular()
    {
        return [
            'REQUIRED' => ['name' => '不能为空', 'regex' => 'required'],
            'EMAIL'    => ['name' => '只能为邮箱地址', 'regex' => 'required|email'],
            'ZIPCODE'  => ['name' => '只能为邮政编码', 'regex' => 'required|digits:5'],
            'MOBILE'   => ['name' => '只能为手机号码', 'regex' => 'required|digits:11'],
            'IDCARD'   => ['name' => '只能为身份证', 'regex' => 'required|min:15,max:18'],
            'NUMERIC'   => ['name' => '只能为数字', 'regex' => 'required|numeric'],
            'REGEX'    => ['name' => '自定义', 'regex' => 'regex:'],
        ];
    }
}
