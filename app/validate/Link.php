<?php
namespace app\admin\validate;
use think\Validate;
use think\facade\Db;

class Link extends Validate{
	protected $rule =   [
        'title'  => 'require',
        'url' => 'require|url',
    ];

    protected $message  =   [
        'title.require' => '网站名称必须填写！',
        'url.require'  => '网站地址必须填写！',
        'url.url'  => '网站地址格式错误！',
    ];  
}