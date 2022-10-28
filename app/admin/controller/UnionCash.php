<?php


namespace app\admin\controller;
use think\facade\Db;
use think\paginator\driver\Bootstrap;
use org\Oauth;

class UnionCash extends Base
{
    protected $beforeActionList = ['checkAuth'];

    protected function checkAuth(){
        $auth = new Oauth();
        if(!$auth->checkAuth()){
            return false;
        }
    }

    public function lists(){
        $res=(new \app\model\UnionCash())->lists();
        if(!empty($res['code'])){
            $paginator = new Bootstrap($res['data']['data'],$res['data']['per_page'],$res['data']['current_page'],$res['data']['total'],false,['path'=>url()]);
            $this->assign('list', $res['data']['data']);
            $this->assign('page',$paginator->render());
            $this->assign('meta_title', '提现记录');
            return $this->fetch();
        }else{
            $this->error($res['msg'],'');
        }
    }

    public function add(){
        $UnionCash=(new \app\model\UnionCash());
        if($this->request->isPost()){
            $data=$this->request->post();
            $res=$UnionCash->add($data);
            if($res  !== false){
                return $this->success('提现提交成功！','');
            } else {
                $this->error();
            }
        }else{
            $this->assign('meta_title', '提现');
            return $this->fetch();
        }
    }
}