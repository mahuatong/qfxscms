<?php


namespace app\admin\controller;
use app\model\RouteModel;
use think\facade\Cache;
use think\facade\Db;
use think\facade\Request;
use think\Model;

class Route extends Base
{

    public function index(){
        $list = Db::name('route')->where(['display' => 1])->paginate(config('web.list_rows'));
        $this->assign('list', $list);
        $this->assign('meta_title','路由列表');
        return $this->fetch();
    }
	
	public function edit($id){
		if($this->request->isPost()){
            $data = $this->request->param();
            RouteModel::update($data,['id'=>$data['id']]);
            $this->success('路由修改成功！',url('index'));
		}else{
			$info=RouteModel::find($id)->toArray();
            $this->assign('info',$info);
			$this->assign('meta_title','修改路由');
			return $this->fetch();
		}
	}

	public function add(){
		if($this->request->isPost()){
            $data=Request::post();
            unset($data['id']);
            RouteModel::create($data);
            Cache::delete('route_data');
            $this->success('保存成功！',url('index'));

        }else{
			$this->assign('meta_title','添加路由');
			return $this->fetch('edit');
		}
	}

	public function del(){
        $id = array_unique((array)$this->request->param('id'));
        if ( empty($id) ) {
            $this->error('请选择要操作的数据!');
        }
        $res = RouteModel::destroy($id);
        if($res !== false){
            $this->success('删除成功');
        } else {
            $this->error('删除失败！');
        }
    }
}