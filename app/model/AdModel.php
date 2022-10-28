<?php


namespace app\model;
use think\Model;

use app\admin\validate\Ad as AdValidate;

class AdModel extends Model{

    protected $name = 'ad';
    protected $autoWriteTimestamp = true;
    protected $insert = ['status'=>1];

	public function info($id){
		$map['id'] = $id;
    	$info=AdModel::where($map)->find();
		return $info;
	}

    public function lists(){
        $map = [];
        $map[] = ['status','=',1];
        $list=AdModel::where($map)->order('update_time desc')->paginate(config('web.list_rows'));
        return $list;
    }

	public function edit($data){
        $validate = new AdValidate;
        if (!$validate->check($data)) {
            $this->error=$validate->getError();
            return false;
        }
        $Ad = new AdModel();
        if(empty($data['id'])){
            $result = $Ad->save($data);
        }else{
            $result = $Ad->isUpdate(true)->save($data);
        }
        if(false === $result){
            $this->error=$Ad->getError();
            return false;
        }
        return $result;
    }

    public function del($id){
        $map = ['id' => $id];
        $result = AdModel::where($map)->delete();
        if(false === $result){
            $this->error=AdModel::getError();
            return false;
        }else{
            return $result;
        }
    }
}