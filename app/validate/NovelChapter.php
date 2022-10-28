<?php
namespace app\admin\validate;
use think\Validate;
use think\facade\Db;

class NovelChapter extends Validate{
	protected $rule =   [
        'title'  => 'require',
        'content' => 'require',
    ];

    protected $message  =   [
        'title.require' => '章节名称必须填写！',
        'content.require' => '章节内容必须填写！',
    ];  
}