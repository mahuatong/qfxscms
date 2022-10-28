<?php


namespace app\admin\controller;
use app\model\SliderModel;
use think\facade\Db;

class Slider extends Base
{

    public function index(){
        $list = (new SliderModel())->paginate();
        $this->assign('list', $list);
        $this->assign('meta_title','幻灯列表');
        return $this->fetch();
    }
	
	public function edit($id){
		if($this->request->isPost()){
		    $data = $this->request->param();
            unset($data['file']);
            SliderModel::where('id','=',$data['id'])->update($data);
            $this->success('幻灯修改成功！',url('index'));

		}else{
			$info=SliderModel::find($id);
            $this->assign('info',$info);
			$this->assign('meta_title','修改幻灯');
			return $this->fetch();
		}
	}

	public function add(){
		if($this->request->isPost()){
		    $data = $this->request->param();
		    unset($data['id']);
		    SliderModel::create($data);
            $this->success('幻灯添加！',url('index'));
		}else{
			$this->assign('meta_title','添加幻灯');
			return $this->fetch('edit');
		}
	}

	public function del(){
        $id = array_unique((array)$this->request->param('id'));
        if ( empty($id) ) {
            $this->error('请选择要操作的数据!');
        }
        $res = SliderModel::destroy($id);
        if($res){
            $this->success('删除成功');
        } else {
            $this->error('删除失败！');
        }
    }

    public function status(){
        $id = $this->request->param('id');
        $info = SliderModel::find($id);
        if($info['status']==1){
            return $this->forbid('Slider');
        }else{
            return $this->resume('Slider');
        }
    }
}