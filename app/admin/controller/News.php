<?php


namespace app\admin\controller;
use app\model\NewsModel;
use think\facade\Db;

class News extends Base
{

    public function index(){
        $News= (new NewsModel());
        $list = $News->lists();
        $this->assign('list', $list);
        $this->assign('category', get_tree(1));
        $this->assign('meta_title','文章列表');
        return $this->fetch();
    }

    public function open(){
        $News=(new \app\model\NewsModel());
        $list = $News->lists();
        $this->assign('list', $list);
        $this->assign('category', get_tree(1));
        $this->assign('meta_title','文章选择列表');
        return $this->fetch();
    }

    public function position($id){
        $News=(new \app\model\NewsModel());
        if($this->request->isPost()){
            $data = $this->request->post();
            $res = $News->edit($data,$this->request->action());
            if($res  !== false){
                $this->success('文章推荐修改成功！',url('index'));
            } else {
                $this->error();
            }
        }else{
            $info=$News->info($id);
            $this->assign('info',$info);
            $this->assign('meta_title','推荐');
            return $this->fetch();
        }
    }

    public function category(){
        if($this->request->isPost()){
            $data = $this->request->post();
            $News=(new \app\model\NewsModel());
            $res = $News->edit_field($data);
            if($res  !== false){
                $this->success('文章分类修改成功！',url('index'));
            } else {
                $this->error();
            }
        }else{
            $this->assign('category', get_tree(1));
            return $this->fetch();
        }
    }
	
	public function edit($id){
		$News=(new \app\model\NewsModel());
		if($this->request->isPost()){
            $data = $this->request->post();
			$res = $News->edit($data,$this->request->action());
			if($res  !== false){
                $this->success('文章修改成功！',url('index'));
            } else {
                $this->error();
            }
		}else{
			$info=$News->info($id);
            $this->assign('info',$info);
            $this->assign('category', get_tree(1));
			$this->assign('meta_title','修改文章');
			return $this->fetch();
		}
	}

	public function add(){
		$News=(new \app\model\NewsModel());
		if($this->request->isPost()){
            $data = $this->request->post();
			$res = $News->edit($data,$this->request->action());
			if($res  !== false){
                $this->success('文章添加成功！可以在章节管理添加章节内容。',url('index'));
            } else {
                $this->error();
            }
		}else{
            $this->assign('category', get_tree(1));
			$this->assign('meta_title','添加文章');
			return $this->fetch('edit');
		}
	}

	public function del(){
        $id = array_unique((array)$this->request->param('id'));
        if ( empty($id) ) {
            $this->error('请选择要操作的数据!');
        }
        $res = NewsModel::destroy($id);
        if($res  !== false){
            $this->success('删除成功');
        } else {
            $this->error();
        }
    }

    public function status(){
        $id = $this->request->param('id');
        $News=(new \app\model\NewsModel());
        $info = $News->info($id);
        if($info['status']==1){
            return $this->forbid('News');
        }else{
            return $this->resume('News');
        }
    }
}