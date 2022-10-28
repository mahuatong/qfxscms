<?php


namespace app\admin\controller;

class UserGroup extends Base
{

    public function index(){
        $UserGroup= (new \app\model\UserGroup());
        $list = $UserGroup->lists();
        $this->assign('list', $list);
        $this->assign('meta_title','用户组列表');
        return $this->fetch();
    }
	
	public function edit($id){
		$UserGroup=(new \app\model\UserGroup());
		if($this->request->isPost()){
			$res = $UserGroup->edit();
			if($res  !== false){
                return $this->success('用户组修改成功！',url('index'));
            } else {
                $this->error('');
            }
		}else{
			$info=$UserGroup->info($id);
            $this->assign('info',$info);
			$this->assign('meta_title','修改用户组');
			return $this->fetch();
		}
	}

	public function add(){
		$UserGroup=(new \app\model\UserGroup());
		if($this->request->isPost()){
			$res = $UserGroup->edit();
			if($res  !== false){
                return $this->success('用户组添加！',url('index'));
            } else {
                $this->error('');
            }
		}else{
			$this->assign('meta_title','添加用户组');
			return $this->fetch('edit');
		}
	}

	public function del(){
        $id = array_unique((array)$this->request->param('id'));
        if ( empty($id) ) {
            $this->error('请选择要操作的数据!');
        }
        $UserGroup=(new \app\model\UserGroup());
        $res = $UserGroup->del($id);
        if($res !== false){
            return $this->success('删除成功');
        } else {
            $this->error('删除失败！');
        }
    }

    public function status(){
        $id = $this->request->param('id');
        $UserGroup=(new \app\model\UserGroup());
        $info = $UserGroup->info($id);
        if($info['status']==1){
            return $this->forbid('UserGroup');
        }else{
            return $this->resume('UserGroup');
        }
    }
}