<?php


namespace app\model;
use think\Model;
use think\facade\Db;
use think\facade\Request;
use think\facade\Config;
use think\facade\Cache;
use net\Http;
use org\Oauth;

class Union extends Model{

    private $oauth_access_token;

    protected function initialize(){
        parent::initialize();
        $auth = new Oauth();
        $this->oauth_access_token="AuthorizationCode: OAuth =".$auth->getToken();
    }

	public function user_info(){
        $url=Config::get('web.official_url').'/oauth/user';
		$content=Http::doGet($url,30,$this->oauth_access_token);
        $content=json_decode($content,true);
		return $content;
	}

    public function user_passw($data){
        $url=Config::get('web.official_url').'/oauth/passw';
        $content=Http::doPost($url,$data,30,$this->oauth_access_token);
        $content=json_decode($content,true);
        if(empty($content['code'])){
            $this->error=$content['msg'];
            return false;
        }else{
            Db::name("Config")->where(['name'=>'client_secret'])->setField('value',$content['client_secret']);
            Cache::delete('config_data');
            Cache::delete('hx_oauth_access_token');
            return true;
        }
    }

    public function user_domain($data){
        $url=Config::get('web.official_url').'/oauth/domain';
        $content=Http::doPost($url,$data,30,$this->oauth_access_token);
        $content=json_decode($content,true);
        if(empty($content['code'])){
            $this->error=$content['msg'];
            return false;
        }else{
            Cache::delete('hx_oauth_access_token');
            return true;
        }
    }

    public function user_logout(){
        Db::name("Config")->where(['name'=>'client_id'])->setField('value','');
        Db::name("Config")->where(['name'=>'client_secret'])->setField('value','');
        Cache::delete('config_data');
        Cache::delete('hx_oauth_access_token');
    }

    public function user_data(){
        $url=Config::get('web.official_url').'/union/user/data';
        $content=Http::doGet($url,30,$this->oauth_access_token);
        $content=json_decode($content,true);
        return $content;
    }

    public function integral_add($data){
        $url=Config::get('web.official_url').'/union/integral/add';
        $content=Http::doPost($url,$data,30,$this->oauth_access_token);
        $content=json_decode($content,true);
        if(empty($content['code'])){
            $this->error=$content['msg'];
            return false;
        }else{
            return true;
        }
    }
	
    public function integral_list(){
        $url=Config::get('web.official_url').'/union/integral/list/'.Config::get('web.list_rows').'/'.Request::param('type',0);
        if($p=Request::param('page')){
            $url.='/'.$p;
        }
        $content=Http::doGet($url,30,$this->oauth_access_token);
        $content=json_decode($content,true);
        if(empty($content['code'])){
            $this->error=$content['msg'];
            return false;
        }else{
            return $content;
        }
    }

    public function integral_del($id){
        $url=Config::get('web.official_url').'/union/integral/del/'.$id;
        $content=Http::doGet($url,30,$this->oauth_access_token);
        $content=json_decode($content,true);
        if(empty($content['code'])){
            $this->error=$content['msg'];
            return false;
        }else{
            return true;
        }
    }

    public function log($type){
        $url=Config::get('web.official_url').'/union/log/'.$type."/".Config::get('web.list_rows');
        $content=Http::doGet($url,30,$this->oauth_access_token);
        $content=json_decode($content,true);
        return $content;
    }
}