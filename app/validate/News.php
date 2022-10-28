<?php
namespace app\admin\validate;
use think\Validate;
use think\facade\Db;

class News extends Validate{
	protected $rule =   [
        'id' => 'require',
        'title'  => 'require',
        'category' => 'require',
        'content' => 'require',
    ];

    protected $message  =   [
        'id.require' => 'ID必须填写！',
        'title.require' => '标题必须填写！',
        'category.require'  => '必须选择栏目！',
        'content.require'  => '内容必须填写！',
    ];

    protected $scene = [
        'edit'  =>  ['title','category','content','id'],
        'add' => ['title','category','content'],
        'position' => ['id']
    ];
}