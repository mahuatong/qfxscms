<?php
namespace app\admin\validate;
use think\Validate;

class Collect extends Validate{
	protected $rule =   [
        'title'  => 'require',
        'source_url' => 'require',
        'url_rule' => 'require',
        'category_way' => 'checkCategoryWay:true',
        'rule' => 'checkRuleJson:true'
    ];

    protected $message  =   [
        'title.require' => '规则名称必须填写！',
        'source_url.require'  => '采集列表必须填写！',
        'url_rule.require' => '采集列表网址规则必须填写！'
    ];

    protected $scene = [
        'novel'  =>  ['title','source_url','url_rule','category_way','rule'],
        'news' =>['title','source_url','url_rule','category_way','rule']
    ];

    protected function checkCategoryWay($value,$rule,$data=[])
    {
    	if($value==1){
    		return empty($data['category_fixed'])?'请选择固定分类':true;
    	}else{
    		return empty($data['category_equivalents'])?'栏目转换必须填写':true;
    	}
    }

    protected function checkRuleJson($value,$rule,$data=[])
    {
        $field=new ("app\model\CollectModel")->field();
        if(!is_array($value)){
            $value=json_decode($value,true);
        }
        foreach ($value as $k => $v) {
            if(empty($v['rule']) && $v['field']!='tag'){
                switch ($v['field']) {
                    case 'category':
                        if($data['category_way']==0){
                            return $field['field'][$data['type']][$v['field']].'规则不能为空！';
                        }
                        break;
                    default:
                        return $field['field'][$data['type']][$v['field']].'规则不能为空！';
                        break;
                }
                
            }
            if(empty($v['serial']) && $data['type']=='novel' && $v['field']=='serialize'){
                return '连载规则连载字符不能为空！';
            }
            if(empty($v['over']) && $data['type']=='novel'  && $v['field']=='serialize'){
                return '连载规则完结字符不能为空！';
            }
        }
        return true;
    }
}