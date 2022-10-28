<?php
namespace app\admin\controller;
use think\facade\Db;
use think\paginator\driver\Bootstrap;
use org\Oauth;

class Union extends Base
{
    protected $beforeActionList = ['checkAuth'];

    protected function checkAuth(){
        $auth = new Oauth();
        if(!$auth->checkAuth()){
            return false;
        }
    }

    public function user(){
        $res=(new \app\model\Union())->user_info();
        if(!empty($res['code'])){
            $user_data=(new \app\model\Union())->user_data();
            $this->assign('user',$res['user']);
            $this->assign('user_data', $user_data);
            $this->assign('meta_title', '用户中心');
            return $this->fetch();
        }else{
            $this->error($res['msg'],'');
        }
    }

    public function user_passw(){
        $Union=(new \app\model\Union());
        if($this->request->isPost()){
            $data=$this->request->post();
            $res=$Union->user_passw($data);
            if($res  !== false){
                return $this->success('密码修改成功！','');
            } else {
                $this->error();
            }
        }else{
            $this->assign('meta_title', '修改密码');
            return $this->fetch();
        }
    }

    public function user_domain(){
        $Union=(new \app\model\Union());
        if($this->request->isPost()){
            $data=$this->request->post();
            $res=$Union->user_domain($data);
            if($res  !== false){
                return $this->success('域名修改成功！','');
            } else {
                $this->error();
            }
        }else{
            $res=$Union->user_info();
            $this->assign('user',$res['user']);
            $this->assign('meta_title', '修改域名');
            return $this->fetch();
        }
    }

    public function user_logout(){
        (new \app\model\Union())->user_logout();
        return $this->success('成功退出联盟登录！','');
    }

    public function data_log(){
        $res=(new \app\model\Union())->log('data');
        if(!empty($res['msg'])){
            $this->error($res['msg'],'');
        }
        $paginator = new Bootstrap($res['data'],$res['per_page'],$res['current_page'],$res['total'],false,['path'=>url()]);
        $this->assign('list', $res['data']);
        $this->assign('page',$paginator->render());
        $this->assign('meta_title', '消费记录');
        return $this->fetch();
    }

    public function market_log(){
        $res=(new \app\model\Union())->log('market');
        if(!empty($res['msg'])){
            $this->error($res['msg'],'');
        }
        $paginator = new Bootstrap($res['data'],$res['per_page'],$res['current_page'],$res['total'],false,['path'=>url()]);
        $this->assign('list', $res['data']);
        $this->assign('page',$paginator->render());
        $this->assign('meta_title', '消费记录');
        return $this->fetch();
    }

    public function collect_log(){
        $res=(new \app\model\Union())->log('collect');
        if(!empty($res['msg'])){
            $this->error($res['msg'],'');
        }
        $paginator = new Bootstrap($res['data'],$res['per_page'],$res['current_page'],$res['total'],false,['path'=>url()]);
        $this->assign('list', $res['data']);
        $this->assign('page',$paginator->render());
        $this->assign('meta_title', '消费记录');
        return $this->fetch();
    }

    public function integral(){
        $res=(new \app\model\Union())->integral_list();
        if(!empty($res['code'])){
            $paginator = new Bootstrap($res['data']['data'],$res['data']['per_page'],$res['data']['current_page'],$res['data']['total'],false,['path'=>url('',['type'=>$this->request->param('type')])]);
            $this->assign('list', $res['data']);
            $this->assign('page',$paginator->render());
            $this->assign('meta_title', '积分中心');
            return $this->fetch();
        }else{
            $this->error($res['msg'],'');
        }
    }

    public function integral_add(){
        $Union=(new \app\model\Union());
        if($this->request->isPost()){
            $data=$this->request->post();
            $res=$Union->integral_add($data);
            if($res  !== false){
                return $this->success('积分出售提交成功！','');
            } else {
                $this->error();
            }
        }else{
            $user=$Union->user_info();
            $this->assign('user',$user['user']);
            $this->assign('meta_title', '出售积分');
            return $this->fetch();
        }
    }

    public function integral_del($id){
        $Union=(new \app\model\Union());
        $res=$Union->integral_del($id);
        if($res  !== false){
            return $this->success('取消积分出售成功，积分已返还帐户！','');
        } else {
            $this->error();
        }
    }
}