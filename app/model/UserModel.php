<?php


namespace app\model;
use think\facade\Validate;
use think\Model;
use think\facade\Db;
use think\facade\Config;
use think\facade\Request;
use org\File;
use app\admin\validate\User as UserValidate;

class UserModel extends Model{

    protected $name = 'user';
    protected $autoWriteTimestamp = true;

	public function info($id){
		$map['id'] = $id;
    	$info=UserModel::where($map)->find();
		return $info;
	}
    public function get_info($id=UID,$field = true){
        $map=['status'=>1,'id'=>$id];
        $data=UserModel::where($map)->field($field)->withAttr('headimgurl', function($value, $data) {
            if($this->api_url){
                $validate = Validate::checkRule($value,'url');
                if(!$validate){
                    return Request::domain().$value;
                }else{
                    return $value;
                }
            }
            return $value;
        })->find();
        if(isset($data['exp'])){
            $user_group = Db::name('user_group')->where([['exp_min','<=',$data['exp']],['exp_max','>=',$data['exp']],['status','=',1]])->field('name,json')->find();
            $data['group'] = $user_group['name'];
            $data['json'] = json_decode($user_group['json'], true);
        }
        return $data;
    }
    public function lists(){
        $map=[];
        $kerword = Request::param('keywords');
        if($kerword){
            $map[]  = ['username','like','%'.$kerword.'%'];
        }
        $status=Request::param('status');
        if(isset($status)){
            $map[] = ['status','=',$status];
        }
        $list=UserModel::where($map)->order('id desc')->paginate(['list_rows'=>Config::get('web.list_rows'),'query'=>['keywords'=>$kerword]])->each(function($item, $key){
            $item->group = Db::name('user_group')->where([['exp_min','<=',$item->exp],['exp_max','>=',$item->exp],['status','=',1]])->value('name');
        });
        return $list;
    }

	public function edit($data){
        if(empty($data['id'])){
            $data['password']=think_ucenter_md5($data['password']);
            $result = UserModel::create($data);
        }else{
            if(!empty($data['password'])){
                $data['password']=think_ucenter_md5($data['password']);
            }
            $result = UserModel::update($data);
        }
        return $result;
    }

    public function del($id){
        $map = ['id' => $id];
        $pic = UserModel::where($map)->column('headimgurl');
        foreach ($pic as $value) {
            if(strpos($value, 'user-icon.png')===false){
                File::unlink(".".$value);
            }
        }
        $result = UserModel::where($map)->delete();
        if(false === $result){
            $this->error=UserModel::getError();
            return false;
        }else{
            Db::name('comment')->where(['uid'=>$id])->delete();
            Db::name('bookshelf')->where(['user_id'=>$id])->delete();
            return $result;
        }
    }
}