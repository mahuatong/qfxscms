<?php
/**
 * @author zhushichuan
 * @email 469702544@qq.com
 * @time 2022/10/25
 *
 *
 */


namespace app\service\listener;

use app\admin\controller\Collect;
use app\job\CollectJob;
use app\model\CollectModel;
use net\Gather;
use think\facade\Cache;
use think\facade\Db;
use think\facade\Log;
use think\facade\Queue;

/**
 * 采集
 * Class CollectThread
 * @package app\service\listener
 */
class CollectThread
{
    private $cache_keys = 'is_handle_auto_';
    public function handle($event){
        $collcet = CollectModel::where('is_auto','=',1)->select()->toArray();
        $collect_thread_num = config('web')['collect_thread_num'];
        foreach ($collcet as $v){
            $id = $v['id'];
            Db::name('collect')->where('id',$id)->update(['collect_time'=>time()]);
            $info = $v;
            Cache::set('collect_info_'.$id,$info);
            $source=Gather::convert_source_url($info['source_url']);
            Cache::set('collect_source_url_'.$id,$source);
//            $current=Cache::get('collect_log_'.$id);
            $cache_key = $this->cache_keys.$v['id'];
            if(Cache::has($cache_key)){
                continue;
            }
            // 根据线程数量配置
            for ($i=0;$i<intval($collect_thread_num);$i++){
                Queue::push('CollectJob', ['id'=>$id],'my_queue');
            }
//            $this->collect_list_set($id);
        }

    }

    //进行采集
    public function threads($id){
        $cache_key = $this->cache_keys.$id;
        $is_stop = false;

        Cache::set($cache_key,1,10);
        $data=$this->collect_list_get($id);
        $this->collect_list_rm($id,$data['url']);
        if($data===false){
            $this->collect_rm($id);
            return true;
        }
        $info=Cache::get('collect_info_'.$id);
        $return=Gather::field_content($info,$data['url']);
        if(isset($return['error'])){
            // 采集错误
            Log::error('爬取错误：'.$return['error']);
            return true;
        }
        if(empty($return['code'])){
            Log::error("保存数据了");
            (new \app\model\CollectModel())->sever_data($info,$return);
        }
        if($data['current']>=$data['count']){
            Log::error('爬取完成');
            return true;
        }
        return $is_stop;

    }
    public function collect_list_get($id){
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
            $res = $this->collect_list_set($id,$list['current']+1);
            if(!$res){
                return  false;
            }
            Cache::set('collect_log_'.$id,$list['current']+1);
            return $this->collect_list_get($id);
        }
        return false;
    }
    public function collect_list_set($id,$source_num){
        $info=Cache::get('collect_info_'.$id);
        $source_url=Cache::get('collect_source_url_'.$id);
        $list['current']=$source_num;
        $list['count']=count($source_url);
        $list_content_html=Gather::get_html($source_url[$source_num],$info['charset'],$info['url_complete']);
        if($list_content_html==false){
            return  false;
        }
        $list_content_html=Gather::get_section_data($list_content_html,$info['section']);
        $list_url=Gather::field_rule(['rule'=>$info['url_rule'],'merge'=>$info['url_merge']],$list_content_html,true);
        if(!is_array($list_url)){
            return  false;
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
    public function collect_rm($id){
        Cache::delete('collect_info_'.$id);
        Cache::delete('collect_source_url_'.$id);
        Cache::delete('collect_list_'.$id);
        Cache::delete('collect_log_'.$id);
    }

    private function collect_list_rm($id,$url){
        $queue_list=Cache::get('collect_list_'.$id);
        unset($queue_list['data'][$url]);
        Cache::set('collect_list_'.$id,$queue_list);
    }

}