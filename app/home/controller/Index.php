<?php


namespace app\home\controller;

use think\facade\Cookie;

class Index extends Base
{
    public function index()
    {
    	Cookie::set('__forward__',$this->request->url());
    	$this->assign('pos',4);
        return $this->fetch('/index');
    }
}
