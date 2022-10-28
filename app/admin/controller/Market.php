<?php


namespace app\admin\controller;
use think\facade\Db;
use think\facade\Config;
use think\facade\Cache;
use net\Http;
use think\paginator\driver\Bootstrap;

class Market extends Base
{

    public function addons(){
        $url=config('web.official_url').'/market/addons/'.Config::get('web.list_rows').'/'.$this->request->param('page');
        $data = Http::doGet($url,300);
        $data=json_decode($data,true);
        $paginator = new Bootstrap($data['data'],$data['per_page'],$data['current_page'],$data['total'],false,['path'=>url()]);
        $this->assign('list', $data['data']);
        $this->assign('page',$paginator->render());
        $this->assign('meta_title','插件列表');
        return $this->fetch();
    }
	
	public function template(){
        $url=config('web.official_url').'/market/index/4/'.Config::get('web.list_rows').'/'.$this->request->param('page');
        $data = Http::doGet($url,300);
        $data=json_decode($data,true);
        $paginator = new Bootstrap($data['data'],$data['per_page'],$data['current_page'],$data['total'],false,['path'=>url()]);
        $this->assign('list', $data['data']);
        $this->assign('page',$paginator->render());
        $this->assign('meta_title','模版列表');
        return $this->fetch();
    }

    public function info($id){
        $url = Config::get('web.official_url').'/market/info/'.$id;
        $Content=Http::doGet($url,30);
        $info=json_decode($Content,true);
        if(!empty($info['error'])){
            $this->error($info['error'],'');
        }
        $this->assign('info',$info);
        $this->assign('meta_title','详情');
        return $this->fetch();
    }

    public function get_insert($name,$type){
        return Db::name($type)->where('name',$name)->value('id');
    }

    public function oiauth_reg(){
        if($this->request->isPost()){
            $this->oiauth_user();
        }else{
            $this->assign('meta_title','联盟用户注册');
            return $this->fetch();
        }
    }

    public function oiauth_login(){
        if($this->request->isPost()){
            $this->oiauth_user('login');
        }else{
            $this->assign('meta_title','联盟用户登录');
            return $this->fetch();
        }
    }

    public function oiauth_forgetpwd(){
        if($this->request->isPost()){
            $http = new Http();
            $url=Config::get('web.official_url').'/oauth/forgetpwd';
            $return=$http->doPost($url,$this->request->post(),60,"Referer:".$this->request->domain()."\r\nCookie:PHPSESSID=".$this->request->post('cookie'));
            $return=json_decode($return,true);
            if($return['code']==1){
                return $this->success($return['msg'],'oiauth_login');
            }else{
                $this->error($return['msg'],'');
            }
        }else{
            $this->assign('meta_title','联盟用户找回密码');
            return $this->fetch();
        }
    }

    private function oiauth_user($type='reg'){
        $http = new Http();
        $url=Config::get('web.official_url').'/oauth/'.$type;
        $return=$http->doPost($url,$this->request->post(),60,"Referer:".$this->request->domain()."\r\nCookie:PHPSESSID=".$this->request->post('cookie'));
        $return=json_decode($return,true);
        if(!empty($return['success'])){
            Db::name("Config")->where(['name'=>'client_id'])->setField('value',$return['client_id']);
            Db::name("Config")->where(['name'=>'client_secret'])->setField('value',$return['client_secret']);
            Cache::delete('config_data');
            Cache::delete('hx_oauth_access_token');
            return $this->success($return['success'],$this->request->cookie('__forward__'));
        }else{
            $this->error($return['error'],'');
        }
    }
}