<?php
namespace app\admin\controller;
use think\App;
use think\facade\Cache;
use think\facade\Config;
use org\Oauth;

class Upgrade extends Base{

    public function __construct(App $app)
    {
        parent::__construct($app);
        exception('不能升级');
    }
}