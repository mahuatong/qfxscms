<?php
namespace app\admin\controller;
use app\model\CategoryModel;
use think\facade\Db;

class Category extends Base
{

    public function index(){
        $list = (new CategoryModel())->getTree(0,'id,title,type,sort,pid,status');
        $this->assign('list', $list);
        $this->assign('meta_title','栏目列表');
        return $this->fetch();
    }
	
	public function edit($id){
		$Category=(new CategoryModel());
		if($this->request->isPost()){
			$res = $Category->edit();
			if($res  !== false){
                $this->success('栏目修改成功！',url('index'));
            } else {
                $this->error();
            }
		}else{
			$info=$Category->info($id);
            $cate = $Category->info($info['pid']);
            $this->assign('info',$info);
            $this->assign('pid',$info['pid']);
            $this->assign('category',$cate);
			$this->assign('meta_title','修改栏目');
			return $this->fetch();
		}
	}

	public function add($pid = 0){
		$Category=(new CategoryModel());
		if($this->request->isPost()){
			$res = $Category->edit();
			if($res  !== false){
                $this->success('栏目添加！',url('index'));
            } else {
                $this->error();
            }
		}else{
            $cate = [];
            if($pid){
                /* 获取上级栏目信息 */
                $cate = $Category->info($pid);
                if(!($cate && 1 == $cate['status'])){
                    $this->error('指定的上级栏目不存在或被禁用！','');
                }
            }
            $this->assign('pid',$pid);
            $this->assign('category',$cate);
			$this->assign('meta_title','添加栏目');
			return $this->fetch('edit');
		}
	}

	public function del(){
        $id = array_unique((array)$this->request->param('id'));
        if ( empty($id) ) {
            $this->error('请选择要操作的数据!');
        }
        $Category= new CategoryModel();
        $res = $Category->del($id);
        if($res !== false){
            $this->success('删除成功');
        } else {
            $this->error('删除失败！');
        }
    }


    /**
     * 操作分类初始化
     * @param string $type
     */
    public function operate($type = 'move'){
        //检查操作参数
        if(strcmp($type, 'move') == 0){
            $operate = '移动';
        }elseif(strcmp($type, 'merge') == 0){
            $operate = '合并';
        }else{
            $this->error('参数错误！');
        }
        $from = intval($this->request->param('from'));
        empty($from) &&  $this->error('参数错误！');
        //获取分类
        $map = [['status','=',1], ['id','<>',$from]];
        $list = Db::name('Category')->where($map)->field('id,pid,title')->order('pid asc,id asc,sort asc')->select();
        $Tree = new \tree\Tree;
        $this->assign('type', $type);
        $this->assign('from', $from);
        $this->assign('operate', $operate);
        $this->assign('title',Db::name('Category')->where('id',$from)->value('title'));
        $this->assign('list', $Tree->tree($list));
        $this->assign('meta_title',$operate.'分类');
        return $this->fetch();
    }

    /**
     * 移动分类
     */
    public function move(){
        $to = $this->request->post('to');
        $from = $this->request->post('from');
        $res = Db::name('Category')->where(['id'=>$from])->setField('pid', $to);
        if($res !== false){
            $this->success('分类移动成功！');
        }else{
            $this->error('分类移动失败！');
        }
    }

    /**
     * 合并分类
     */
    public function merge(){
        $to = $this->request->post('to');
        $from = $this->request->post('from');
        //合并文档
        $res = Db::name('Novel')->where(['category'=>$from])->setField('category', $to);
        if($res){
            //删除被合并的分类
            Db::name('Category')->delete($from);
            $this->success('合并分类成功！');
        }else{
            $this->error('合并分类失败！');
        }

    }

    public function status(){
        $id = $this->request->param('id');
        $Category=(new CategoryModel());
        $info = $Category->info($id);
        if($info['status']==1){
            return $this->forbid('Category');
        }else{
            return $this->resume('Category');
        }
    }
}