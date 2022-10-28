<?php
namespace app\admin\controller;

use app\model\LogModel;
use think\Model;

class Log extends Base
{

    public function index(){
        $Log= (new LogModel());
        $list = $Log->lists();
        $this->assign('list', $list);
        $this->assign('meta_title','操作日志');
        return $this->fetch();
    }

    public function del(){
        $Log=(new LogModel());
        $res = $Log->del();
        if($res !== false){
            return $this->success('删除成功');
        } else {
            $this->error('删除失败！');
        }
    }
}