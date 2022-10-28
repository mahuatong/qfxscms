<?php


namespace app\model;

set_time_limit(0);
ini_set('memory_limit', '-1');

use think\Model;
use think\facade\Db;
use think\facade\Request;
use think\facade\Config;
use think\facade\Cache;
use net\Http;
use net\Gather;
use org\Oauth;

class UnionCollect extends Model{

    private $oauth_access_token;

    protected function initialize(){
        parent::initialize();
        $auth = new Oauth();
        $this->oauth_access_token="AuthorizationCode: OAuth =".$auth->getToken();
    }

    public function info($id){
        $url=Config::get('web.official_url').'/union/server/info/'.$id;
        $content=Http::doGet($url,60,$this->oauth_access_token);
        $content=json_decode($content,true);
        if(isset($content['code'])){
            $this->error=$content['msg'];
            return false;
        }else{
            return $content;
        }
    }

    public function edit($data){
        $url=Config::get('web.official_url').'/union/server/edit';
        $content=Http::doPost($url,$data,60,$this->oauth_access_token);
        $content=json_decode($content,true);
        if($content['code']==0){
            $this->error=$content['msg'];
            return false;
        }else{
            return true;
        }
    }

    public function slist(){
        $url=Config::get('web.official_url').'/union/server/slist';
        $content=Http::doGet($url,60,$this->oauth_access_token);
        $content=json_decode($content,true);
        if(isset($content['code'])){
            $this->error=$content['msg'];
            return false;
        }else{
            return $content;
        }
    }

    public function del($id){
        $url=Config::get('web.official_url').'/union/server/del/'.$id;
        $content=Http::doGet($url,60,$this->oauth_access_token);
        $content=json_decode($content,true);
        if($content['code']==0){
            $this->error=$content['msg'];
            return false;
        }else{
            return true;
        }
    }

    public function server($type){
        $url=Config::get('web.official_url').'/union/server/list/'.$type;
        $content=Http::doGet($url,60,$this->oauth_access_token);
        $content=json_decode($content,true);
        if(is_array($content)){
            return $content;
        }else{
            $this->error='未获取到数据！';
            return false;
        }
    }

    public function lists($url,$type){
        $category_url=url('api/'.$type.'/category','','',$url);
        $category_content=Http::doGet($category_url,60,$this->oauth_access_token);
        $category_content=json_decode($category_content,true);
        $url=url('api/'.$type.'/lists',Request::param(),'',$url);
        $lists_content=Http::doGet($url,60,$this->oauth_access_token);
        $lists_content=json_decode($lists_content,true);
        if(is_array($lists_content) && is_array($category_content)){
            return ['category'=>$category_content,'list'=>$lists_content];
        }else{
            $this->error='未获取到数据！';
            return false;
        }
    }

    public function bind_type($data){
        if($data['category']){
            if((new CommonApi())->get_branch($data['category'])){
                $this->error="该分类下还有分类请选择下属分类";
                return false;
            }
        }
        $bind_type = Cache::get('bind_type');
        if($data['category']){
            $bind_type[$data['sid']][$data['id']]=$data['category'];
        }else{
            unset($bind_type[$data['sid']][$data['id']]);
        }
        Cache::set('bind_type',$bind_type);
        return true;
    }

    public function collect_list($url,$type,$data=[]){
        if(empty($data)){
            $data=Request::param();
        }
        $url=url('api/'.$type.'/lists',$data,'',$url);
        $lists_content=Http::doGet($url,60,$this->oauth_access_token);
        $lists_content=json_decode($lists_content,true);
        if(is_array($lists_content)){
            return $lists_content;
        }else{
            $this->error='未获取到数据！';
            return false;
        }
    }

    public function collect_content($id,$type,$url){
        $url=url('api/'.$type.'/content',['id'=>$id],'',$url);
        $content=Http::doGet($url,60,$this->oauth_access_token);
        $content=json_decode($content,true);
        if(is_array($content)){
            return $content;
        }else{
            $this->error='未获取到数据！';
            return false;
        }
    }

    public function collect_chapter($id,$key,$url){
        $url=url('api/novel/chapter',['id'=>$id,'key'=>$key],'',$url);
        $content=Http::doGet($url,60,$this->oauth_access_token);
        $content=json_decode($content,true);
        if(is_array($content)){
            return $content;
        }else{
            $this->error='未获取到数据！';
            return false;
        }
    }

