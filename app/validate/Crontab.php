<?php
namespace app\admin\validate;
use think\Validate;
use think\facade\Db;

class Crontab extends Validate{
	protected $rule =   [
        'content'  => 'require',
        'interval' => 'require',
        'type' => 'in:1,2,3',
    ];

    protected $message  =   [
        'content.require' => '内容必须填写！',
        'interval.require'  => '间隔时间必须填写！',
        'type.in'  => '类型必须选择！',
    ];  
}