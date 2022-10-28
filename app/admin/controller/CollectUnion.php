<?php


namespace app\admin\controller;
use think\paginator\driver\Bootstrap;
use org\Oauth;

class CollectUnion extends Base
{
    protected $beforeActionList = ['checkAuth'];

    protected function checkAuth(){
        $auth = new Oauth();
        if(!$auth->checkAuth()){
            return false;
        }
    }

    public function index(){
        $res=(new \app\model\CollectUnion())->lists();
        if(!empty($res['code'])){
            $paginator = new Bootstrap($res['data']['data'],$res['data']['per_page'],$res['data']['current_page'],$res['data']['total'],false,['path'=>url('')]);
            $this->assign('list', $res['data']);
            $this->assign('page',$paginator->render());
            $this->assign('meta_title', '采集规则联盟');
            return $this->fetch();
        }else{
            $this->error($res['msg'],'');
        }
    }

    public function release(){
        if($this->request->isPost()){
            $data=$this->request->param();
            $res = (new \app\model\CollectUnion())->release($data);
            if($res  !== false){
                $this->success('规则发布成功！',url('index'));
            } else {
                $this->error((new \app\model\CollectUnion())->getError());
            }
        }else{
           $this->assign('collect_id',$this->request->param('id'));
            $this->assign('meta_title','发布采集规则');
            return $this->fetch(); 
        }
    }

    public function buy($id){
        $res = (new \app\model\CollectUnion())->buy($id);
        if(!empty($res['pay'])){
            $this->error('您的积分不够？',url('union/integral'));
        }
        if(empty($res['code'])){
            $this->error($res['code'],'');
        }
        $res = (new \app\model\CollectModel())->edit($res['data']);
        if($res  !== false){
            return $this->success('购买成功！','');
        } else {
            $this->error((new \app\model\CollectModel())->getError());
        }
        
    }
}