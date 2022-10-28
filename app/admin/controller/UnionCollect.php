<?php


namespace app\admin\controller;

set_time_limit(0);
ini_set('memory_limit', '-1');

use think\facade\Db;
use think\facade\Cache;
use think\paginator\driver\Bootstrap;
use org\Oauth;
use net\Gather;

class UnionCollect extends Base
{

    protected $middleware = [
        'app\admin\middleware\UnionCollect' => ['only' => ['collect']]
    ];

    protected $beforeActionList = ['checkAuth'];

    protected function checkAuth(){
        $auth = new Oauth();
        if(!$auth->checkAuth()){
            return false;
        }
    }

    public function slist(){
        $UnionCollect= (new \app\model\UnionCollect());
        $list=$UnionCollect->slist();
        if($list === false){
            $this->error();
        }
        $this->assign('list',$list);
        $this->assign('meta_title','数据联盟');
        return $this->fetch();

    }

    public function add(){
        $UnionCollect=(new \app\model\UnionCollect());
        if($this->request->isPost()){
            $data=$this->request->post();
            $res = $UnionCollect->edit($data);
            if($res  !== false){
                return $this->success('联盟数据交成功,等待审核！','');
            } else {
                $this->error();
            }
        }else{
            $this->assign('domain',$this->request->domain());
            $this->assign('meta_title', '发布数据到联盟');
            return $this->fetch('edit');
        }
    }

    public function edit($id){
        $UnionCollect=(new \app\model\UnionCollect());
        if($this->request->isPost()){
            $data=$this->request->post();
            $res = $UnionCollect->edit($data);
            if($res  !== false){
                return $this->success('联盟数据修改成功！',url('index'));
            } else {
                $this->error();
            }
        }else{
            $info=$UnionCollect->info($id);
            $this->assign('info',$info);
            $this->assign('domain',$this->request->domain());
            $this->assign('meta_title','修改联盟数据');
            return $this->fetch();
        }
    }

    public function del($id){
        $UnionCollect=(new \app\model\UnionCollect());
        $res=$UnionCollect->del($id);
        if($res  !== false){
            return $this->success('取消联盟数据！','');
        } else {
            $this->error();
        }
    }

    public function index(){
        $UnionCollect=(new \app\model\UnionCollect());
        $res=$UnionCollect->server($this->request->param('type',0));
        if($res){
            $this->assign('list',$res);
        }
        $this->assign('meta_title', '资源列表');
        return $this->fetch();
    }

    public function lists(){
        $UnionCollect=(new \app\model\UnionCollect());
        $res=$UnionCollect->lists(urldecode($this->request->param('url')),$this->request->param('type'));
        if($res){
            $paginator = new Bootstrap($res['list']['data'],$res['list']['per_page'],$res['list']['current_page'],$res['list']['total'],false,['path'=>url('',['sid'=>$this->request->param('sid'),'type'=>$this->request->param('type'),'url'=>$this->request->param('url')])]);
            $collect_log=Cache::get('union_collect_log');
            if(!empty($collect_log[$this->request->param('sid')])){
                $this->assign('param_data',$collect_log[$this->request->param('sid')]);
            }
            $this->assign('bind_type',Cache::get('bind_type'));
            $this->assign('list_category',$res['category']);
            $this->assign('list',$res['list']['data']);
            $this->assign('page',$paginator->render());
            $this->assign('category', get_tree(0));
            $this->assign('meta_title', '数据列表');
            return $this->fetch();
        }else{
            $this->error();
        }
    }

    public function bind_type(){
        $UnionCollect=(new \app\model\UnionCollect());
        if($this->request->isPost()){
            $data=$this->request->post();
            $UnionCollect->bind_type($data);
            return $this->success('绑定成功！','');
        }
    }

    public function collect(){
        $current=0;
        Cache::clear('union_collect');
        $param_data=$this->request->param();
        $data=$this->set_collect_queue($param_data);
        $this->assign('param_data',$param_data);
        if($data['current_page']!=1){
            $current=$data['current_page']*$data['per_page'];
        }
        $this->assign(['total'=>$data['total'],'current'=>$current]);
        $this->assign('meta_title', '采集进度');
        return $this->fetch('progress');
    }

