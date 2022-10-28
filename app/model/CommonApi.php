<?php
/**
 * @author zhushichuan
 * @email 469702544@qq.com
 * @time 2022/10/20
 *
 *
 */


namespace app\model;
use think\Model;
use think\facade\Db;
use think\facade\Request;
use think\facade\Cache;
use think\facade\Config;
use think\facade\Env;
use think\facade\Validate;
use org\File;
use net\Gather;

class CommonApi
{

    private $cache_data,$cache_options,$cache_name;
    private $cache_on=false;
    private $cache_set=false;
    public $api_url=false;

    public function __construct()
    {
        if(Config::get('web.data_cache')){


            if(in_array(strtolower(app('http')->getName()),['home'])
                && !in_array(strtolower(Request::controller()."/".Request::action()),['chapter/index','chapter/lists','chapter/info','novel/digg','news/digg','other/index'])){
                $this->cache_on=true;
                $this->cache_options=[
                    'path' => Env::get('runtime_path').'data'.DIRECTORY_SEPARATOR.strtolower(Request::controller()).DIRECTORY_SEPARATOR,
                    'cache_subdir' => false
                ];
                if(in_array(strtolower(Request::controller()."/".Request::action()),['novel/index','news/index'])){
                    $this->cache_name=strtolower("_".Request::controller()."_".Request::action()."_".Request::param('id'));
                }else{
                    $this->cache_name=strtolower("_".Request::controller()."_".Request::action());
                }
            
                $this->cache_data = Cache::get($this->cache_name);
            }
        }
    }

//    public function __destruct(){
//        if($this->cache_set && $this->cache_on){
//            if(in_array(strtolower(getModule().Request::controller()."/".Request::action()),['novel/index','news/index'])){
//                Cache::set($this->cache_name,$this->cache_data);
//            }else{
//                Cache::set($this->cache_name,$this->cache_data,3600*12);
//            }
//        }
//    }

    public function get_nav($category,$type,$limit,$cid,$field=true){
        $map = ['status' => 1,'pid' => $category];
        if($type!==false){
            $map['type']=$type;
        }
        $data=Db::name('category')->field($field)->where($map)->limit(intval($limit))->order('sort')->select();
        if($data){
            foreach ($data as $k=>$v){
                $meun_list[$k]=$v;
                if($v["type"]==3){
                    $meun_list[$k]["url"]=$this->nav_check_url($v["link"]);
                    $meun_list[$k]["branch"]=0;
                    $visit = strtolower("/".Request::controller()."/".Request::action());
                    if($v["link"]==$visit){
                        $meun_list[$k]["current"]=1;
                    }else{
                        $meun_list[$k]["current"]=0;
                    }
                }else{
                    $meun_list[$k]["url"]=url("home/lists/index",["id"=>$v["id"]]);
                    $meun_list[$k]["branch"]=$this->get_branch($v["id"]);
                    $meun_list[$k]["current"]=$this->has_current($v["id"],$cid);
                }
                if(isset($meun_list[$k]["icon"])){
                    $meun_list[$k]["icon"]=$this->check_pic($meun_list[$k]["icon"]);
                }
            }
            if($category===0 && $type===false){
                array_unshift($meun_list,['id'=>0,'title'=>'首页','url'=>url('home/index/index'),'branch'=>0,'current'=>$this->has_current(0,$cid)]);
            }
            return $meun_list;
        }
    }

    public function get_slider($limit,$type=false){
        $map = ['status' => 1];
        if($type){
            $map['type'] = $type;
        }else{
            $map['type'] = Request::isMobile()?'1':'0';
        }
        $data=Db::name('slider')->where($map)->limit(intval($limit))->order('sort')->select();
        if($data){
            foreach ($data as $k=>$v){
                $slider_list[$k]=['id'=>$v['id'],'title'=>$v['title'],'pic'=>$this->check_pic($v['picpath']),'url'=>$v['link']];
            }
            return $slider_list;
        }
    }

