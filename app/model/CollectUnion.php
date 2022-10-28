<?php
namespace app\model;
use think\Model;
use think\facade\Request;
use think\facade\Config;
use think\facade\Db;
use net\Http;
use org\Oauth;

class CollectUnion extends Model{

    private $oauth_access_token;

    protected function initialize(){
        parent::initialize();
        $auth = new Oauth();
        $this->oauth_access_token="AuthorizationCode: OAuth =".$auth->getToken();
    }

    public function release($data){
        $url=Config::get('web.official_url').'/collect/release';
        $db_data=Db::name('collect')->where(['id'=>$data['collect_id']])->find();
        $db_data['integral']=$data['integral'];
        $content=Http::doPost($url,$db_data,30,$this->oauth_access_token);
        $content=json_decode($content,true);
        if($content['code']==0){
            $this->error=$content['msg'];
            return false;
        }else{
            return true;
        }
    }

    public function lists(){
        $url=Config::get('web.official_url').'/collect/lists/'.Config::get('web.list_rows');
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

    public function buy($id){
        $url=Config::get('web.official_url').'/collect/buy/'.$id;
        $content=Http::doGet($url,30,$this->oauth_access_token);
        $content=json_decode($content,true);
        return $content;
    }
}