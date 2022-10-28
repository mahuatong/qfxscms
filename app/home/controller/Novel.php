<?php


namespace app\home\controller;

use think\facade\Cookie;

class Novel extends Base
{
    public function index()
    {
        Cookie::set('__forward__',$this->request->url());
        $id=$this->request->param('id');
        $info=(new \app\model\CommonApi())->novel_detail($id);
        if(!$info){
            $this->error(empty($error) ? '未找到该小说！' : $error,url('Home/Index/index'));
        }
        if(empty($info['template'])){
            $tpl=(new \app\model\CommonApi())->get_tpl($info['cid'],'template_detail');
            if(empty($tpl)){
                $this->error();
            }
        }else{
            $tpl=$info['template'];
        }
        if(!empty($info['link'])){
        	$this->redirect($info['link'],302);
        }
        (new \app\model\CommonApi())->hits($id,'novel');
//        $is_bookshelf=model('user/bookshelf')->check($info['id']);
        $this->assign('pos',1);
        $this->assign('type','novel');
        $this->assign($info,$info);
        $this->assign('reader_url',(new \app\model\CommonApi())->novel_reader_url($info['id']));
        $this->assign('is_bookshelf',0);
        $this->assign('add_bookshelf','onclick=add_bookshelf()');
        $tpl = str_replace('.html','',$tpl);
        return $this->fetch('/'.$tpl);
    }

    public function digg($id,$digg){
        $return=(new \app\model\CommonApi())->digg($id,'novel',$digg);
        if($return){
           return $this->success($digg.'+1');
        }else{
           $this->error('请不要重复操作！');
        }
    }
}