    public function get_news($category, $order, $limit, $pos, $time, $page, $id=null){
        $cache_name=__FUNCTION__."_".implode('_',func_get_args());
        if(isset($this->cache_data[$cache_name]) && !$page){
            return $this->cache_data[$cache_name];
        }
        $category=$this->get_id($category);
        $map = $this->list_map($category,$pos);
        if($id){
            $map[] = ['id','in',$id];
        }
        $limit = intval($limit);
        $news=Db::name('news')->where($map)->whereTime('update_time',$time)->order($order);
        if($page){
            $simple = Request::isMobile()?true:false;
            $data=$news->paginate($limit,$simple);
        }else{
            $data=$news->limit($limit)->select();
        }
        if($data){
            foreach ($data as $k=>$v){
                $data[$k]=$this->data_change($v,'news');
            }
            if(!$page){
                $this->cache_set=true;
                $this->cache_data[$cache_name]=$data;
            }
            return $data;
        }
    }

    public function get_novel($category, $order, $limit, $pos, $time, $newbook, $over, $author, $page, $id=null){
        $cache_name=__FUNCTION__."_".implode('_',func_get_args());
        if(isset($this->cache_data[$cache_name]) && !$page){
            return $this->cache_data[$cache_name];
        }
        $category=$this->get_id($category);
        if($page){
            $map = $this->list_page_map($category);
            $update=Request::param('update',NULL);
            if(Request::param('order')){
                $order=Request::param('order');
                if(strstr($order,'+')){
                    $order=str_replace('+',' ',$order);
                }
            }
            if(isset($update)){
                $filter_update=array_keys(Config::get('web.filter_update'));
                $time=$filter_update[$update];
            }
        }else{
            $map = $this->list_map($category,$pos);
        }
        if($newbook){
            $map[] = ['serialize','=',0];
            $map[] = ['create_time','>=',strtotime("-3 month")];
        }
        if($over){
            $map[] = ['serialize','=',1];
        }
        if($author){
            $map[] = ['author','=',$author];
        }
        if($id){
            $map[] = ['id','in',$id];
        }
        $map[] = ['sole','>',1];
        $novel=Db::name('novel')->where($map)->order($order);
        if($time){
            $novel=$novel->whereTime('update_time',$time);
        }
        $limit = intval($limit);
        if($page){
            $simple = Request::isMobile()?true:false;
            $data=$novel->paginate($limit,$simple);
        }else{
            $data=$novel->limit($limit)->select();
        }
        if($data){
            foreach ($data as $k=>$v){
                $data[$k]=$this->data_change($v,'novel');
            }
            if(!$page){
                $this->cache_set=true;
                $this->cache_data[$cache_name]=$data;
            }
            return $data;
        }
    }

    public function get_chapter_list($nid, $order, $limit, $page){
        $cache_name=__FUNCTION__."_".implode('_',func_get_args());
        if($page){
            $cache_name.='_'.Request::param('chapter_page',1);
        }
        if(isset($this->cache_data[$cache_name])){
            return $this->cache_data[$cache_name];
        }
        $map[] = ['status','=',1];
        $map[] = ['novel_id','=',$nid];
        $chapter_data=Db::name('novel_chapter')->field('id,chapter')->where($map)->find();
        if($chapter_data){
            $chapter_data['chapter']=$this->decompress_chapter($chapter_data['chapter']);
            $chapter_data['chapter']=json_decode($chapter_data['chapter'],true);
        }else{
            return false;
        }
        if(strpos(strtolower($order),'desc') !== false){
            $data=array_reverse($chapter_data['chapter']);
        }else{
            $data=$chapter_data['chapter'];
        }
        if($data){
            if($page){
                $class='\\think\\paginator\\driver\\Bootstrap';
                $page_num  = call_user_func([$class,'getCurrentPage'],'chapter_page');
                $page_num = $page_num < 1 ? 1 : $page_num;
                $config=['var_page'=>'chapter_page','path'=>call_user_func([$class, 'getCurrentPath'])];
                $totals=count($data);
                $start=($page_num-1)*$limit;
                if(Request::isMobile()){
                    if($totals>$limit){
                        $data=array_slice($data,$start,$limit+1,true);
                    }
                    $simple = true;
                    $totals = null;
                }else{
                    if($totals>$limit){
                        $data=array_slice($data,$start,$limit,true);
                    }
                    $simple = false;
                }
                $data=$class::make($data, $limit, $page_num, $totals, $simple, $config);
            }else{
                if($limit){
                    $data=array_slice($data,0,$limit,true);
                }
            }
            foreach ($data as $k=>$v){
                if($v['issued']==1){
                    $data[$k]=$this->chapter_change($v,$chapter_data['id'],$k);
                }else{
                    unset($data[$k]);
                }
            }
            $this->cache_set=true;
            $this->cache_data[$cache_name]=$data;
            return $data;
        }
    }

