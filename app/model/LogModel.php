<?php
namespace app\model;
use think\Model;

class LogModel extends Model{

    protected $name = 'member_log';

    public function getMemberNameAttr($value,$data){
        $MemberModel=(new MemberModel());
        $member=$MemberModel->info($data['member_id']);
        return $member['username'];
    }

    public function lists(){
        $list = LogModel::order('id desc')->paginate(config('web.list_rows'));
        return $list;
    }

    public function del(){
        $week = strtotime(date("Y-m-d H:i:s", strtotime("-1 week")));
        $map[] = ['time' ,'<', $week];
        $result = LogModel::where($map)->delete();
        if(false === $result){
            $this->error=LogModel::getError();
            return false;
        }else{
            return $result;
        }
    }
}