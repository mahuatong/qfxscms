<?php
namespace app\admin\validate;
use think\Validate;
use think\facade\Db;

class Route extends Validate{
	protected $rule =   [
        'title'  => 'require',
        'name' => 'require',
        'value'=>'require|checkValue:1'
    ];

    protected $message  =   [
        'title.require' => '路由名称必须填写！',
        'name.require'  => '路由方式必须填写！',
        'name.checkName'  => '路由已存在！',
        'value.require' => '路由规则必须填写！',
        'value.checkValue' => '路由规则格式错误！',
    ];  

    protected function checkValue($value,$rule,$data=[]){
        return is_null(json_decode($value))?false:true;
    }
}