<?php


namespace app\admin\controller;
use app\model\LinkModel;
use think\facade\Db;

class Link extends Base
{

    public function index(){
        $list = Db::name('Link')->order('sort asc')->paginate(config('web.list_rows'));
        $this->assign('list', $list);
        $this->assign('meta_title','友情链接列表');
        return $this->fetch();
    }
	
	public function edit($id){
		if($this->request->isPost()){
		    $data = $this->request->param();
		    LinkModel::where('id','=',$data['id'])->update($data);
            return $this->success('友情链接修改成功！',url('index'));
		}else{
			$info=LinkModel::find($id)->toArray();
            $this->assign('info',$info);
			$this->assign('meta_title','修改友情链接');
			return $this->fetch();
		}
	}

	public function add(){
		if($this->request->isPost()){
            LinkModel::create($this->request->param());
            return $this->success('友情链接添加！',url('index'));

		}else{
			$this->assign('meta_title','添加友情链接');
			return $this->fetch('edit');
		}
	}

	public function del(){
        $id = array_unique((array)$this->request->param('id'));
        if ( empty($id) ) {
            $this->error('请选择要操作的数据!');
        }
        $res = LinkModel::destroy($id);
        if($res){
            return $this->success('删除成功');
        } else {
            $this->error('删除失败！');
        }
    }
}