    public function get_chapter($id,$key){
        $map[] = ['status','=',1];
        $map[] = ['id','=',$id];
        $chapter_data=Db::name('novel_chapter')->field('id,chapter,novel_id,collect_id')->where($map)->find();
        if($chapter_data){
            $chapter_data['chapter']=$this->decompress_chapter($chapter_data['chapter']);
            $chapter_data['chapter']=json_decode($chapter_data['chapter'],true);
            $chapter=isset($chapter_data['chapter'][$key])?$chapter_data['chapter'][$key]:'';
            if($chapter){
                $chapter['id']=$key;
                $chapter['novel_id']=$chapter_data['novel_id'];
                $chapter['source_id']=$chapter_data['id'];
                $chapter_data_keys = array_keys($chapter_data['chapter']);
                $chapter_data_keys_num = array_search($key,$chapter_data_keys);
                if($chapter_data_keys_num<=0){
                    $chapter['prev']=null;
                }else{
                    $chapter_data_prev_keys=$chapter_data_keys[$chapter_data_keys_num-1];
                    $chapter['prev']=$chapter_data['chapter'][$chapter_data_prev_keys];
                    $chapter['prev']['id']=$chapter_data_prev_keys;
                }
                if($chapter_data_keys_num>=(count($chapter_data_keys)-1)){
                    $chapter['next']=null;
                }else{
                    $chapter_data_next_keys=$chapter_data_keys[$chapter_data_keys_num+1];
                    $chapter['next']=$chapter_data['chapter'][$chapter_data_next_keys];
                    $chapter['next']['id']=$chapter_data_next_keys;
                }
                $chapter['time']=time_format($chapter['update_time']);
                $chapter['prev']['url']=$chapter['prev']?url('home/chapter/index',['id'=>$id,'key'=>$chapter_data_prev_keys]):'javascript:void(0);';
                $chapter['next']['url']=$chapter['next']?url('home/chapter/index',['id'=>$id,'key'=>$chapter_data_next_keys]):'javascript:void(0);';
                if($chapter['auto']==1){
                    $getchapter=(new CommonUnionChapter())->get_chapter($chapter['reurl']);
                    if(!empty($getchapter['content'])){
                        $word=mb_strlen($getchapter['content']);
                        if(Config::get('web.chapter_txt')){
                            $this->set_chapter_content($chapter['path'],$getchapter['content']);
                            $chapter_data['chapter'][$key]['auto']=0;
                            $chapter_data['chapter'][$key]['word']=$word;
                            $chapter_data['chapter'][$key]['intro']=$getchapter['intro'];
                            $chapter_data['chapter']=json_encode($chapter_data['chapter']);
                            $chapter_data['chapter']=$this->compress_chapter($chapter_data['chapter']);
                            Db::name('novel_chapter')->update($chapter_data);
                        }
                        $chapter['content']=$getchapter['content'];
                        $chapter['intro']=$getchapter['intro'];
                        $chapter['word']=$word;
                    }else{
                        $chapter['content']='错误的数据！！！';
                    }
                }elseif($chapter['auto']==2){
                    $getchapter=Gather::get_chapter_content($chapter_data['collect_id'],$chapter['reurl']);
                    if($getchapter!==false){
                        $word=mb_strlen($getchapter['chapter_content']);
                        if($word>500 && Config::get('web.chapter_txt')){
                            $this->set_chapter_content($chapter['path'],$getchapter['chapter_content']);
                            $chapter_data['chapter'][$key]['auto']=0;
                            $chapter_data['chapter'][$key]['word']=$word;
                            if(!empty($getchapter['chapter_title'])){
                                $chapter_data['chapter'][$key]['title']=$getchapter['chapter_title'];
                            }
                            $chapter_data['chapter']=json_encode($chapter_data['chapter']);
                            $chapter_data['chapter']=$this->compress_chapter($chapter_data['chapter']);
                            Db::name('novel_chapter')->update($chapter_data);
                            $novel_data=['word'=>Db::raw('word+'.($word-$chapter['word']))];
                            Db::name('novel')->where(['id'=>$chapter['novel_id']])->update($novel_data);
                        }
                        $chapter['word']=$word;
                        $chapter['title']=empty($getchapter['chapter_title'])?'':$getchapter['chapter_title'];
                        $chapter['content']=empty($getchapter['chapter_content'])?'章节内容转码失败！':$getchapter['chapter_content'];
                    }else{
                        $chapter['content']='章节内容转码失败！';
                    }
                }else{
                    $chapter['content']=$this->get_chapter_content($chapter['path']);
                }
                $chapter['content']=$this->change_chapter_content($chapter['content']);
                return $chapter;
            }
        }
    }

