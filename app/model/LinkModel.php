<?php


namespace app\model;
use think\Model;
use think\facade\Request;
use app\admin\validate\Link as LinkValidate;

class LinkModel extends Model{

    protected $name = 'link';
    protected $autoWriteTimestamp = true;


}