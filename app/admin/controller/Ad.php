<?php
namespace app\admin\controller;
use app\model\AdModel;
use think\facade\Db;
use think\Model;

class Ad extends Base
{

    public function index(){
        $Ad=app()->make(AdModel::class);
        $list = $Ad->lists();
        $this->assign('list', $list);
        $this->assign('meta_title','广告列表');
        return $this->fetch();
    }
	
	public function edit($id){
		$Ad=(new AdModel());
		if($this->request->isPost()){
            $data = $this->request->post();
            $res = AdModel::update($data,['id'=>$data['id']]);
			if($res){
                $this->success('广告修改成功！',url('index'));
            } else {
                $this->error('保存失败');
            }
		}else{
			$info=$Ad->info($id);
            $this->assign('info',$info);
            $this->assign('category', get_tree(0));
			$this->assign('meta_title','修改广告');
			return $this->fetch();
		}
	}

	public function add($pid = 0){
        if($this->request->isPost()){
            $data = $this->request->post();
			$res = AdModel::create($data);
			if($res){
                $this->success('广告添加！',url('index'));
            } else {
                $this->error('保存失败');
            }
		}else{
            $this->assign('category', get_tree(0));
			$this->assign('meta_title','添加广告');
			return $this->fetch('edit');
		}
	}

	public function del(){
        $id = array_unique((array)$this->request->param('id'));
        if ( empty($id) ) {
            $this->error('请选择要操作的数据!');
        }
        $res = AdModel::destroy($id);
        if($res){
            $this->success('删除成功');
        } else {
            $this->error('删除失败');
        }
    }
}