    public function get_link($limit){
        $map = ['status' => 1];
        $link=Db::name('link')->where($map)->field('id,title,url')->limit(intval($limit))->select();
        return $link;
    }

    public function get_filter($name,$type,$cid){
        $name = ($name=="type")?"id":$name;
        $map=Request::param();
        unset($map[$name],$map['page']);
        $filter_name=Request::param($name);
        if($name=="id"){
            $id=$this->siblingsId($cid);
            if($id!==0){
                $map[$name]=$id;
            }
            $filter[]=["name"=>"id","value"=>$cid?$id:null,"title"=>'全部',"url"=>url('home/lists/lists',$map),'current'=>($id==Request::param('id'))?1:0];
            $where = ['status' => 1,'pid' => $id];
            if($type!==false){
                $where['type'] = $type;
            }
            $data=Db::name('category')->where($where)->field('id,title,type,link')->order('sort asc')->select();
            foreach ($data as $key => $value){
                if($value["type"]==3){
                    $filter[]=["name"=>"id","value"=>$value["id"],"title"=>$value["title"],"url"=>$this->nav_check_url($value["link"]),'current'=>$this->filter_has_current($value['id'],$filter_name)];
                }else{
                    $map[$name]=$value['id'];
                    $filter[]=["name"=>"id","value"=>$value["id"],"title"=>$value["title"],"url"=>url('home/lists/lists',$map),'current'=>$this->filter_has_current($value['id'],$filter_name)];
                }
            }
        }else{
            $filter[]=["name"=>"{$name}","value"=>null,"title"=>'全部',"url"=>url('home/lists/lists',$map),'current'=>isset($filter_name)?0:1];
            $i=0;
            foreach (Config::get('web.filter_'.$name) as $key => $value) {
                $map[$name]=$i;
                $filter[]=["name"=>"{$name}","value"=>$i,"title"=>$value,"url"=>url('home/lists/lists',$map),'current'=>($filter_name==$i && isset($filter_name))?1:0];
                $i++;
            }
        }
        return $filter;
    }

    //面包屑导航
    public function get_crumbs($cid=0,$id=0){
        $type=strtolower(Request::controller());
        if(!in_array($type,['novel','news'])){
            $type='novel';
        }
        $crumbs[]=['title'=>'首页','url'=>url('home/index/index')];
        if($cid){
            $crumbs=array_merge($crumbs,$this->get_parent($cid));
        }
        if($keyword=Request::param('keyword')){
            $crumbs[]=['title'=>$keyword,'url'=>url('home/search/index',['keyword'=>$keyword])];
        }
        if($id){
            $data=Db::name($type)->where('id',$id)->field("id,title")->find();
            if($data){
                $crumbs[]=['title'=>$data['title'],'url'=>url('home/'.$type.'/index',['id'=>$data['id']])];
            }
        }
        return $crumbs;
    }

    // oauth登录
    public function get_oauth_login($mold){
        $config = [];
        $map=[];
        $map[] = ['group','=','oauth'];
        $map[] = ['status','=',1];
        $map[] = ['','exp',Db::raw('find_in_set("'.$mold.'",`mold`)')];
        $login  =   Db::name('Addons')->where($map)->field('title,name,config')->order('sort')->select();
        if($login){
            foreach ($login as $key => $value) {
                $config=json_decode($value['config'],true);
                $data[$key]['title']=$value['title'];
                $data[$key]['pic']=$config['login_img'];
                $data[$key]['url']=url('\\addons\\'.$value['name'].'\\'.$value['name'].'@login');
            }
            return $data;
        }
    }

    //ad代码
    public function get_ad($id){
        $ad  =   Db::name('ad')->where(['id'=>$id])->value('value');
        if($ad){
            return $ad;
        }
    }

