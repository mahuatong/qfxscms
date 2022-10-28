<?php
namespace app\admin\validate;
use think\Validate;

class UserGroup extends Validate{
	protected $rule =   [
        'name'  => 'require|max:12',
        'exp_min' => 'number',
        'exp_max' => 'number|egt:exp_min',
        'json' => 'checkRuleJson:true',
    ];

    protected $message  =   [
        'name.require' => '请先填写用户组名称',
        'name.max' => '用户组名称长度不要超过12个字符',
        'exp_min.number' => '请正确填写经验所处(最低)',
        'exp_max.number' => '请正确填写经验所处(最高)',
        'exp_max.egt' => '经验所处(最高)必须大于经验所处(最低)',
    ];

    protected function checkRuleJson($value,$rule,$data=[]){
        if(!is_numeric($value['comment_exp'])){
            return '评论经验只能是数字';
        }
        if(!is_numeric($value['comment_integral'])){
            return '评论经验只能是数字';
        }
        if(!is_numeric($value['bookshelf_num'])){
            return '评论经验只能是数字';
        }
        if(!is_numeric($value['reader_exp'])){
            return '评论经验只能是数字';
        }
        if(!is_numeric($value['reader_integral'])){
            return '阅读积分只能是数字';
        }
        return true;
    }
}