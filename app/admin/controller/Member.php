<?php


namespace app\admin\controller;
use app\model\MemberModel;
use think\facade\Db;

class Member extends Base
{

    public function index(){
        $list  = Db::name('member')->paginate(config('web.list_rows'));
        $this->assign('list', $list);
        $this->assign('meta_title','管理员列表');
        return $this->fetch();
    }

    public function password(){
		 if($this->request->isPost()){
            $Member=(new MemberModel());
			$res  =  $Member->password();
			if($res  !== false){
				$this->success('修改密码成功！',url('index'));
			}else{
				$this->error();
			}
		 }else{
		 	$username = Db::name('member')->where('id',$this->request->param('id'))->value('username');
			$this->assign('username', $username);
            $this->assign('meta_title','修改密码');
			return $this->fetch();
		 }
    }

    public function group(){
        exception('不可用');
    }
	
	public function add(){
		if($this->request->isPost()){
            $Member=(new \app\model\MemberModel());
			if($this->request->post("password") != $this->request->post("repassword")){
                $this->error('密码和重复密码不一致！');
            }
			if(Db::name('member')->where(['username'=>$this->request->post("username")])->value('id')){
				$this->error('用户名已被占用！');
			}
			$res = $Member->reg();
			if($res  !== false){
                $this->success('用户添加成功！',url('index'));
            } else {
                $this->error();
            }
		}else{
            $this->assign('meta_title','添加管理员');
			return $this->fetch();
		}
	}
	
    public function del(){
        $id = array_unique((array)$this->request->param('id'));
        if ( empty($id) ) {
            $this->error('请选择要操作的数据!');
        }
        $Member=(new \app\model\MemberModel());
        $res = $Member->del($id);
        if($res !== false){
            $this->success('删除成功');
        } else {
            $this->error('删除失败！');
        }
    }
}