    public function collect_thread(){
        $UnionCollect=(new \app\model\UnionCollect());
        $param_data=$this->request->param();
        $data=$this->get_collect_queue($param_data);
        if($data===false){
            // 采集完成
            $this->union_collect_log_rm($param_data['sid']);
            return $this->success('采集完成！','',['state'=>'finish']);
        }
        $return=$UnionCollect->collect_save($data,$param_data);
        $this->rm_collect_queue($data['id']);
        if(isset($return['error'])){
            return $this->error($return['msg'],$return['url'],$return['data']);
        }else{
            return $this->success('成功！','',$return);
        }
        
    }

    public function crontab_collect($vars,$page=1){
        $UnionCollect=(new \app\model\UnionCollect());
        $data=$UnionCollect->collect_list(urldecode($vars['url']),$vars['type'],['time'=>'today','page'=>$page]);
        if($data){
            foreach ($data['data'] as $key => $value) {
                $UnionCollect->collect_save($value,$vars);
            }
            if($data['current_page']<$data['last_page']){
                $this->crontab_collect($vars,intval($data['current_page'])+1);
            }
        }
    }

    public function crontab_config(){
        $Crontab=(new \app\model\CrontabModel());
        if($this->request->isPost()){
            $data=$this->request->post();
            if($data['interval']=='del'){
            	$res = $Crontab->del($data['id']);
            }else{
            	$data['type']=1;
	            $data['relation_id']=$data['content']['vars']['sid'];
	            $data['class_name']=$data['content']['url'];
	            $data['run_time']=time()+$data['interval'];
	            $res = $Crontab->edit($data);
            }
            if($res  !== false){
                return $this->success('定时采集设置成功！',url('index'));
            } else {
                $this->error('');
            }
        }else{
            $data=$this->request->param();
            $info=$Crontab->info(['type'=>1,'relation_id'=>$data['sid'],'class_name'=>'admin/UnionCollect']);
            if($info){
                $this->assign('info',$info);
            }
            $this->assign('meta_title','设置定时采集');
            return $this->fetch();
        }
    }

    public function del_collect_log($id){
        $this->union_collect_log_rm($id);
        return $this->success('断点续采删除成功！');
    }

    private function get_collect_queue($param_data){
        $queue_list=Cache::tag('union_collect')->get('union_list');
        foreach ($queue_list['data'] as $key => $value) {
            if($value['lock']==0){
                $queue_list['data'][$key]['lock']=1;
                Cache::tag('union_collect')->set('union_list',$queue_list);
                return $value;
            }
        }
        if($queue_list['current_page']<$queue_list['last_page']){
            $param_data['page']=$queue_list['current_page']+1;
            $this->set_collect_queue($param_data);
            return $this->get_collect_queue($param_data);
        }
        return false;
    }

    private function set_collect_queue($param_data){
        $UnionCollect=(new \app\model\UnionCollect());
        if(isset($param_data['page'])){
            $this->union_collect_log_set($param_data);
        }
        $queue_list=Cache::tag('union_collect')->get('union_list');
        if(empty($queue_list)){
            $queue_list=['current_page'=>1,'last_page'=>1,'data'=>[]];
        }
        $data=$UnionCollect->collect_list(urldecode($param_data['url']),$param_data['type'],$param_data);
        if($data===false){
            $this->error();
        }
        $queue_list['current_page']=$data['current_page'];
        $queue_list['last_page']=$data['last_page'];
        foreach ($data['data'] as $key => $value){
            $queue_list['data'][$value['id']]=[
                'id'=>$value['id'],
                'title'=>$value['title'],
                'author'=>$value['author'],
                'chapter_count'=>$value['chapter_count'],
                'lock'=>0
            ];
        }
        Cache::tag('union_collect')->set('union_list',$queue_list);
        return $data;
    }

    private function rm_collect_queue($id){
        $queue_list=Cache::tag('union_collect')->get('union_list');
        unset($queue_list['data'][$id]);
        Cache::tag('union_collect')->set('union_list',$queue_list);
    }

    private function union_collect_log_set($param_data){
        $collect_log=Cache::get('union_collect_log');
        if(empty($collect_log)){
            $collect_log=[];
        }
        $collect_log[$param_data['sid']]=$param_data;
        Cache::set('union_collect_log',$collect_log);
    }

    private function union_collect_log_rm($id){
        $collect_log=Cache::get('union_collect_log');
        unset($collect_log[$id]);
        Cache::set('union_collect_log',$collect_log);
    }
}