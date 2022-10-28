<?php


namespace app\home\controller;

use think\facade\Config;
use think\facade\Cookie;

class Chapter extends Base
{
    public function index()
    {
        Cookie::set('__forward__',$this->request->url());
        $id=$this->request->param('id');
        $key=$this->request->param('key');
        $chapter=(new \app\model\CommonApi())->get_chapter($id,$key);
        $info=(new \app\model\CommonApi())->novel_detail($chapter['novel_id']);
        if(!$info){
            $error = (new \app\model\CommonApi())->getError();
            $this->error(empty($error) ? '未找到该小说！' : $error,url('Home/Index/index'));
        }
        $info['chapter']=$chapter;
        if(Config::get('web.login_reader') && !UID){
            if(Cookie::has('reader_num_'.$id)){
                Cookie::set('reader_num_'.$id,Cookie::get('reader_num_'.$id)+1,3600*24);
            }else{
                Cookie::set('reader_num_'.$id,1,3600*60*24);
            }

        }
        $reader_config=Cookie::get('reader_config_'.$this->mold,'');
        if(empty($reader_config)){
            if($this->mold=='web'){
                $reader_config=[0,18,1,0,1];
            }else{
                $reader_config=['d',3,0,'v'];
            }
        }else{
            $reader_config=explode('|',$reader_config);
        }
        $this->assign($info,[]);
        $this->assign('is_bookshelf',0);
        $this->assign('add_bookshelf','onclick=add_bookshelf()');
        $this->assign('reader_config',$reader_config);
        $this->assign('reader_tplpath','/public/'.$this->mold.'/');
        if($this->mold=='web'){
            $this->assign('recentread',[]);
        }
        return $this->fetch('../reader/'.$this->mold.'/index');
    }

    public function lists(){
       $this->assign('id',$this->request->param('id'));
       return $this->fetch('../reader/'.$this->mold.'/list');
    }

    public function info(){
        $id=$this->request->param('id');
        $key=$this->request->param('key');
        $chapter=(new \app\model\CommonApi())->get_chapter($id,$key);
        $chapter['vip']=0;
        $chapter['nextVip']=0;
        $info=(new \app\model\CommonApi())->novel_detail($chapter['novel_id']);
        $info['chapter']=$chapter;
        if(Config::get('web.login_reader') && !UID){
            if(Cookie::has('reader_num_'.$id)){
                Cookie::set('reader_num_'.$id,Cookie::get('reader_num_'.$id)+1,3600*24);
            }else{
                Cookie::set('reader_num_'.$id,1,3600*60*24);
            }
            if(Config::get('web.login_reader_num')<Cookie::get('reader_num_'.$id)){
                if(mb_strlen($info['chapter']['content'])>500){
                    $info['chapter']['content']=substr($info['chapter']['content'],0,strpos($info['chapter']['content'],'<p/>',300));
                }
                if($this->mold=='web'){
                    $info['chapter']['content'].='<div class="login-reader-wrap"><h3>请登录后阅读</h3><div class="subscribe-btn-wrap dib-wrap no-subscribe-btn-box"><a class="single j_subscribeBtn" href="'.url('user/user/login').'"><em>登录</em></a></div></div>';
                }else{
                    $info['chapter']['content'].='<a href="'.url('user/user/login').'" data-size="14" class="btn-primary read-btn-read-login">登录阅读</a>';
                }
            }
        }
        return json(['code'=>1,'data'=>$info]);
    }

    public function updread($novel_id,$id,$key){
    	//阅读记录
        model("user/recentread")->add($novel_id,$id,$key);
        return json(['code'=>1]);
    }
}
