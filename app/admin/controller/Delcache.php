<?php


namespace app\admin\controller;
use think\facade\Cache;
use think\facade\Env;

class Delcache extends Base{

    public function index(){
        $this->assign('meta_title','清除缓存');
        return $this->fetch();
    }

	public function del(){
        $data=$this->request->post();
        if(!empty($data['cache'])){
            foreach ($data['cache'] as $key => $value) {
                if($value){
                    Cache::delete($key);
                }
            }
        }
        if(!empty($data['temp'])){
            $temp_path = Env::get('runtime_path').'temp'.DIRECTORY_SEPARATOR;
            if(is_dir($temp_path)){
                del_dir_file($temp_path,true);
            }
        }
        if(!empty($data['db'])){
            $temp_path = Env::get('runtime_path').'data'.DIRECTORY_SEPARATOR;
            if(is_dir($temp_path)){
                del_dir_file($temp_path,true);
            }
        }
        if(!empty($data['html'])){
            $html_path = Env::get('runtime_path').'html'.DIRECTORY_SEPARATOR;
            if(is_dir($html_path)){
                del_dir_file($html_path,true);
            }
        }
        $this->success('缓存清除成功！');
    }
}