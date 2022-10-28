<?php


namespace app\admin\controller;
use think\facade\Db;
use think\facade\Cache;
use net\Gather;

set_time_limit(0);
ini_set('memory_limit', '-1');

class Collect extends Base
{

    public function index(){
        $Collect= (new \app\model\CollectModel());
        $this->assign('list', $Collect->lists());
        $this->assign('meta_title','采集列表');
        return $this->fetch();
    }

    public function terms(){
    	if($this->request->isPost()){
    		Cache::set('collect_terms',1);
    		$this->success('同意',url('index'));
    	}else{
    		$this->assign('meta_title','免责声明');
    		return $this->fetch();
    	}
    }
    
    public function edit($id){
        $Collect=(new \app\model\CollectModel());
        if($this->request->isPost()){
            $data = $this->request->post(false);
            $res = $Collect->edit($data);
            if($res  !== false){
                $this->success('采集修改成功！',url('index'));
            } else {
                $this->error($Collect->getError());
            }
        }else{
            $info=$Collect->info($id);
            $this->assign('info',$info);
            $this->assign('field',$Collect->fields());
            $this->assign('meta_title','修改采集');
            return $this->fetch();
        }
    }

    public function add(){
        $Collect=(new \app\model\CollectModel());
        if($this->request->isPost()){
            $data = $this->request->post(false);
            $res = $Collect->edit($data);
            if($res  !== false){
                $this->success('采集添加！',url('index'));
            } else {
                $this->error($Collect->getError());
            }
        }else{
            $this->assign('field',$Collect->field());
            $this->assign('meta_title','添加采集');
            return $this->fetch('edit');
        }
    }

    public function del(){
        $id = array_unique((array)$this->request->param('id'));
        if ( empty($id) ) {
            $this->error('请选择要操作的数据!');
        }
        $map = ['id' => $id];
        if(Db::name('Collect')->where($map)->delete()){
            $this->success('删除成功');
        } else {
            $this->error('删除失败！');
        }
    }

    public function del_data(){
        $id = array_unique((array)$this->request->param('id'));
        if ( empty($id) ) {
            $this->error('请选择要操作的数据!');
        }
        $info=(new \app\model\CollectModel())->info($id);
        if($info['type']=='novel'){
            $map = ['collect_id' => $id];
            Db::name('novel_chapter')->where($map)->chunk(200, function($data) {
                foreach ($data as $chapter) {
                    (new \app\model\NovelModel())->del($chapter['novel_id']);
                }
            });
        }
        $this->success('删除数据成功');
    }

    public function source(){
        $this->assign('index',$this->request->param('index'));
        $this->assign('meta_title','添加列表采集');
        return $this->fetch();
    }

    public function relation(){
        $this->assign('index',$this->request->param('index'));
        $this->assign('meta_title','添加关联采集');
        return $this->fetch();
    }

    public function release(){
        if($this->request->isPost()){
            $data=$this->request->param();
            $res = (new \app\model\CollectUnion())->release($data);
            if($res  !== false){
                $this->success('规则发布成功！',url('index'));
            } else {
                $this->error();
            }
        }else{
           $this->assign('collect_id',$this->request->param('id'));
            $this->assign('meta_title','发布采集规则');
            return $this->fetch(); 
        }
    }

    public function field_category(){
        $index=$this->request->param('index');
        $this->assign('index',$index);
        $this->assign('meta_title','栏目转换');
        return $this->fetch();
    }

    public function field_replace(){
        $index=$this->request->param('index');
        if(strpos($index,'|') !== false){
            $index=explode('|',$index);
            $this->assign('index',$index[0]);
            $this->assign('reindex',$index[1]);
        }else{
            $this->assign('index',$index);
        }
        $this->assign('meta_title','内容替换');
        return $this->fetch();
    }

