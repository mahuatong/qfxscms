<?php
/**
 * @author zhushichuan
 * @email 469702544@qq.com
 * @time 2022/10/25
 *
 *
 */



namespace app\service;


use think\facade\Config;
use think\facade\Lang;
use think\Response;

/**
 * Json输出类
 * Class Json
 * @package crmeb\utils
 */
class Json
{
    private $code = 200;

    public function code(int $code): self
    {
        $this->code = $code;
        return $this;
    }

    public function make(int $status, string $msg, ?array $data = null): Response
    {
        $request = app()->request;
        $res = compact('status', 'msg');

        if (!is_null($data))
            $res['data'] = $data;

        if ($res['msg'] && !is_numeric($res['msg'])) {
            if (!$range = $request->get('lang')) {
                $range = $request->cookie(Config::get('lang.cookie_var'));
            }
            $langData = array_values(Config::get('lang.accept_language', []));
            if (!in_array($range, $langData)) {
                $range = 'zh-cn';
            }
            $res['msg'] = Lang::get($res['msg'], [], $range);
        }
        return Response::create($res, 'json', $this->code);
    }



    public function success($msg = 'ok', ?array $data = null): Response
    {
        if (is_array($msg)) {
            $data = $msg;
            $msg = 'ok';
        }
        return $this->make(200, $msg, $data);
    }

    public function successful(...$args): Response
    {
        return $this->success(...$args);
    }

    public function fail($msg = 'fail', ?array $data = null): Response
    {
        if (is_array($msg)) {
            $data = $msg;
            $msg = 'ok';
        }

        return $this->make(400, $msg, $data);
    }

    public function status($status, $msg = 'ok', $result = [])
    {
        $status = strtoupper($status);
        if (is_array($msg)) {
            $result = $msg;
            $msg = 'ok';
        }
        return $this->success($msg, compact('status', 'result'));
    }
}
