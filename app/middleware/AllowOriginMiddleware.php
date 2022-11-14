<?php
/**
 * @author zhushichuan
 * @email 469702544@qq.com
 * @time 2022/10/31
 *
 *
 */


namespace app\middleware;

use app\Request;
use think\Response;
use think\middleware\AllowCrossDomain;

class AllowOriginMiddleware extends AllowCrossDomain
{

    protected $cookieDomain;

    // header头配置
    protected $header = [
        'Access-Control-Allow-Credentials' => 'true',
        'Access-Control-Max-Age'           => 1800,
        'Access-Control-Allow-Methods'     => 'GET, POST, PATCH, PUT, DELETE, OPTIONS',
        'Access-Control-Allow-Headers'     => 'Authorization, Content-Type, If-Match, If-Modified-Since, If-None-Match, If-Unmodified-Since, X-CSRF-TOKEN, X-Requested-With,token',
    ];


    public function __construct(\think\Config $config)
    {
        parent::__construct($config);
        $this->cookieDomain = $config->get('cookie.domain', '');

    }

    /**
     * 允许跨域请求
     * @access public
     * @param Request $request
     * @param Closure $next
     * @param array   $header
     * @return Response
     */
    public function handle($request, \Closure $next, ?array $header = [])
    {
        $header = !empty($header) ? array_merge($this->header, $header) : $this->header;

        if (!isset($header['Access-Control-Allow-Origin'])) {
            $origin = $request->header('origin');

            if ($origin && ('' == $this->cookieDomain || strpos($origin, $this->cookieDomain))) {
                $header['Access-Control-Allow-Origin'] = $origin;
            } else {
                $header['Access-Control-Allow-Origin'] = '*';
            }
        }

        return $next($request)->header($header);
    }
}