    public function collect($id,$current=0){
        Db::name('collect')->where('id',$id)->update(['collect_time'=>time()]);
        $info=(new \app\model\CollectModel())->info($id);
        Cache::set('collect_info_'.$id,$info);
        $source=Gather::convert_source_url($info['source_url']);
        Cache::set('collect_source_url_'.$id,$source);
        $list=$this->collect_list_set($id,$current);
        $this->assign('meta_title', '采集进度');
        return $this->fetch();
    }

    // 采集按钮第一步
    public function collect_continuation($id){
    	$collect_log=Cache::get('collect_log_'.$id);
    	if($collect_log){
    	    $url = "/admin/Collect/collect.html?id=".$id;
    		return $this->success('继续采集',url('collect',['id'=>$id,'current'=>$collect_log]),['url'=>$url]);
    	}else{
    		$this->error('重新采集',url('collect',['id'=>$id,'current'=>0]),['url'=>url('collect',['id'=>$id])]);
    	}
    }

    public function collect_thread($id){
        $data=$this->collect_list_get($id);
        $this->collect_list_rm($id,$data['url']);
        if($data===false){
            $this->collect_rm($id);
            return $this->success('采集完成！','',['state'=>'finish']);
        }
        $info=Cache::get('collect_info_'.$id);
        $return=Gather::field_content($info,$data['url']);
        if(isset($return['error'])){
            $this->error($return['msg'],'');
        }
        if(empty($return['code'])){
            (new \app\model\CollectModel())->sever_data($info,$return);
        }
        if(empty($return['msg'])){
        	if(isset($return["id"]) && isset($info['update'])){
        		$return['msg']='替换成功';
        	}else{
        		$return['msg']='添加成功';
        	}
        	$return['status']='ok';
        }
        return $this->success($return['msg'],'',['title'=>$return['title'],'url'=>$return['reurl'],'status'=>$return['status'],'count'=>$data['count'],'current'=>$data['current']]);
    }

    public function collect_chapter(){
    	$count=Db::name('novel_chapter')->where([['collect_id','>',0],['reurl','<>',''],['status','=',1]])->count('id');
    }

    public function collect_chapter_thread($id){
    	$map[] = ['status','=',1];
    	$map[] = ['id','=',$id];
    	$chapter_data=Db::name('novel_chapter')->field('id,chapter,novel_id,collect_id')->where($map)->find();
    	if($chapter_data){
    		$chapter_data['chapter']=(new \app\model\CommonApi())->decompress_chapter($chapter_data['chapter']);
    		$chapter_data['chapter']=json_decode($chapter_data['chapter'],true);
    		$getchapter=Gather::get_chapter_content($chapter_data['collect_id'],$chapter['reurl']);
            if($getchapter!==false){
            	$word=mb_strlen($getchapter['chapter_content']);
            	if($word>500){
            		$this->set_chapter_content($chapter['path'],$getchapter['chapter_content']);
	    			$chapter_data['chapter'][$key]['auto']=0;
	    			$chapter_data['chapter'][$key]['word']=$word;
	    			if(!empty($getchapter['chapter_title'])){
	    				$chapter_data['chapter'][$key]['title']=$getchapter['chapter_title'];
	    			}
	    			$chapter_data['chapter']=json_encode($chapter_data['chapter']);
	    			$chapter_data['chapter']=(new \app\model\CommonApi())->compress_chapter($chapter_data['chapter']);
	    			Db::name('novel_chapter')->update($chapter_data);
	    			$novel_data=['word'=>Db::raw('word+'.($word-$chapter['word']))];
	    			Db::name('novel')->where(['id'=>$chapter['novel_id']])->update($novel_data);
            	}
				$chapter['word']=$word;
                $chapter['title']=empty($getchapter['chapter_title'])?'':$getchapter['chapter_title'];
                $chapter['content']=empty($getchapter['chapter_content'])?'章节内容转码失败！':$getchapter['chapter_content'];
            }
    	}
    }

