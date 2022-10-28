<?php


namespace app\admin\controller;
use app\model\MenuModel;
use app\service\listener\CollectThread;
use think\facade\Db;
use think\facade\Config;
use captcha\Captcha;

class Index extends Base
{
    public function test(){
        var_dump((new CollectThread())->collect_list_get(1));
    }
    public function index()
    {
    	$this->assign('menu', (new MenuModel())->getMenus());
        return $this->fetch();
    }

    public function welcome()
    {

        $count['novel_count']=Db::name('novel')->count('id');
        $count['novel_today_count']=Db::name('novel')->whereTime('create_time', 'd')->count('id');
        $count['novel_today_count_update']=Db::name('novel')->whereTime('update_time', 'd')->count('id');
        $count['news_count']=Db::name('news')->count('id');
        $count['news_today_count']=Db::name('news')->whereTime('create_time', 'd')->count('id');
        $count['news_today_count_update']=Db::name('news')->whereTime('update_time', 'd')->count('id');
        $count['user_count']=Db::name('user')->count('id');
        $count['user_today_count']=Db::name('user')->whereTime('create_time', 'd')->count('id');
        $count['user_today_count_update']=Db::name('user')->whereTime('update_time', 'd')->count('id');
        $count['comment_count']=Db::name('comment')->count('id');

        $count['comment_today_count']=Db::name('comment')->whereTime('create_time', 'd')->count('id');

        $novle=(new \app\model\CommonApi())->get_novel('','update_time desc',17,'','','','','','');

        $comment = (new \app\model\CommentModel())->lists(['novel','news'],null,5)->toArray();

        $comment = (new \app\model\CommentModel())->get_tree($comment['data']);

        $this->assign('web',['version'=>'1.0.0']);
        $this->assign('count',$count);
        $this->assign('novle',$novle);
        $this->assign('comment',$comment);
    	return $this->fetch();
    }

    public function login($username = null, $password = null, $code = null)
    {
        if($this->request->isPost()){
//        	$captcha = new Captcha();
//			if(!$captcha->check($code)){
//				$this->error('验证码错误！');
//			}
            $uid = (new \app\model\Index())->login($username, $password);
            if(0 < $uid){
                $this->success('登录成功！', url('admin/index/index'));
            } else { //登录失败
                switch($uid) {
                    case -1: $error = '用户不存在或被禁用！'; break; //系统级别禁用
                    case -2: $error = '密码错误！'; break;
                    default: $error = '未知错误！'; break;
                }
                $this->error($error);
            }
        } else {
            if(is_login('admin')){
                $this->redirect('index/index');
            }else{
                return $this->fetch();
            }
        }
    }
	
	/* 退出登录 */
    public function logout()
    {
        if(is_login('admin')){
            (new \app\model\Index())->logout();
            session('[destroy]');
            $this->success('退出成功！', url('admin/index/login'));
        } else {
            $this->redirect('admin/index/login');
        }
    }

    public function verify()
    {
    	$captcha = new Captcha();
        return $captcha->entry();
    }
}