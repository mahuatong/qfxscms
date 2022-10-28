<?php


namespace app\model;
use think\Model;
use think\facade\Request;
use think\facade\Cache;
use app\admin\validate\Route as RouteValidate;

class RouteModel extends Model{

    protected $name = 'route';

}