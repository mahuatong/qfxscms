<?php


namespace app\admin\controller;
use think\App;
use think\facade\Db;
use think\facade\Cache;

class Config extends Base 
{
    public function __construct(App $app)
    {
        parent::__construct($app);
    }

    /**
     * 批量保存配置
     */
    public function save($config){
        if($config && is_array($config)){
            foreach ($config as $name => $value) {
                $map = ['name' => $name];
                if(is_array($value)){
                    $value=implode(",", $value);
                }
                Db::name('config')->where($map)->update(['value'=>$value]);
            }
        }
        Cache::delete('config_data');
        $this->success('保存成功！');
    }

    // 获取某个标签的配置参数
    public function index() {
        $id     =   $this->request->param('id',1);
        $list   =   Db::name("Config")->where(['group'=>$id,'display'=>1])->field('id,name,title,extra,value,remark,type')->order('sort')->select();
        if($list) {
            $this->assign('list',$list);
        }

        $this->assign('id',$id);
        $this->assign('meta_title','系统设置');
        return $this->fetch();
    }
}
