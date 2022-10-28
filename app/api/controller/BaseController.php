<?php
/**
 * @author zhushichuan
 * @email 469702544@qq.com
 * @time 2022/10/26
 *
 *
 */


namespace app\api\controller;


use app\service\Json;

class BaseController
{
    public function success($msg = '', $data = [])
    {
        return app()->make(Json::class)->success($msg,$data);
    }

    public function error($msg = '', $data = [])
    {
        return app()->make(Json::class)->error($msg,$data);
    }
}