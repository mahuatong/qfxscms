<?php
namespace app\admin\validate;
use think\Validate;
use think\facade\Db;

class Category extends Validate{
	protected $rule =   [
        'title'  => 'require',
        'sort' => 'number',
        'template_index' => 'require',
        'template_detail' => 'require',
        'template_filter' => 'require',
    ];

    protected $message  =   [
        'title.require' => '标题必须填写！',
        'sort.number' => '排序只能是数字！',
        'template_index.require' => '栏目模版必须填写！',
        'template_detail.require' => '内容模版必须填写！',
    ];  
}