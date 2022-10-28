<?php
/**
 * @author zhushichuan
 * @email 469702544@qq.com
 * @time 2022/10/25
 *
 *
 */


namespace app\service\listener;


use think\facade\Log;

class TestListener
{
    public function handle($event){
        Log::error('开始了.'.date('Y-m-d H:i:s'));
    }
}