    public function get_branch($category){
        $map = ['status' => 1,'pid'=>$category];
        $Count=Db::name("category")->where($map)->Count();
        if($Count>0){
            return 1;
        }else{
            return 0;
        }
    }

    private function has_current($id,$cid){
        $visit = strtolower("/".Request::controller()."/".Request::action());
        if($id==0 && empty($cid) && $visit=='home/index/index'){
            return 1;
        }
        if($id==$cid && $id!=0 && $visit=='home/lists/index'){
            return 1;
        }
        if($id==$this->get_category($cid,'pid') && $cid  && $id!=0 && $visit=='home/lists/index'){
            return 1;
        }
        return 0;
    }

    private function filter_has_current($id,$cid){
        if($id==$cid && $id!=0){
            return 1;
        }
        if($id==$this->get_category($cid,'pid') && $cid  && $id!=0){
            return 1;
        }
        return 0;
    }

    private function get_id($id){
        if($id){
            $id = explode(',',$id);
            $map = ['status' => 1,'pid' => $id];
            $info = Db::name('category')->field("id")->where($map)->order('sort')->select();
            $ids = [];
            if($info){
                foreach ($info as $key=>$val){
                    $ids[]=$val["id"];
                }
                return $ids;
            }else{
                return $id;
            }
        }
    }

    private function siblingsId($id){
        $pid=$this->get_category($id,'pid');
        if($pid==0){
            return $id;
        }
        return $pid;
    }

    private function list_map($category,$pos){
        $map[] = ['status','=',1];
        if(!empty($category)){
            $map[] = is_array($category)?['category','in',$category]:['category','=',$category];
        }
        if(is_numeric($pos)){
            $map[] = ['position','exp',Db::raw('& '.$pos.' = '.$pos)];
        }
        return $map;
    }

    private function list_page_map($category){
        $map[] = ['status','=',1];
        $serialize=Request::param('serialize',NULL);
        $size=Request::param('size',NULL);
        $keyword=Request::param('keyword',NULL);
        if(isset($keyword)){
            $map[]=['title|author','like','%'.trim($keyword).'%'];
            return $map;
        }
        if(!empty($category)){
            $map[] = is_array($category)?['category','in',$category]:['category','=',$category];
        }
        if(isset($serialize)){
            $filter_serialize=array_keys(Config::get('web.filter_serialize'));
            $map[]=['serialize','=',$filter_serialize[$serialize]];
        }
        if(isset($size)){
            $filter_size=array_keys(Config::get('web.filter_size'));
            $where_size=explode(' ',$filter_size[$size]);
            $map[]=['word',$where_size[0],$where_size[1]];
        }
        return $map;
    }

    private function get_parent($id,&$list=[]){
        $data=$this->get_category($id);
        if($data){
            array_unshift($list,['title'=>$data['title'],'url'=>url('home/lists/index',['id'=>$data['id']])]);
            $this->get_parent($data['pid'],$list);
        }
        return $list;
    }

    public function get_category($cid,$field=''){
        if(empty($cid)){
            return false;
        }
        $data=Cache::remember('category',function(){
            return Db::name("category")->where('status',1)->column('*','id');
        });
        if($field){
            return isset($data[$cid][$field])?$data[$cid][$field]:false;
        }else{
            return isset($data[$cid])?$data[$cid]:false;
        }
    }

