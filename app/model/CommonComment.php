<?php


namespace app\model;
use think\Model;
use think\facade\Db;
use think\Validate;
use think\facade\Config;
use think\facade\Cache;

class CommonComment extends Model {

    protected $name = 'common';

    protected $autoWriteTimestamp = true;
    protected $createTime = 'create_time';
    protected $updateTime = false;
    protected $auto = ['content'];
    protected $insert = ['status' => 1];

	public function comment_add($data){
        return true;
    }

    /**
     * 获取分类树，指定分类则返回指定分类极其子分类，不指定则返回所有分类树
     * @param  integer $id    分类ID
     * @param  boolean $field 查询字段
     * @return array          分类树
     */
    public function get_tree($mid, $type='novel', $id = 0, $field = true, $api = false){

        return [];
    }

    protected function setContentAttr($value){
        $str = htmlspecialchars($value);
        $comment_key = preg_split('/[\r\n]+/', trim(Config::get('web.comment_key'), "\r\n"));
        $str = str_replace($comment_key, '***', $str);
        return $str;
    }
}
