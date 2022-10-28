<?php


namespace app\admin\controller;
use think\facade\Db;
use think\facade\Cache;
use think\facade\Config;
use think\facade\Env;
use org\File;

class Tool extends Base
{
    public function datadel(){
        $tool= (new \app\model\Tool());
        if($this->request->param('type')){
            switch ($this->request->param('type')) {
                case 'novel':
                    $tool->datadel_novel();
                    break;
                case 'news':
                    $tool->datadel_news();
                    break;
                case 'user':
                    $tool->datadel_user();
                    break;
            }
            // return $this->success('清空成功！');
        }else{
            $this->assign('meta_title','数据清除');
            return $this->fetch();
        }
    }

    public function datato(){
        $tool=(new \app\model\Tool());
        $this->assign('meta_title','数据转换');
        if($this->request->isPost()){
            return $this->fetch('datato_progress');
        }else{
            return $this->fetch();
        }
    }

    public function data_to_progress($page=1,$page_num=0){
        $chapter_count=Db::name('novel_chapter')->count('id');
        $limit=20;
        $id=$this->request->param('id');
        if($id){
            $chapter=Db::name('novel_chapter')->where(['id'=>$id])->value('chapter');
        }else{
            $chapter=Db::name('novel_chapter')->page($page,1)->value('chapter'); 
        }
        $chapter=(new \app\model\CommonApi())->decompress_chapter($chapter);
        $chapter=json_decode($chapter,true);
        $totals=count($chapter);
        $page_count=ceil($totals/$limit);
        if($totals>$limit){
            $start=$page_num*$limit;
            $data=array_slice($chapter,$start,$limit,true);
        }else{
            $data=$chapter;
        }
        $addons_name = Cache::remember('addons_storage',function(){
            $map = ['status'=>1,'group'=>'storage'];
            return Db::name('Addons')->where($map)->value('name');
        });
        if($addons_name){
            $addons_class = get_addon_class($addons_name);
            if(class_exists($addons_class)){
                $addon = new $addons_class();
            }
        }
        foreach ($data as $key => $value){
            if($value['auto']==0){
                $content=File::read(Env::get('runtime_path').'txt'.DIRECTORY_SEPARATOR.$value['path']);
                if($addons_name){
                    $addon->put($value['path'],$content);
                }
                if($this->request->param('del')){
                    File::unlink(Env::get('runtime_path').'txt'.DIRECTORY_SEPARATOR.$value['path']);
                }
            }
        }
        if($chapter_count<=$page){
            return $this->success('转换完成','',['complete'=>true,'chapter_count'=>$chapter_count,'page'=>$page]);
        }else{
            if($page_count<=$page_num+1){
                $page_num=0;
                return $this->success('转换进度',url('data_to_progress',['del'=>$this->request->param('del'),'id'=>$id,'page'=>$page+1,'page_num'=>$page_num]),['complete'=>false,'chapter_count'=>$chapter_count,'page'=>$page+1]);
            }else{
                return $this->success('转换进度',url('data_to_progress',['del'=>$this->request->param('del'),'id'=>$id,'page'=>$page,'page_num'=>$page_num+1]),['complete'=>false,'chapter_count'=>$chapter_count,'page'=>$page]);
            }
        }
    }

    public function duplication(){
        $tool=(new \app\model\Tool());
        $this->assign('meta_title','数据去重');
        if($this->request->param('type')){
            $this->assign('url',url('duplication_progress',['type'=>$this->request->param('type')]));
            return $this->fetch('progress');
        }else{
            return $this->fetch();
        }
    }

