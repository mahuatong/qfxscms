<?php

/**
 * 这个还不知道干嘛用的
 */
namespace app\model;
use think\Model;
use think\facade\Request;
use think\facade\Validate;

class Api extends Model{
    public function category($cid,$type,$filter){
        /**
         * @var CommonApi $api
         */
        $api= app()->make(CommonApi::class);
        $api->api_url=true;
        $category=$api->get_nav($cid,$type,$filter,Request::param('cid'),'id,title,pid,icon,type');
        foreach ($category as $key => $value) {
            $class[$key]=$value;
            if($value['branch']==1){
                $class[$key]['subor']=$this->category($value['id'],$type,$filter);
            }
        }
        return $class;
    }
}