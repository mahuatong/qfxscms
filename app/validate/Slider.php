<?php
namespace app\admin\validate;
use think\Validate;
use think\facade\Db;

class Slider extends Validate{
	protected $rule =   [
        'title'  => 'require',
        'type' => 'require',
        'link' => 'require',
    ];

    protected $message  =   [
        'title.require' => '名称必须填写！',
        'type.require' => '请选择类型！',
        'link.require'  => '连接必须填写！',
    ];  
}