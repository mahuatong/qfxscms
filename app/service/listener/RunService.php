<?php
/**
 * @author zhushichuan
 * @email 469702544@qq.com
 * @time 2022/10/24
 *
 *
 */


namespace app\service\listener;


use think\facade\Cache;
use think\facade\Config;
use think\facade\Log;

class RunService
{
    public function handle($event){
        try {
            $config =   Cache::get('config_data');
            if(!$config){
                $config =  config_lists();
                Cache::set('config_data',$config);
            }
            Config::set($config,'web');
        }catch(\Throwable $e){
        }
    }
}