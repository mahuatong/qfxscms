<?php


namespace app\model;
use think\Model;
use think\facade\Request;
use think\facade\Config;
use app\admin\validate\Slider as SliderValidate;

class SliderModel extends Model{

    protected $name = 'slider';
    protected $autoWriteTimestamp = true;

    public function getTypeTextAttr($value,$data){
        $status = [0=>'web',1=>'wap',2=>'app'];
        return $status[$data['type']];
    }


}