<?php namespace Aike\Web\Flow;

use Aike\Web\Index\BaseModel;

class Model extends BaseModel
{
    protected $table = 'flow';

    public function fields()
    {
        return $this->hasMany('Aike\Web\Flow\Field');
    }

    public function children()
    {
        return $this->hasMany('Aike\Web\Flow\Model', 'parent_id');
    }
    
    public function regulars()
    {
        return [
            'required'               => '必填',
            'numeric'                => '数字',
            'integer'                => '整数',
            'alpha'                  => '字母',
            'date'                   => '日期',
            'alpha_num'              => '数字+字母',
            'email'                  => '邮箱',
            'active_url'             => '链接',
            'regex:/^[0-9]{5,20}$/'  => 'QQ',
            'regex:/^(1)[0-9]{10}$/' => '手机',
            'regex:/^[0-9-]{6,13}$/' => '电话',
            'regex:/^[0-9]{6}$/'     => '邮编',
        ];
        /*
        return [
            'REQUIRED' => ['name' => '不能为空', 'regex' => 'required'],
            'EMAIL'    => ['name' => '只能为邮箱地址', 'regex' => 'required|email'],
            'ZIPCODE'  => ['name' => '只能为邮政编码', 'regex' => 'required|digits:5'],
            'MOBILE'   => ['name' => '只能为手机号码', 'regex' => 'required|digits:11'],
            'IDCARD'   => ['name' => '只能为身份证', 'regex' => 'required|min:15,max:18'],
            'NUMERIC'   => ['name' => '只能为数字', 'regex' => 'required|numeric'],
            'REGEX'    => ['name' => '自定义', 'regex' => 'regex:'],
        ];
        */
    }
}
