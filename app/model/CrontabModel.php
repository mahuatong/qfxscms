<?php


namespace app\model;
use think\Model;
use app\admin\validate\Crontab as CrontabValidate;

class CrontabModel extends Model{

    protected $name = 'crontab';
    protected $autoWriteTimestamp = true;
    protected $json = ['content'];
    protected $jsonAssoc = true;

	public function info($map){
    	$info=CrontabModel::where($map)->find();
		return $info;
	}

	public function edit($data){
        $validate = new CrontabValidate;
        if (!$validate->check($data)) {
            $this->error=$validate->getError();
            return false;
        }
        $Crontab = new CrontabModel();
        if(empty($data['id'])){
            $result = $Crontab->save($data);
        }else{
            $result = $Crontab->isUpdate(true)->save($data);
        }
        if(false === $result){
            $this->error='';
            return false;
        }
        return $result;
    }

    public function del($id){
        if ( empty($id) ) {
            $this->error='定时任务还为添加无法取消！';
            return false;
        }
        $map = ['id' => $id];
        $result = CrontabModel::where($map)->delete();
        if(false === $result){
            $this->error=CrontabModel::getError();
            return false;
        }else{
            return $result;
        }
    }
}