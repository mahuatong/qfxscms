<?php
namespace app\admin\validate;
use think\Validate;
use think\facade\Db;

class Ad extends Validate{
	protected $rule =   [
        'title'  => 'require',
        'value' => 'require'
    ];

    protected $message  =   [
        'title.require' => '广告名称必须填写！',
        'value.require'  => '广告代码必须填写！'
    ];  
}