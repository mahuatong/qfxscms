<?php
namespace app\admin\validate;
use think\Validate;
use think\facade\Db;

class Template extends Validate{
	protected $rule =   [
        'mold'  => 'require',
        'title' => 'require',
        'name' => 'require|alphaDash',
        'author' => 'require',
    ];

    protected $message  =   [
        'mold.require' => '模版类型必须选择！',
        'title.require' => '模版名称必须填写！',
        'name.require' => '模版标识必须填写！',
        'name.alphaDash' => '模版标识只能是字母和数字下划线_及破折号-！',
        'author.require' => '模版作者必须填写！'
    ];

    protected $scene = [
        'default'  =>  ['mold'],
        'add' => ['title','name','author']
    ]; 

}