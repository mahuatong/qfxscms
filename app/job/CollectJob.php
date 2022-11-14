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
        $id = $data['id'];
        $r = null;
        try{
            $r = (new CollectThread())->threads($id);
        }catch (\Throwable $e){
            Log::error('爬取错误：'.$e->getMessage().'--'.$e->getLine());
        }
        if($r === false){
            // 可以继续获取，则重新发布
            $job->release();
        }
        $job->delete();
    }
}