    public function data_change($data,$type){
        $data["cid"]=$data["category"];
        $data["ctitle"]=$this->get_category($data["category"],'title');
        $data["curl"]=url('home/lists/index',["id"=>$data["category"]]);
        $data["time"]=$data["update_time"];
        $data['pic']=$this->check_pic($data['pic']);
        switch ($type) {
            case 'news':
                preg_match_all('/<img.*?src="(.*?)".*?>/is',$data["content"],$matches);
                $data["content_pic"]=$matches[1];
                break;
            case 'novel':
                $data['tag_array']=explode(',', $data['tag']);
                $data["word_million"]=number_format($data["word"]/10000,2);
                $data["serialize_text"]=($data["serialize"]==1)?"已完结":"连载中";
                $data["author_url"]=url('home/search/index',['keyword'=>$data["author"]]);
                $chapter_data=Db::name('novel_chapter')->field('id,chapter,updated')->where(['novel_id'=>$data['id'],'status'=>1])->find();
                if($chapter_data){
                    if($chapter_data['updated']){
                        $chapter_data_last=json_decode($chapter_data['updated'],true);
                        $data['chapter_id']=$chapter_data_last['id'];
                        $data["chapter_count"]=$chapter_data_last['count'];
                    }else{
                        $chapter_data['chapter']=$this->decompress_chapter($chapter_data['chapter']);
                        $chapter_data['chapter']=json_decode($chapter_data['chapter'],true);
                        $chapter_data_last=end($chapter_data['chapter']);
                        $data['chapter_id']=key($chapter_data['chapter']);
                        $data["chapter_count"]=count($chapter_data['chapter']);
                    }
                    $data['source_id']=$chapter_data['id'];
                    $data['chapter_title']=$chapter_data_last['title'];
                    $data['chapter_content']='';
                    $data['chapter_time'] = $chapter_data_last['update_time'];
                    $data["chapter_url"]=url('home/chapter/index',['id'=>$chapter_data['id'],'key'=>$data['chapter_id']]);
                    $data["chapter_url_id"]=$chapter_data['id'];
                    $data["chapter_url_key"] = $data['chapter_id'];
                }else{
                    $data['source_id'] = "";
                    $data['chapter_id'] = "";
                    $data['chapter_title'] = "";
                    $data['chapter_content'] = "";
                    $data['chapter_time'] = "";
                    $data['chapter_count'] = "";
                    $data['chapter_url'] = "";
                    $data["chapter_url_id"] = "";
                    $data["chapter_url_key"] = "";
                }
                break;
            default:
                break;
        }
        $data["url"]=url("home/".$type."/index",["id"=>$data["id"]]);
        $data["digg"]=[
            "up"=>$data["up"],
            "up_js"=>"onclick=digg('".$data["id"]."','up','".$type."')",
            "down"=>$data["down"],
            "down_js"=>"onclick=digg('".$data["id"]."','down','".$type."')"
        ];
        unset($data["category"],$data["status"],$data["up"],$data["down"]);
        return $data;
    }

    public function chapter_change($data,$id,$key){
        $data["chapter_id"]=$id;
        $data['id']=$key;
        $data['new']=(time()-$data['update_time']<(3*24*3600))?1:0;
        $data['time']=$data['update_time'];
        $data['url']=url('home/chapter/index',['id'=>$id,'key'=>$key]);
        $data['new_'] = [
            'id'=>$id,
            'key'=>$key
        ];
        return $data;
    }

    public function get_tpl($id,$tpl_type){
        if(empty($id)){
            return [];
        }
        $tpl=$this->get_category($id);
        if($tpl[$tpl_type]){
            return $tpl[$tpl_type];
        }
        if($tpl['pid']==0){
            $this->error='模板未设置！';
        }else{
            return $this->get_tpl($tpl['pid'],$tpl_type);
        }
    }

    public function novel_detail($id){
        $cache_name=__FUNCTION__."_".implode('_',func_get_args());
        if(isset($this->cache_data[$cache_name])){
            return $this->cache_data[$cache_name];
        }
        $info = Db::name("novel")->where(['id'=>$id,'status'=>1])->find();
        if(!$info){
            $this->error = '小说被禁用或已删除！';
            return false;
        }
        $this->cache_set=true;
        $this->cache_data[$cache_name]=$this->data_change($info,'novel');
        return $this->cache_data[$cache_name];
    }

    public function novel_reader_url($id){
        $chapter=Db::name('novel_chapter')->field('id,chapter')->where(['novel_id'=>$id,'status'=>1])->find();
        if($chapter){
            $chapter['chapter']=$this->decompress_chapter($chapter['chapter']);
            $chapter['chapter']=json_decode($chapter['chapter'],true);
            $chapter_key=key($chapter['chapter']);
            if($this->api_url){
                return ['id'=>$chapter['id'],'key'=>$chapter_key];
            }
            return url('home/chapter/index',['id'=>$chapter['id'],'key'=>$chapter_key]);
        }
    }

    public function news_detail($id){
        $info = Db::name("news")->where(['id'=>$id,'status'=>1])->find();
        if(!$info){
            $this->error = '文章被禁用或已删除！';
            return false;
        }
        return $this->data_change($info,'news');
    }

