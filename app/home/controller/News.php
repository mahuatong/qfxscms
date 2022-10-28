<?php


namespace app\home\controller;

use think\facade\Cookie;

class News extends Base
{
    public function index()
    {
        Cookie::set('__forward__',$this->request->url());
        $id=$this->request->param('id');
        $info=(new \app\model\CommonApi())->news_detail($id);
        if(!$info){
            $error = (new \app\model\CommonApi())->getError();
            $this->error(empty($error) ? '未找到该文章！' : $error,url('Home/Index/index'));
        }
        if(empty($info['template'])){
            $tpl=(new \app\model\CommonApi())->get_tpl($info['cid'],'template_detail');
            if(empty($tpl)){
                $error = (new \app\model\CommonApi())->getError();
                $this->error(empty($error) ? '未知错误！' : $error);
            }
        }else{
            $tpl=$info['template'];
        }
        if(!empty($info['link'])){
            $this->redirect($info['link'],302);
        }
        (new \app\model\CommonApi())->hits($id,'news');
        $this->assign('pos',1);
        $this->assign('type','news');
        $this->assign($info);
        return $this->fetch($this->home_tplpath.$tpl);
    }

    public function digg($id,$digg){
        $return=(new \app\model\CommonApi())->digg($id,'news',$digg);
        if($return){
           return $this->success($digg.'+1');
        }else{
           $this->error('请不要重复操作！');
        }
    }
}
