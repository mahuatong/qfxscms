<?php


namespace app\model;
use think\Model;
use think\facade\Request;
use think\facade\Cache;
use app\admin\validate\Category as CategoryValidate;

class CategoryModel extends Model{

    protected $name = 'category';
    protected $autoWriteTimestamp = true;
    protected $insert = ['status'=>1];

    public function getTypeTextAttr($value,$data)
    {
        $status = [0=>'小说',1=>'文章',2=>'独立模版',3=>'外链'];
        return $status[$data['type']];
    }

	public function info($id){
		$map['id'] = $id;
    	$info=CategoryModel::where($map)->find();
		return $info;
	}

    /**
     * 获取分类树，指定分类则返回指定分类极其子分类，不指定则返回所有分类树
     * @param  integer $id    分类ID
     * @param  boolean $field 查询字段
     * @return array          分类树
     */
    public function getTree($id = 0, $field = true){
        /* 获取当前分类信息 */
        $Tree = new \tree\Tree;
        $Tree::$treeList = [];
        $list = CategoryModel::field($field)->order('pid asc,id asc,sort asc')->select();
        return $Tree->tree($list,0,0,'&nbsp;&nbsp;&nbsp;&nbsp;');
    }

	public function edit(){
        $data=Request::post();
        unset($data['ptitle']);
        unset($data['file']);
        if(empty($data['id'])){
            CategoryModel::create($data);
        }else{
            CategoryModel::where('id','=',$data['id'])->update($data);
        }
        Cache::delete('category');
        return [];
    }

    public function del($id){
        $result = CategoryModel::destroy($id);
        return $result;
    }
}