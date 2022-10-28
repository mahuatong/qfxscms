<?php
// +----------------------------------------------------------------------
// | KyxsCMS [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2018~2019 http://www.kyxscms.com All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: kyxscms
// +----------------------------------------------------------------------
namespace org;
use think\facade\Cache;
use think\facade\Config;
use think\facade\Cookie;
use think\facade\Request;
use net\Http;

class Oauth{

    protected $appKey = '';
    protected $appSecret = '';
	protected $response_type = 'code-and-token';
    protected $getAccessTokenURL = '';
    protected $getDeductURL = '';

	public function __construct(){
        $this->getAccessTokenURL = Config::get('web.official_url').'/oauth/authorize';
        $this->getDeductURL = Config::get('web.official_url').'/oauth/deduct';
        $this->appKey    = Config::get('web.client_id');
        $this->appSecret = Config::get('web.client_secret');
    }

    /**
     * 获取access_token
     * @param string $code 授权登录成功后得到的code信息
     */
    public function getAccessToken(){
        $params = [
            'client_id'     => $this->appKey,
            'response_type'    => $this->response_type,
            'display'  => 'frame'
        ];
        if(Request::isAjax()){
            $params['display']='url';
        }
        // 获取token信息
        $data = Http::doPost($this->getAccessTokenURL, $params);
        if(is_ssl()){
            $data = str_replace('http:','https:',$data);
        }
        if(Request::isAjax()){
            header("Location: " . $data);
        }
        echo $data;
    }

    public function setToken($token,$expires){
        Cache::set('hx_oauth_access_token',$token,$expires);
    }

    public function getToken(){
        return Cache::get('hx_oauth_access_token'); 
    }

    public function checkAuth(){
        if($this->appKey && $this->appSecret){
            if($this->getToken()){
                return true;
            }else{
                Cookie::set('__forward__',Request::url());
                $this->getAccessToken();
            }
        }else{
            Cookie::set('__forward__',Request::url());
            header("Location: ".url('market/oiauth_reg'));
            exit;
        }
    }

    public function checkDeduct($type){
        $headers=$this->getAuthorizationHeader();
        if($headers){
            $deduct=Http::doPost($this->getDeductURL,['url'=>Request::domain(),'type'=>$type],60,"AuthorizationCode: ".$headers);
            return $deduct;
        }
        return false;
    }

    private function getAuthorizationHeader() {
        if (array_key_exists("HTTP_AUTHORIZATIONCODE", $_SERVER))
          return $_SERVER["HTTP_AUTHORIZATIONCODE"];
        if (function_exists("apache_request_headers")) {
          $headers = apache_request_headers();
          if (array_key_exists("Authorizationcode", $headers))
            return $headers["Authorizationcode"];
        }
        return false;
    }
}