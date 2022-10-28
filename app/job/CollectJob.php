<?php
/**
 * @author zhushichuan
 * @email 469702544@qq.com
 * @time 2022/10/25
 *
 *
 */


namespace app\job;

use app\service\listener\CollectThread;
use think\facade\Log;
use think\queue\Job;
class CollectJob
{
    public function fire(Job $job, $data){
        Log::error('队列开始进来了>>>>>');
        Log::error('获取的参数:'.json_encode($data));

        $id = $data['id'];
        $r = (new CollectThread())->threads($id);
        if(!$r){
            // 可以继续获取，则重新发布
            $job->release();
        }
        $job->delete();
    }
}