    public function duplication_progress($page=1){
        $limit=100;
        $type=$this->request->param('type');
        switch ($type) {
            case 'novel':
                $data_list=Db::name('novel')->field('id,title')->group('title,author')->having('count(id)>1')->order('id desc')->paginate($limit);
                break;
            case 'news':
                $data_list=Db::name('news')->field('id,title')->group('title')->having('count(id)>1')->order('id desc')->paginate($limit);
                break;
        }

        if($data_list->total()>0){
            foreach ($data_list as $key => $value) {
                model($type)->del($value['id']);
            }

            $lastPage=$data_list->lastPage();
            if($lastPage<=$page){
                return $this->success('转换完成','',['complete'=>true,'count'=>$lastPage,'page'=>$page]);
            }else{
                return $this->success('转换进度',url('duplication_progress',['page'=>$page+1,'type'=>$type]),['complete'=>false,'count'=>$lastPage,'page'=>$page+1]);
            }
        }else{
            $this->error('没有检测到重复数据');
        }
    }

    public function sitemap(){
    	$tool=(new \app\model\Tool());
        $this->assign('meta_title','sitemap生成');
        if($this->request->isPost()){
            return $this->fetch('sitemap_progress');
        }else{
            return $this->fetch();
        }
    }

    public function sitemap_progress($page=1){
    	$content='';
    	$page_num=$this->request->param('page_num');
        $page_no=$this->request->param('page_no');
        $type=$this->request->param('type');
        $filename='sitemap';
        $map = ['status'=>1];
        $novel=Db::name('novel')->field('id,update_time')->where($map)->order('update_time desc')->limit($page_num);
        if($page_no){
        	$filename.='_'.$page;
        	$data=$novel->page($page);
        	$count=Db::name('novel')->where($map)->count('id');
        	$page_count=ceil($count/$page_num);
        }else{
        	$page_count=1;
        }
        $data=$novel->select();
        foreach ($data as $k=>$v){
			if($type=='xml'){
				$content.='<url>'.PHP_EOL.'<loc>'.url("home/novel/index",["id"=>$v["id"]],true,true).'</loc>'.PHP_EOL.'<mobile:mobile type="pc,mobile" />'.PHP_EOL.'<priority>0.8</priority>'.PHP_EOL.'<lastmod>'.time_format($v["update_time"],'Y-m-d').'</lastmod>'.PHP_EOL.'<changefreq>daily</changefreq>'.PHP_EOL.'</url>';
	        }else{
	        	$content.=url("home/novel/index",["id"=>$v["id"]],true,true).PHP_EOL;
	        }
		}
        if($type=='xml'){
        	$xml='<?xml version="1.0" encoding="UTF-8"?>'.PHP_EOL.'<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" xmlns:mobile="http://www.baidu.com/schemas/sitemap-mobile/1/">'.PHP_EOL;
			$xml.=$content.PHP_EOL.'</urlset>';
			$content=$xml;
        }
        $url=$this->request->domain().'/runtime/'.'repaste/'.$filename.'.'.$type;
        $filename=Env::get('runtime_path').'repaste'.DIRECTORY_SEPARATOR.$filename.'.'.$type;
        $content=File::put($filename,$content);
        if($page_count<=$page){
            return $this->success('生成完成',url('sitemap_progress',['page_no'=>$page_no,'page'=>$page,'page_num'=>$page_num,'type'=>$type,]),['complete'=>true,'page_count'=>$page_count,'page'=>$page,'filename'=>$url]);
        }else{
            return $this->success('生成进度',url('sitemap_progress',['page_no'=>$page_no,'page'=>$page+1,'page_num'=>$page_num,'type'=>$type,]),['complete'=>false,'page_count'=>$page_count,'page'=>$page+1,'filename'=>$url]);
        }
    }

    public function sqlexecute(){
    	if($this->request->isPost()){
    		$sql=$this->request->param('sql');
    		if(!empty($sql)){
    			$sql = str_replace('{pre}',Config::get('database.prefix'),$sql);
                //查询语句返回结果集
                if(strtolower(substr($sql,0,6))=="select"){

                }
                else{
                    $return = Db::execute($sql);
                }
    		}
            return $this->success('执行完成');
        }else{
        	$this->assign('meta_title','SQL语句执行');
            return $this->fetch();
        }
    }
}