<?php


namespace app\model;
use think\Model;
use think\facade\Db;
use think\facade\Config;
use think\facade\Cache;
use net\Http;
use org\Oauth;

class CommonUnionChapter extends Model{

    private $oauth_access_token;

    protected function initialize(){
        parent::initialize();
        $auth = new Oauth();
        $this->oauth_access_token="AuthorizationCode: OAuth =".$auth->getToken();
    }

	public function get_chapter($union_id){
		$union_url=explode('_', $union_id);
        $server=Cache::get('server_'.$union_url[0]);
        if(empty($server)){
            $server_url=Config::get('web.official_url').'/union/server/server/'.$union_url[0];
            $server=Http::doGet($server_url,90,$this->oauth_access_token);
            $server=json_decode($server,true);
            Cache::set('server_'.$union_url[0],$server);
        }
        if($server['code']==1){
            $chapter_url=$server['url'].'/api/novel/chapter/id/'.$union_url[2].'/key/'.$union_url[3];
            $chapter=Http::doGet($chapter_url,90,$this->oauth_access_token);
            $chapter=json_decode($chapter,true);
            if($chapter['content']){
                return ['content'=>$chapter['content'],'intro'=>$chapter['intro']];
            }else{
                $this->error='未获取到数据,请稍后重试！';
                return false;
            }
        }else{
            $this->error='未获取到数据！';
            return false;
        }
    }
}