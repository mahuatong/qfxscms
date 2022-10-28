<?php


namespace app\home\controller;

use think\facade\Cookie;

class Lists extends Base{

	public function index(){
		Cookie::set('__forward__',$this->request->url());
		$cid=$this->request->param('id');
		$info=(new \app\model\CommonApi())->get_category($cid);
		if(empty($info)){
			$this->error('未找到该栏目！','home/index/index');
		}
		$this->assign('list_title',empty($info["meta_title"]) ? config("web.meta_title") : $info["meta_title"]);
		$this->assign('list_keywords',empty($info["meta_keywords"]) ? config("web.meta_keyword") : $info["meta_keywords"]);
		$this->assign('list_description',empty($info["meta_description"]) ? config("web.meta_description") : $info["meta_description"]);
		if($info['type']===2){
			$tpl=(new \app\model\CommonApi())->get_tpl($info['id'],'template_detail');
		}else{
			$tpl=(new \app\model\CommonApi())->get_tpl($info['id'],'template_index');
		}
		if(!$tpl){
        	$this->error();
		}
		$this->assign(['cid'=>$info['id'],'pid'=>$info['pid'],'title'=>$info['title'],'icon'=>$info['icon'],'pos'=>2]);
        $tpl = '/'.str_replace('.html','',$tpl);
        return $this->fetch($tpl);
	}

	public function lists(){
		Cookie::set('__forward__',$this->request->url());
		$cid=$this->request->param('id');
		$size=$this->request->param('size');
		$serialize=$this->request->param('serialize');
		$update=$this->request->param('update');
		$tag=$this->request->param('tag');
		$info=(new \app\model\CommonApi())->get_category($cid);

        $this->assign('list_title',empty($info["meta_title"]) ? config("web.meta_title") : $info["meta_title"]);
        $this->assign('list_keywords',empty($info["meta_keywords"]) ? config("web.meta_keyword") : $info["meta_keywords"]);
        $this->assign('list_description',empty($info["meta_description"]) ? config("web.meta_description") : $info["meta_description"]);


		$tpl=(new \app\model\CommonApi())->get_tpl($cid,'template_filter');
		if(!$tpl){
			$tpl='lists.html';
		}
		$this->assign(['cid'=>$info['id'],'pid'=>$info['pid'],'title'=>$info['title'],'icon'=>$info['icon'],'size'=>$size,'serialize'=>$serialize,'update'=>$update,'tag'=>$tag,'pos'=>1]);
        $tpl = '/'.str_replace('.html','',$tpl);

        return $this->fetch($tpl);
	}
}