    public function collect_save($data,$param_data){
        $data_check=$this->collect_check($param_data,$data);
        if($data_check['state']===false){
            return ['title'=>$data['title'],'msg'=>'无更新','count'=>''];
        }
        $data=$this->collect_content($data['id'],$param_data['type'],urldecode($param_data['url']));
        if($data===false){
            return ['title'=>$data['title'],'msg'=>$this->error,'count'=>''];
        }elseif(isset($data['code'])){
            if(!empty($data['pay'])){
                return ['error'=>true,'msg'=>$data['msg'],'url'=>'admin/union/integral','data'=>['state'=>'pay']];
            }else{
                return ['error'=>true,'msg'=>$data['msg'],'url'=>'admin/union/user','data'=>['state'=>'user']];
            }
        }
        $category=$this->change_category($param_data['sid'],$data['cid']);
        if(!$category){
            return ['title'=>$data['title'],'msg'=>'分类['.$data['ctitle'].']未绑定分类'];
        }
        $data['category']=$category;
        $data_save=$this->change_field_data($data,$param_data['type'],$param_data['sid'],$data_check);
        if($data_check['state']==='add'){
            $data_id = Db::name($param_data['type'])->insertGetId($data_save);
            rm_cache();
        }else{
            Db::name($param_data['type'])->update($data_save);
            rm_cache($data_check['id']);
            $data_id = $data_check['id'];
        }
        (new CommonDataOperation())->after($data_check['state'],$param_data['type'],[url('home/'.$param_data['type'].'/index',['id'=>$data_id],true,true)]);
        if($data_id && $param_data['type']=='novel' && $data['chapter']){
            if($data_check['state']==='update'){
                $chapter=$data_check['chapter'];
                $chapter_array=array_column($chapter['chapter'],'title','reurl');
            }else{
                $chapter=['novel_id'=>$data_id,'status'=>1,'chapter'=>[]];
            }
            $update_count=0;
            $link_key=[];
            $data_link=[];
            foreach ($data['chapter'] as $key => $value){
                $chapter_data=[];
                if($data_check['state']==='update' && !empty($chapter_array)){
                    if(isset($chapter_array[$param_data['sid']."_".$data['id']."_".$data['source_id']."_".$value['id']]) || in_array($value['title'],$chapter_array)){
                        continue;
                    }
                }
                $keys=uniqidReal();
                $chapter_data=[
                    'title'=>$value['title']?$value['title']:$value['id'],
                    'intro'=>$value['intro'],
                    'update_time'=>$value['update_time'],
                    'issued'=>1,
                    'word'=>$value['word'],
                    'reurl'=>$param_data['sid']."_".$data['id']."_".$data['source_id']."_".$value['id'],
                    'auto'=>0,
                    'path'=>$data_id.DIRECTORY_SEPARATOR.$keys.'.txt'
                ];
                if(Config::get('web.uinon_collect_chapter_save')==false){
                    $chapter_data['auto']=1;
                }else{
                    $chapter_content=$this->collect_chapter($data['id'],$value['id'],urldecode($param_data['url']));
                    if(!$chapter_content){
                        return false;
                    }
                    if(!$chapter_content['content']){
                        $chapter_data['content']='未找到内容';
                        $chapter_data['auto']=1;
                    }else{
                        $chapter_data['intro']=$chapter_content['intro'];
                        (new CommonApi())->set_chapter_content($chapter_data['path'],$chapter_content['content']);
                    }
                }
                $chapter['chapter'][$keys]=$chapter_data;
                $link_key[]=$keys;
                $update_count++;
            }
            $chapter['chapter']=json_encode($chapter['chapter']);
            $chapter['chapter']=(new CommonApi())->compress_chapter($chapter['chapter']);
            if($data_check['state']==='update'){
                Db::name('novel_chapter')->update($chapter);
                $return=['title'=>$data['title'],'msg'=>'更新成功','count'=>$update_count];
            }else{
                $chapter['id']=Db::name('novel_chapter')->insertGetId($chapter);
                $return=['title'=>$data['title'],'msg'=>'添加成功','count'=>$update_count];
            }
            foreach ($link_key as $key => $value) {
                $data_link[]=url('home/chapter/index',['id'=>$chapter['id'],'key'=>$value],true,true);
            }
            (new CommonDataOperation())->after('add',$param_data['type'],$data_link);
        }
        return $return;
    }

    private function change_category($sid,$cid){
        $bind_type = Cache::get('bind_type');
        if(!empty($bind_type[$sid])){
            if(!empty($bind_type[$sid][$cid])){
                return $bind_type[$sid][$cid];
            }
            return false;
        }else{
            return false;
        }
    }


    private function collect_check($param_data,$data){
        if($param_data['type']=='novel'){
            if($local_db=Db::name('novel')->field('id')->where('reurl',$param_data['sid'].'_'.$data['id'])->find()){
                $chapter=$this->get_chapter_collect($local_db);
                if($chapter){
                    if(count($chapter['chapter'])==$data['chapter_count']){
                        return ['state'=>false];
                    }else{
                        return ['state'=>'update','id'=>$local_db['id'],'chapter'=>$chapter];
                    }
                }else{
                    return ['state'=>'add'];
                }
            }else{
                if($local_db=Db::name('novel')->field('id,author')->where('title',$data['title'])->find()){
                    if(empty($local_db['author']) || ($local_db['author'] == $data['author'])){
                        $chapter=$this->get_chapter_collect($local_db);
                        if($chapter){
                            return ['state'=>'update','id'=>$local_db['id'],'chapter'=>$chapter];
                        }else{
                            return ['state'=>'add'];
                        }
                    }else{
                        return ['state'=>'add'];
                    }
                }
                return ['state'=>'add'];
            }
        }
    }

    private function get_chapter_collect($local_db){
        $chapter=Db::name('novel_chapter')->field('id,chapter')->where(['novel_id'=>$local_db['id']])->find();
        if($chapter['chapter']){
            $chapter['chapter']=(new CommonApi())->decompress_chapter($chapter['chapter']);
            $chapter['chapter']=json_decode($chapter['chapter'],true);
            return $chapter;
        }
        return false;
    }
    
    private function change_field_data($data,$type,$sid,$data_check){
        $change_data=[];
        if($data_check['state']===false){
            return false;
        }elseif($data_check['state']==='update'){
            $data_field=explode(',',Config::get('web.union_collect_update_'.$type));
        }else{
            $data_field=json_decode(Config::get('web.union_collect_field'),true);
            $data_field=$data_field[$type];
        }
        foreach ($data_field as $value) {
            if($value=='pic'){
                if(Config::get('web.union_collect_pic_save')){
                    $change_data[$value]=Gather::down_img($data[$value],$type);
                }else{
                    $change_data[$value]=$data[$value];
                }
            }else{
                $change_data[$value]=$data[$value];
            }
        }
        $change_data['reurl']=$sid.'_'.$data['id'];
        $change_data['update_time']=$data['update_time'];
        if($data_check['state']==='update'){
            $change_data['id']=$data_check['id'];
        }
        return $change_data;
    }
}