    public function test(){
        if($this->request->isPost()){
            $info=$this->request->param('info');
            $source_url=Gather::convert_source_url($info['source_url']);
            $list_content_html=Gather::get_html($source_url[0],$info['charset'],$info['url_complete']);
            if (empty($list_content_html)){
                return Gather::echo_msg('未获得起始页面数据!:url['.$source_url[0].']');
            }
            if(!empty($info['section'])){
                $list_content_html=Gather::get_section_data($list_content_html,$info['section']);
            }
            $list_url=Gather::field_rule(['rule'=>$info['url_rule'],'merge'=>$info['url_merge']],$list_content_html,true);
            if(!is_array($list_url)){
                return Gather::echo_msg('未获取到列表，请检查是否可以连接目标网站或列表规则是否正确!');
            }
            $list_url=array_unique($list_url);
            foreach ($list_url as $key=>$cont_url) {
                if (!empty($info['url_must'])) {
                    if (!preg_match('/' . $info['url_must'] . '/i', $cont_url)) {
                        continue;
                    }
                }
                if (!empty($info['url_ban'])) {
                    if (preg_match('/' . $info['url_ban'] . '/i', $cont_url)) {
                        continue;
                    }
                }
                $return=Gather::field_content($info,$cont_url,$this->request->param('test'));
                break;
            }
        }else{
           $this->assign('field',$this->request->param('field'));
            $this->assign('meta_title','采集规则测试');
            return $this->fetch(); 
        }
    }

    public function collect_list_set($id,$source_num){
        $info=Cache::get('collect_info_'.$id);
        $source_url=Cache::get('collect_source_url_'.$id);
        $list['current']=$source_num;
        $list['count']=count($source_url);
        $list_content_html=Gather::get_html($source_url[$source_num],$info['charset'],$info['url_complete']);
        if($list_content_html==false){
            $this->error('未获取到起始页面数据!','',['state'=>'stop']);
        }
        $list_content_html=Gather::get_section_data($list_content_html,$info['section']);
        $list_url=Gather::field_rule(['rule'=>$info['url_rule'],'merge'=>$info['url_merge']],$list_content_html,true);
        if(!is_array($list_url)){
            $this->error('未获取到列表，请检查是否可以连接目标网站或列表规则是否正确!','',['state'=>'stop']);
        }
        $list_url=array_unique($list_url);
        if($info['url_reverse']){
            $list_url=array_reverse($list_url);
        }
        foreach ($list_url as $key=>$cont_url) {
            if (!empty($info['url_must'])) {
                if (!preg_match('/' . $info['url_must'] . '/i', $cont_url)) {
                    continue;
                }
            }

            if (!empty($info['url_ban'])) {
                if (preg_match('/' . $info['url_ban'] . '/i', $cont_url)) {
                    continue;
                }
            }
            $list['data'][$cont_url]=['url'=>$cont_url,'lock'=>0];
        }
        Cache::set('collect_list_'.$id,$list);
        return $list;
    }

    private function collect_list_get($id){
        $list=Cache::get('collect_list_'.$id);
        foreach ($list['data'] as $key => $value) {
            if($value['lock']==0){
                $list['data'][$key]['lock']=1;
                Cache::set('collect_list_'.$id,$list);
                $value['count']=$list['count'];
                $value['current']=$list['current'];
                return $value;
            }
        }
        if($list['current']+1<$list['count']){
            $this->collect_list_set($id,$list['current']+1);
            Cache::set('collect_log_'.$id,$list['current']+1);
            return $this->collect_list_get($id);
        }
        return false;
    }

    private function collect_list_rm($id,$url){
        $queue_list=Cache::get('collect_list_'.$id);
        unset($queue_list['data'][$url]);
        Cache::set('collect_list_'.$id,$queue_list);
    }

    private function collect_rm($id){
        Cache::delete('collect_info_'.$id);
        Cache::delete('collect_source_url_'.$id);
        Cache::delete('collect_list_'.$id);
        Cache::delete('collect_log_'.$id);
    }
}