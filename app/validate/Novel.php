<?php
namespace app\admin\validate;
use think\Validate;
use think\facade\Db;

class Novel extends Validate{
	protected $rule =   [
        'id' => 'require',
        'title'  => 'require|checkTitle:true',
        'category' => 'require',
        'pic' => 'require',
        'author' => 'require',
        'content' => 'require',
    ];

    protected $message  =   [
        'id.require' => 'ID必须填写！',
        'title.require' => '书名必须填写！',
        'category.require' => '必须选择栏目！',
        'pic.require' => '封面必须填写！',
        'author.require' => '作者必须填写！',
        'content.require'  => '介绍必须填写！',
    ];

    protected $scene = [
        'edit'  =>  ['title','category','pic','author','content','id'],
        'add' => ['title','category','pic','author','content'],
        'position' => ['id']
    ];

    protected function checkTitle($value,$rule,$data=[])
    {
        $id=Db::name('novel')->where(["title"=>$value,"author"=>$data['author']])->value('id');
        return empty($id)?true:'该作品已经存在！';
    }

    public function sceneEdit(){
        return $this->remove('title', 'checkTitle');
    }
}