    public function hits($id,$type){
        $hits_time=Db::name($type)->where(['id'=>$id])->value('hits_time');
        if(date('d',$hits_time)==date('d',time())){
            $data['hits_day']=Db::raw('hits_day+1');
        }else{
            $data['hits_day']=1;
        }
        if(date('W',$hits_time)==date('W',time())){
            $data['hits_week']=Db::raw('hits_week+1');
        }else{
            $data['hits_week']=1;
        }
        if(date('m',$hits_time)==date('m',time())){
            $data['hits_month']=Db::raw('hits_month+1');
        }else{
            $data['hits_month']=1;
        }
        $data['hits']=Db::raw('hits+1');
        $data['hits_time']=time();
        Db::name($type)->where('id',$id)->update($data);
        if($type=='novel'){
            $addons_name = Cache::remember('addons_author',function(){
                $map = ['status'=>1,'group'=>'author'];
                return Db::name('Addons')->where($map)->value('name');
            });
            if($addons_name){
                $addons_class = get_addon_class($addons_name);
                if(class_exists($addons_class)){
                    $addon = new $addons_class();
                    $addon->setinc($id,'read');
                }
            }
        }
    }

    public function digg($id,$type,$digg){
        if(!cookie('digg_'.$type.$digg.$id)){
            cookie('digg_'.$type.$digg.$id,true);
            Db::name($type)->where('id',$id)->setInc($digg);
            rm_cache($id,$type,false);
            return true;
        }else{
            return false;
        }
    }

    public function compress_chapter($content){
        if(Config::get('web.data_save_compress')){
            ini_set("memory_limit","-1");
            $content=base64_encode(gzcompress($content,Config::get('web.data_save_compress_level')));
        }
        return $content;
    }

    public function decompress_chapter($content){
        if(Config::get('web.data_save_compress')){
            ini_set("memory_limit","-1");
            $content=@gzuncompress(base64_decode($content));
        }
        return $content;
    }

    public function get_chapter_content($path){
        $addons_name = Cache::remember('addons_storage',function(){
            $map = ['status'=>1,'group'=>'storage'];
            return Db::name('Addons')->where($map)->value('name');
        });
        if($addons_name){
            $addons_class = get_addon_class($addons_name);
            if(class_exists($addons_class)){
                $addon = new $addons_class();
                $content = $addon->read($path);
            }
        }else{
            $content=File::read(Env::get('runtime_path').'txt'.DIRECTORY_SEPARATOR.$path);
        }
        $content=$this->decompress_chapter($content);
        return $content;
    }

    public function set_chapter_content($path,$content){
        $content=$this->compress_chapter($content);
        $addons_name = Cache::remember('addons_storage',function(){
            $map = ['status'=>1,'group'=>'storage'];
            return Db::name('Addons')->where($map)->value('name');
        });
        if($addons_name){
            $addons_class = get_addon_class($addons_name);
            if(class_exists($addons_class)){
                $addon = new $addons_class();
                $addon->put($path,$content);
            }
        }else{
            File::put(Env::get('runtime_path').'txt'.DIRECTORY_SEPARATOR.$path,$content);
        }
        return $path;
    }

    private function change_chapter_content($content){
        $content = str_ireplace(['<br>','<br\/>','<br />','　','&nbsp;','<p>','</p>','<p/>'], ["\r\n","\r\n","\r\n","","","","\r\n","\r\n"], $content);
        $content_array = preg_split('/[\r\n]+/', trim($content,"\r\n"));
        if(is_array($content_array)){
            if(!$this->api_url){
                $map = ['status'=>1,'group'=>'chapter_content'];
                $addons_name = Db::name('Addons')->where($map)->value('name');
                $addons_class = get_addon_class($addons_name);
                if(class_exists($addons_class)){
                    $addon = new $addons_class();
                    $content_array=$addon->run($content_array);
                }
            }
            $content = '';
            foreach ($content_array as $value) {
                if($value){
                    $content.='<p>　　'.$value.'</p>';
                }
            }
        }
        return $content;
    }

    private function nav_check_url($url){
        $validate = Validate::checkRule($url,'url');
        if(!$validate){
            return url($url);
        }else{
            return $url;
        }
    }

    private function check_pic($pic){
        if(empty($pic)){
            $pic="/public/home/images/default_cover.png";
        }
        if($this->api_url){
            $validate = Validate::checkRule($pic,'url');
            if(!$validate){
                return Request::domain().$pic;
            }else{
                return $pic;
            }
        }
        return $pic;
    }
}