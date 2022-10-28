<?php


namespace app\model;
use think\facade\Db;
use think\Model;
use think\facade\Request;
use app\admin\validate\UserGroup as UserGroupValidate;

class UserGroup extends Model{

    protected $name = 'user_group';
    // 设置json类型字段
    protected $json = ['json'];
    
    // 设置JSON数据返回数组
    protected $jsonAssoc = true;

	public function info($id){
		$map['id'] = $id;
    	$info=UserGroup::where($map)->find();
		return $info;
	}

    public function lists(){
        $list=UserGroup::paginate(config('web.list_rows'))->each(function($item, $key){
            $item->count = Db::name('user')->where('exp','between',[$item->exp_min,$item->exp_max])->count('id');
        });
        return $list;
    }

	public function edit(){
        $data=Request::post();
        if(empty($data['id'])){
            $result = UserGroup::create($data);
        }else{
            $result = UserGroup::update($data);
        }

        return $result;
    }

    public function del($id){
        $map = ['id' => $id];
        $result = UserGroup::where($map)->delete();
        return $result;
    }
}