<?php
namespace app\admin\validate;
use think\Validate;
class Member extends Validate{
	protected $rule =   [
        'username'  => 'require|length:5,30',
        'password' => 'require|length:6,30',
        'repassword'=>'require|confirm:password'
    ];

    protected $message  =   [
        'username.require' => '用户名必须填写！',
        'username.length'     => '用户名长度必须在5-30个字符之间！',
        'password.require'  => '密码必须填写！',
        'password.length'  => '密码长度必须在6-30个字符之间！',
        'repassword.require' => '重复密码必须填写！',
        'repassword.confirm'  => '重复密码与确认密码不一致！'
    ];
}