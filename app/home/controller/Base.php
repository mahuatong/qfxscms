<?php


namespace app\home\controller;
use app\BaseController;
use think\facade\Db;
use think\facade\Config;
use think\facade\Env;
use think\facade\Cache;
use net\Http;

class Base extends BaseController {
    protected $mold;
    protected $home_tplpath='public/homeview/';

    protected function initialize(){
        if(!Config::get('web.close')){
            $this->error(Config::get('web.close_tip'));
        }
        if(!defined('UID')){
            define('UID',is_login());
        }
        $this->mold=($this->request->isMobile())?'wap':'web';
        if(checkDomain($this->request->domain(),Config::get('web.wap_url')) && $this->mold!='wap'){
            $this->mold='wap';
        }elseif(Config::get('web.wap_url') && !checkDomain($this->request->domain(),Config::get('web.wap_url')) && $this->mold=='wap'){
            if(strpos(Config::get('web.wap_url'),'://') !==false){
                $this->redirect(Config::get('web.wap_url'),302);
            }else{
                $this->redirect("http://".Config::get('web.wap_url'),302);
            }
        }
//        $map[] = ['','exp',Db::raw('find_in_set("'.$this->mold.'",`mold`)')];
//        $map[] = ['default','=',1];
//        $tpl_name=Db::name('Template')->where($map)->value('name');

        $this->assign('web',Config::get('web'));
        $map=[];
        $map[] = ['','exp',Db::raw('find_in_set("'.$this->mold.'",`mold`)')];
        $map[] = ['status','=',1];
        $map[] = ['group','=','author'];
        $author_show=Db::name('addons')->where($map)->value('name');

        $this->assign('author_show',$author_show);
        $this->assign('mold',$this->mold);
        $this->assign('home_tplpath','/'.$this->home_tplpath);
	}

}