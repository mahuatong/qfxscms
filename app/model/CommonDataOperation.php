<?php


namespace app\model;
use think\Model;
use think\facade\Db;
use think\facade\Config;
use think\facade\Cache;
use net\Http;
use org\Oauth;

class CommonDataOperation{

    public function after($type,$model,$data){
        $map = ['status'=>1,'group'=>'data_operation'];
        $addons = Db::name('Addons')->where($map)->column('name','id');
        foreach ($addons as $key => $value) {
            $addons_class = get_addon_class($value);
            if(class_exists($addons_class)){
                $addon = new $addons_class();
                $addon->run(['type'=>$type,'model'=>$model,'data'=>$data]);
            }
        }
    }

    public function replace_str($view,$html){
        $map = ['status'=>1,'group'=>'home_replace_html'];
        $addons = Db::name('Addons')->where($map)->column('name','id');
        foreach ($addons as $key => $value) {
            $addons_class = get_addon_class($value);
            if(class_exists($addons_class)){
                $addon = new $addons_class();
                $html=$addon->run(['view'=>$view,'html'=>$html]);
            }
        }
        return $html;
    }

    public function print_js(){
        $html='';
        $map = ['status'=>1,'group'=>'home_js'];
        $addons = Db::name('Addons')->where($map)->column('name','id');
        foreach ($addons as $key => $value) {
            $addons_class = get_addon_class($value);
            if(class_exists($addons_class)){
                $addon = new $addons_class();
                $html.=$addon->run();
            }
        }
        return $html;
    }
}