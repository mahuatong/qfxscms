<?php


namespace app\model;
use think\Model;
use think\facade\Db;
use think\facade\Config;

class CommentModel extends Model{
    protected $name = 'comment';
    protected $autoWriteTimestamp = true;

	public function info($id){
		$map['id'] = $id;
    	$info=CommentModel::where($map)->find();
		return $info;
	}

    public function lists($type='novel',$id=null,$limit=0){
        $map=[];
        $map=['status'=>1];
        $map=['type'=>$type];
        if($id){
            $map=['mid'=>$id];
        }
        $limit=$limit?$limit:Config::get('web.list_rows');
        $list=CommentModel::where($map)->order('up desc,id desc')->paginate($limit);
        return $list;
    }

	public function edit($data){
        if(empty($data['id'])){
            $result = CommentModel::create($data);
        }else{
            $result = CommentModel::update($data);
        }
        return $result;
    }

    public function del($id){
        $map = ['id' => $id];
        $result = CommentModel::where($map)->delete();
        if(false === $result){
            $this->error='';
            return false;
        }else{
            $sub_id=CommentModel::where(['pid'=>$id])->column('id');
            if($sub_id){
                $this->del($sub_id);
            }
            return $result;
        }
    }

    public function get_tree($list, $id = 0){
        foreach ($list as $key => $value) {
            $list[$key]['user']=(new UserModel())->get_info($value['uid'])->toArray();
            $list[$key]['title']=Db::name($value['type'])->where(['id'=>$value['mid']])->value('title');
        }
        $list = list_to_tree($list, $pk = 'id', $pid = 'pid', $child = '_', $root = $id);
        return $list;
    }
}