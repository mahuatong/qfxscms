<?php
namespace app\model;
use think\Model;
use think\facade\Config;
use net\Http;
use think\facade\Db;
use org\Oauth;

class Check extends Model{

    public function index(){
        $auth = new Oauth();
        if(!$auth->checkAuth()){
            return false;
        }
        $oauth_access_token="AuthorizationCode: OAuth =".$auth->getToken();
        $url=Config::get('web.official_url').'/check/';
        $data=[];
        $data['addons']=Db::name('addons')->column('name');
        $data['template']=Db::name('template')->column('name');
        $content=Http::doPost($url,$data,60,$oauth_access_token);
        $content=json_decode($content,true);
        foreach ($content as $key => $value) {
        	if(!empty($value)){
        		foreach ($value as $k => $v) {
        			Db::name($key)->where(['name'=>$v])->delete();
        			if($key=='template'){
        				del_dir_file('./'.$key.'/home/'.$v,true);
        			}else{
        				del_dir_file('./'.$key.'/'.$v,true);
        			}
        		}
        	}else{
        		// Db::name('crontab')->where(['class_name'=>'admin/Check'])->update(['run_time'=>time()+2592000]);
        	}
        }
    }
}