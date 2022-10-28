<?php


namespace app\model;
use think\Model;
use think\facade\Db;
use think\facade\Request;
use think\facade\Cache;
use think\facade\Config;
use net\Http;
use org\Oauth;

class Upgrade extends Model{

    private $oauth_access_token;

    protected function initialize(){
        parent::initialize();
        $auth = new Oauth();
        $this->oauth_access_token="AuthorizationCode: OAuth =".$auth->getToken();
    }

    public function version($version,$name=null){
        $update=false;
        $url=Config::get('web.official_url').'/upgrade/version';
        if($name){
            $url=$url.'/'.$name;
        }
        $content=Http::doGet($url);
        $upArray=json_decode($content, true);

        if(!empty($upArray['error'])){
           $update['update']=false;
        }else{
           foreach ($upArray as $param){
                if(tonum($param['version']) > tonum($version)){
                    $update=$param;
                    $update['update']=true;
                    break;
                }
            } 
        }
        return $update;
    }

    public function updates(){
        $num=Request::get('num',0);
        $upArray=$this->upContent();
        $upCode=Http::doGet(Config::get('web.official_url').'/'.$upArray[$num]['file_name']);
        if(!$upCode){
            $this->error="读取远程升级文件错误，请检测网络！";
            return false;
        }
        $dir = dirname($upArray[$num]['stored_file_name']);
        if(!is_dir($dir))
            mkdir($dir,0755,true);
         if(false === @file_put_contents($upArray[$num]['stored_file_name'],$upCode)){
            $this->error="保存文件错误，请检测文件夹写入权限！";
            return false;
         }
        return $num+1;
    }

    public function upContent($id=null,$type=null,$model='updata'){
        $content=Cache::get('update_list');
        if(!$content){
            $url = Config::get('web.official_url').'/upgrade/'.$model.'/'.$id;
            if($type){
                $url = $url.'/'.$type;
            }
            $content=Http::doGet($url,30,$this->oauth_access_token);
            $content=json_decode($content,true);
            Cache::set('update_list',$content);
        }
        return $content;
    }

    //插件安装
    public function insert_install($id){
        $url = Config::get('web.official_url').'/upgrade/info/'.$id;
        $Content=Http::doGet($url,30,$this->oauth_access_token);
        $info=json_decode($Content,true);
        if(!empty($info['error'])){
            $this->error=$info['error'];
           return false;
        }
        $upArray=$this->upContent();
        if($this->install_file($upArray)==false){
            return false;
        }
        switch ($info['type']) {
            case 0:
                Db::name("template")->insert(['title'=>$info['title'],'name'=>$info['name'],'version'=>$info['version'],'create_time'=>time()]);
                break;
            case 1:
                controller('addons')->install($info['name'],$info['version']);
                break;
        }
        Cache::delete('route_data');
        Cache::delete('update_list');
        Cache::delete('hooks');
        Cache::delete('admin_menu');
        Cache::delete('user_menu');
        return true;
    }

    /**
     * 安装更新
     */
    public function install(){
        $version=Request::param('version');
        $type=Request::param('type');
        $name=Request::param('name');
        $upArray=$this->upContent();
        if($this->install_file($upArray)==false){
            return false;
        }
        switch ($type) {
            case 0:
                Db::name("template")->where(['name'=>$name])->setField('version',$version);
                break;
            case 1:
                Db::name("addons")->where(['name'=>$name])->setField('version',$version);
                break;
            default:
                Db::name("config")->where(['name'=>'version'])->setField('value',$version);
                Cache::delete('config_data');
                break;
        }
        Cache::delete('route_data');
        Cache::delete('update_list');
        Cache::delete('hooks');
        Cache::delete('admin_menu');
        Cache::delete('user_menu');
        return true;
    }

    private function install_file($list_array){
        foreach ($list_array as $value) {
            if($value['suffix']==='del' || $value['suffix']==='sql'){
                $upCode=file_get_contents($value['stored_file_name']);
                $upCode = str_replace("\r", "\n", $upCode);
                $filePath=explode("\n",$upCode);
                foreach ($filePath as $v){
                    $v = trim($v);
                    if(empty($v)) continue;
                    if($value['suffix']==='del'){
                        @unlink($v);
                    }elseif ($value['suffix']==='sql') {
                        $prefix=Config::get('database.prefix');
                        $upSqlCode = str_replace("`ky_", "`{$prefix}", $v);
                        try{
                            Db::execute($upSqlCode);
                        }catch(\Exception $e){
                            $this->error='执行sql错误代码：'.$e->getMessage();
                            return false;
                        }
                    }
                }
                @unlink($value['stored_file_name']);
            }
        }
        return true;
    }
}