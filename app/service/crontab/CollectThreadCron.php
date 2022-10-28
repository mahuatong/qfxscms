<?php
/**
 * @author zhushichuan
 * @email 469702544@qq.com
 * @time 2022/10/25
 *
 *
 */


namespace app\service\crontab;


use app\service\listener\CollectThread;
use think\facade\Log;

class CollectThreadCron extends Cron
{
    public function handle($event){
        $this->tick(3000, function (){
            (new CollectThread())->handle(1);
        });
    }
}