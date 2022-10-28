<?php
namespace app\admin\validate;
use think\Validate;

class User extends Validate{
	protected $rule =   [
        'username'  => 'require|unique:user',
        'email' => 'email|unique:user',
        'password' => 'require|length:6,30',
        'headimgurl' => 'require',
        'integral' => 'number',
        'exp' => 'number',
        'recommend' => 'number'
    ];

    protected $message  =   [
        'username.require' => '请先填写用户名',
        'username.unique' => '该用户名已注册',
        'email.email' => '请填写正确邮箱地址',
        'email.unique' => '该邮箱已注册',
        'password.require' => '用户密码必须填写',
        'password.length'  => '用户密码长度必须在6-30个字符之间！',
        'headimgurl.require' => '用户头像不能为空',
        'integral.number' => '用户积分只能是数字',
        'exp.number' => '用户经验只能是数字',
        'recommend.number' => '推荐票只能是数字'
    ];

    protected $scene = [
        'reg'  =>  ['username','email','password','headimgurl','integral','exp'],
        'password' =>['password'],
        'edit' => ['email'=>'require','headimgurl','integral','exp','recommend']
    ];
}