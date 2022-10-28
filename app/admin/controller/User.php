<?php


namespace app\admin\controller;
use app\model\UserModel;
use think\facade\Db;

class User extends Base
{

    public function index(){
        $User= (new UserModel());
        $list = $User->lists();
        $this->assign('list', $list);
        $this->assign('meta_title','用户列表');
        return $this->fetch();
    }
	
	public function edit($id){
		$User=(new \app\model\UserModel());
		if($this->request->isPost()){
            $data=$this->request->post();
            unset($data['password']);
			$res = $User->edit($data);
			if($res  !== false){
                return $this->success('用户修改成功！',url('index'));
            } else {
                $this->error('');
            }
		}else{
			$info=$User->info($id);
            $this->assign('info',$info);
			$this->assign('meta_title','修改用户');
			return $this->fetch();
		}
	}

    public function password($id){
        $User=(new \app\model\UserModel());
        if($this->request->isPost()){
            $data=$this->request->post();
            $res = $User->edit($data);
            if($res  !== false){
                return $this->success('用户密码修改成功！',url('index'));
            } else {
                $this->error('');
            }
        }
    }

	public function add(){
		$User=(new \app\model\UserModel());
		if($this->request->isPost()){
            $data=$this->request->post();
			$res = $User->edit($data);
			if($res  !== false){
                return $this->success('用户添加！',url('index'));
            } else {
                $this->error('');
            }
		}else{
			$this->assign('meta_title','添加用户');
			return $this->fetch('edit');
		}
	}

	public function del(){
        $User=(new \app\model\UserModel());
        $id = array_unique((array)$this->request->param('id'));
        if ( empty($id) ) {
            $this->error('请选择要操作的数据!');
        }
        $res=$User->del($id);
        if($res){
            return $this->success('删除成功');
        } else {
            $this->error('');
        }
    }

    public function status(){
        $id = $this->request->param('id');
        $User=(new \app\model\UserModel());
        $info = $User->info($id);
        if($info['status']==1){
            return $this->forbid('User');
        }else{
            return $this->resume('User');
        }
    }
}