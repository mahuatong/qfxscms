<?php


namespace app\model;
use think\Model;
use think\facade\Request;

class AddonsModel extends Model{

    protected $name = 'addons';
    protected $autoWriteTimestamp = true;

	public function info($id){
		$map['id'] = $id;
    	$info=AddonsModel::where($map)->find();
		return $info;
	}

    public function lists(){
        return AddonsModel::order('sort asc')->paginate(config('web.list_rows'));
    }

	public function edit($data){
        if(empty($data['id'])){
            $result = AddonsModel::allowField(true)->save($data);
        }else{
            $result = AddonsModel::allowField(true)->isUpdate(true)->save($data);
        }
        if(false === $result){
            $this->error=AddonsModel::getError();
            return false;
        }
        return $result;
    }

    public function del($id){
        $map = ['id' => $id];
        $result = AddonsModel::where($map)->delete();
        if(false === $result){
            $this->error=AddonsModel::getError();
            return false;
        }else{
            return $result;
        }
    }
}