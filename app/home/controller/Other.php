<?php


namespace app\home\controller;

use think\facade\Cookie;

class Other extends Base
{
    public function index()
    {
    	Cookie::set('__forward__',$this->request->url());
       	$tpl=$this->request->param('tpl');

        $tpl = '/'.str_replace('.html','',$tpl);

        return $this->fetch($tpl);
    }
}
