<?php
/**
 * @author zhushichuan
 * @email 469702544@qq.com
 * @time 2022/10/25
 *
 *
 */


namespace app\service\crontab;


class CronStart
{
    public function handle($event){
        try{
            event('crontab');//app/event.php 里面配置事件
        }catch (\Throwable $e){}

    }
}