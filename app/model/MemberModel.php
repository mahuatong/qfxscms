<?php


namespace app\model;
use think\facade\Request;
use think\Model;

class MemberModel extends Model {
	protected $name = 'member';

    protected $insert = ['status'=>1];

    protected function setPasswordAttr($value){
        return think_ucenter_md5($value);
    }

	public function info($uid){
		$member = new MemberModel;
		$map['id'] = $uid;
		$map['status']=1;
		$info=$member->where($map)->field('id,username,last_login_time')->find();
		return $info;
	}

	public function reg(){
		$data=Request::post();
		$member = new MemberModel;
		$result = $member->save($data);
		if(!$result){
			exception('添加失败');
		}
		return $result;
	}
	
	public function password(){
		$data=Request::post();
		if(!$this->verifyUser($data['id'], $data['oldpassword'])){
			exception('旧密码不正确');
			return false;
		}
	
        $result = MemberModel::update($data,['id'=>$data['id']]);
        if(!$result){
		    exception('修改密码错误');
		}
        return $result;
    }
	
	protected function verifyUser($uid, $password_in){
		$password = MemberModel::where('id',$uid)->value('password');
		if(think_ucenter_md5($password_in) === $password){
			return true;
		}
		return false;
	}

	public function del($id){
        $map = ['id' => $id];
        $result = MemberModel::where($map)->delete();
        if(false === $result){
            $this->error=MemberModel::getError();
            return false;
        }else{
            return $result;
        }
    }
}