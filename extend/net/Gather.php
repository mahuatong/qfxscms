<?php
// +----------------------------------------------------------------------
// | KyxsCMS [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2018~2019 http://www.kyxscms.com All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: kyxscms
// +----------------------------------------------------------------------
namespace net;

use app\admin\controller\Log;
use think\facade\Db;
use think\facade\Request;
use think\facade\Config;
use think\facade\Cache;
use think\facade\Validate;
use net\Http;
use org\File;

class Gather
{
    static protected $sign_match = '\[内容(?P<num>\d*)\]';
    static protected $echo_msg_head;

    //自动编码转换
    static public function auto_convert2utf8($str,$encode) {
        if(empty($encode) || $encode=='auto'){
            $encode = mb_detect_encoding($str, array('ASCII', 'UTF-8', 'GB2312', 'GBK', 'BIG5'));
        }
        if (strcasecmp($encode, 'utf-8') !== 0) {
            $str = iconv($encode, 'utf-8//IGNORE', $str);
        }
        return $str;
    }

    //获取数据
    static public function get_html($url,$encode,$url_complete){
        $proxy = '';
        $map = ['status'=>1,'group'=>'proxy'];
        $addons_name = Db::name('Addons')->where($map)->value('name');
        if($addons_name){
            $addons_class = get_addon_class($addons_name);
            if(class_exists($addons_class)){
                $addon = new $addons_class();
                $proxy = $addon->$addons_name();
            }
        }
        $html=Http::doGet($url,60,'',$proxy);
        if($html==false){
            return $html;
        }
        $collect_sleep=Config::get('web.collect_sleep');
        if(!empty($collect_sleep)){
            sleep($collect_sleep);
        }
        if($url_complete){
            $html=self::url_complete($html,$url);
        }
        return self::auto_convert2utf8($html,$encode);
    }

    //生成起始地址
	static public function convert_source_url($url) {
		$urls = [];
        $url_array=json_decode($url,true);
        foreach ($url_array as $key => $value) {
        	switch ($value['type']) {
        		case 1:
        			if ($value['param'][3]) {
        				for ($i = $value['param'][1];$i >= $value['param'][0];$i--) {
	                        $urls[]=str_replace('[内容]',$value['param'][0]+($i-$value['param'][0])*$value['param'][2],$value['url']);
	                    }
        			}else{
	        			for ($i = $value['param'][0];$i <= $value['param'][1];$i++) {
	                        $urls[]=str_replace('[内容]',$value['param'][0]+($i-$value['param'][0])*$value['param'][2],$value['url']);
	                    }
                	}
        			break;
        		case 2:
        			$urls=array_merge($urls,explode(chr(10), $value['url']));
        			break;
        		default:
        			$urls[]=$value['url'];
        			break;
        	}
        }
        return $urls;
    }

    //列表正则
    static public function convert_sign_match($str) {
        $str = preg_replace_callback('/(\={0,1})(\s*)([\'\"]{0,1})' . self::$sign_match . '\3/', function ($matches) {
            $ruleStr = $matches[1] . $matches[2] . $matches[3] . '(?P<match' . $matches['num'] . '>';
            if (!empty($matches[1]) && !empty($matches[3])) {
                $ruleStr.= '[^\<\>]*?)';
            } else {
                $ruleStr.= '[\s\S]*?)';
            }
            $ruleStr.= $matches[3];
            return $ruleStr;
        }, $str);
        $str = preg_replace('/\\\*([\'\/])/', "\\\\$1", $str);
        $str = str_replace('(*)', '[\s\S]*?', $str);
        return $str;
    }

    //地址拼接
    static public function set_merge_default($reg, $merge) {
        if (empty($merge)) {
            $merge = '';
            if (!empty($reg)) {
                if (preg_match_all('/\<match(?P<num>\d*)\>/i', $reg, $match_signs)) {
                    foreach ($match_signs['num'] as $snum) {
                        $merge.= '[内容'.$snum.']';
                    }
                }
            }
        }
        return $merge;
    }

    // 获取指定标记中的内容
    static public function get_section_data($str, $section){
        if(!$section || !$str){
            return $str;
        }
        $section = explode('[内容]', $section);
        if (empty($section[0]) || empty($section[1])){
            return $str;
        }
        $str = explode($section[0], $str);
        if(empty($str[0]) || empty($str[1])){
            return $str[0];
        }
        $str = explode($section[1], $str[1]);
        return $str[0];
    }

    //规则采集
    static public function field_rule($field_params, $html, $is_loop = false) {
        $field_params['rule'] = self::convert_sign_match($field_params['rule']);
        $field_params['merge'] = self::set_merge_default($field_params['rule'],$field_params['merge']);
        if(!empty($field_params['chapter'])){
            $field_params['rule']=str_replace('[章节标题]','(?P<title>[\s\S]*?)',$field_params['rule']);
        }
        if (!empty($field_params['rule']) && preg_match_all('/' . self::$sign_match . '/i', $field_params['merge'], $match_signs)) {
            if ($is_loop) {
                preg_match_all('/' . $field_params['rule'] . '/i', $html, $match_conts, PREG_SET_ORDER);
            } else {
                if (preg_match('/' . $field_params['rule'] . '/i', $html, $match_cont)) {
                    $match_conts = [$match_cont];
                }
            }
            $curI = 0;
            if(!empty($match_conts)){
                foreach ($match_conts as $match_cont) {
                    $curI++;
                    $re_match = [];
                    foreach ($match_signs['num'] as $ms_k => $ms_v) {
                        $re_match[$ms_k] = $match_cont['match' . $ms_v];
                    }
                    $contVal = str_replace($match_signs[0], $re_match, $field_params['merge']);
                    if(!empty($field_params['strip'])){
                        if(strpos($field_params['strip'],'all') !== false){
                            $contVal = self::strip_tags_content($contVal,'style,script,object');
                            $contVal = strip_tags($contVal);
                        } else {
                            $contVal = self::strip_tags_content($contVal,$field_params['strip']);
                        }
                    }
                    if(!empty($field_params['replace'])){
                        if(!is_array($field_params['replace'])){
                            $field_params['replace']=json_decode($field_params['replace'],true);
                        }
                        foreach ($field_params['replace'] as $key => $value) {
                            $contVal = str_replace($value['find'], $value['replaces'], $contVal);
                        }
                    }
                    if ($is_loop) {
                        if(!empty($field_params['chapter'])){
                            $val[] = ['title'=>$match_cont['title'],'url'=>$contVal];
                        }else{
                            $val[] = trim($contVal);
                        }
                    } else {
                        $val= trim($contVal);
                    }
                }
                return $val;
            }
        }
    }

    //字段内容
    static public function field_content($info,$url,$test=['state'=>false],$field=[],$page='default'){
        $data=self::has_url($url,$info);
        if($data && $test['state']===false){
            $return=['code'=>true,'title'=>$data['title'],'msg'=>'已存在','reurl'=>$url,'status'=>'ok'];
            return $return;
        }
        $html=self::get_html($url,$info['charset'],$info['url_complete']);
        if(!$html){
            if($test['state']===false){
                return ['error'=>true,'msg'=>'无法获取页面','url'=>$url];
            }else{
                self::echo_msg('无法获取页面:url['.$url.']');
                return false;
            }
        }
        foreach ($info['rule'] as $rule_key => $rule_value){
            if($rule_value['source']===$page){
                if($test['state']===false){
                    switch ($rule_value['field']) {
                        case 'category':
                            if($info['category_way']===1){
                                $field[$rule_value['field']]=$info['category_fixed'];
                            }else{
                                $category_mb=self::field_rule($rule_value,$html);
                                if(empty($category_mb)){
                                    $return=['code'=>true,'title'=>'','msg'=>'未获取到栏目','reurl'=>$url,'status'=>'error'];
                                    return $return;
                                }
                                if(empty($info['category_equivalents'])){
                                    $return=['code'=>true,'title'=>'','msg'=>'栏目规则-栏目转换不能为空','reurl'=>$url,'status'=>'error'];
                                    return $return;
                                }
                                $category=self::category_equivalents($info['category_equivalents'],$category_mb);
                                $field[$rule_value['field']]=$category;
                                if(empty($category)){
                                    $return=['code'=>true,'title'=>'','msg'=>'获取对应栏目出错--'.$category_mb,'reurl'=>$url,'status'=>'error'];
                                    return $return;
                                }
                            }
                            $field['reurl']=$url;
                            break;
                        case 'pic':
                            if(empty($field['id'])){
                                $pic=self::field_rule($rule_value,$html);
                                if($info['pic_local']==1){
                                    $pic = self::down_img($pic,$info['type']);
                                }
                                $field[$rule_value['field']]=$pic;
                            }
                            break;
                        case 'serialize':
                            $serialize=self::field_rule($rule_value,$html);
                            if($rule_value['serial']==$serialize){
                                $field[$rule_value['field']]=0;
                            }elseif($rule_value['over']==$serialize){
                                $field[$rule_value['field']]=1;
                            }else{
                                $field[$rule_value['field']]=0;
                            }
                            break;
                        default:
                            $field[$rule_value['field']]=self::field_rule($rule_value,$html);
                            $data=self::has_title($info,$field);
                            if($data){
                                if(empty($info['update'])){
                                    $return=['code'=>true,'title'=>$data['title'],'msg'=>'已存在','reurl'=>$url,'status'=>'ok'];
                                    return $return;
                                }else{
                                   $field['id']=$data['id']; 
                                }
                            }
                            break;
                    }
                }else{
                    if($rule_value['field']===$test['field']){
                        self::echo_msg('获取页面:url['.$url.']');
                        $test_value=self::field_rule($rule_value,$html);
                        if(empty($test_value)){
                            self::echo_msg('未获取到内容，请检测规则！');
                            self::echo_msg('<pre class="layui-code" lay-title="页面代码" lay-height="500px">'.htmlentities($html).'</pre><script>layui.use("code", function(){layui.code();});</script>');
                        }else{
                            self::echo_msg('获取结果:['.$test_value.']');
                        }
                        return false;
                    }
                }
            }
        }
        $relation=json_decode($info['relation_url'],true);
        if($relation){
            foreach ($relation as $key => $value) {
                if($value['page']===$page){
                    if(!empty($value['section'])){
                        $html=self::get_section_data($html,$value['section']);
                    }
                    $relation_list=self::field_rule(['rule'=>$value['url_rule'],'merge'=>$value['url_merge'],'chapter'=>$value['chapter']],$html,true);
                    if(empty($relation_list)){
                        if($test['state']===false){
                            return ['code'=>true,'title'=>$field['title'],'msg'=>'获取关联页面出错','reurl'=>$url,'status'=>'error'];
                        }else{
                            self::echo_msg('获取关联页面出错--'.$value['title'].':url['.$url.']');
                            self::echo_msg('<pre class="layui-code" lay-title="页面代码" lay-height="500px">'.htmlentities($html).'</pre><script>layui.use("code", function(){layui.code();});</script>');
                            return false;
                        }
                    }else{
                        if($value['chapter']==0){
                            $relation_list=array_unique($relation_list);
                            foreach ($relation_list as $list_key => $list_url){
                                $field=self::field_content($info,$list_url,$test,$field,strval($key));
                            }
                        }else{
                            if($test['state']===false){
                                $field['chapter_url']=$url;
                                $field['chapter']=$relation_list;
                            }else{
                                self::field_content($info,$relation_list[0]['url'],$test,$field,strval($key));
                            }
                        }
                    }
                }
            }
        }
        return $field;
    }

    //获取章节列表
    static public function get_chapter($id,$url){
        $info=Db::name('collect')->field('charset,url_complete,relation_url')->where(['id'=>$id])->find();
        $relation=json_decode($info['relation_url'],true);
        if($relation){
            $html=self::get_html($url,$info['charset'],$info['url_complete']);
            if(!$html){
                return false;
            }
            foreach ($relation as $key => $value) {
                if($value['chapter']==1){
                    if(!empty($value['section'])){
                        $html=self::get_section_data($html,$value['section']);
                    }
                    $relation_list=self::field_rule(['rule'=>$value['url_rule'],'merge'=>$value['url_merge'],'chapter'=>$value['chapter']],$html,true);
                    if(!empty($relation_list)){
                        return $relation_list;
                    }
                }
            }
        }
        return false;
    }

    //获取章节内容
    static public function get_chapter_content($id,$url){
        $field=['title'=>''];
        $info=Db::name('collect')->field('type,charset,url_complete,rule,relation_url')->where(['id'=>$id])->find();
        if($info){
            $info['rule']=json_decode($info['rule'],true);
            $info['rule']=array_intersect_key($info['rule'],['chapter_title'=>'','chapter_content'=>'']);
            $field=self::field_content($info,$url,['state'=>false],$field,$info['rule']['chapter_title']['source']);
            if(empty($field['chapter_content'])){
                return false;
            }
            return $field;
        }
        return false;
    }

    static public function category_equivalents($content,$category) {
        foreach ($content as $key => $value) {
            if($value['target']==$category){
                return $value['local'];
            }
        }
    }

    static public function has_url($url,$info) {
        $map['reurl']=$url;
        if($data=Db::name($info['type'])->where($map)->field('id,title')->find()){
            return $data;
        }
        return false;
    }

    static public function has_title($info,$field) {
        unset($field['category'],$field['reurl']);
        if($info['type']=='novel'){
            $field_array_diff=["title"=>"标题","author"=>"作者"];
        }else{
            $field_array_diff=["title"=>"标题"];
        }
        if(empty(array_diff_key($field_array_diff,$field)) && count($field)==2){
            if($data=Db::name($info['type'])->where($field)->field('id,title')->find()){ 
                return $data;
            }
            return false;
        }
    }

    //标签过滤
    static public function strip_tags_content($content, $tags) {
        $tags_special = $tags_ordinary = [];
        $tags = explode(',', $tags);
        $tags = array_unique($tags);
        foreach ($tags as $tag) {
            $tag = strtolower($tag);
            if ($tag == 'script' || $tag == 'style' || $tag == 'object') {
                $tags_special[$tag] = $tag;
            } else {
                $tags_ordinary[$tag] = $tag;
            }
        }
        if ($tags_special) {
            $content = preg_replace('/<(' . implode('|', $tags_special) . ')[^<>]*>[\s\S]*?<\/\1>/i', '', $content);
        }
        if ($tags_ordinary) {
            $content = preg_replace('/<[\/]*(' . implode('|', $tags_ordinary) . ')[^<>]*>/i', '', $content);
        }
        return $content;
    }

    //地址补全
    static public function url_complete($html,$base_url){
        $html = preg_replace_callback('/(?<=\bhref\=[\'\"])([^\'\"]*)(?=[\'\"])/i', function ($matche) use($base_url) {
            return self::create_url($matche[1], $base_url);
        }, $html);
        $html = preg_replace_callback('/(?<=\bsrc\=[\'\"])([^\'\"]*)(?=[\'\"])/i', function ($matche) use($base_url) {
            return self::create_url($matche[1], $base_url);
        }, $html);
        return $html;
    }

    /**
     * URL地址补全
     * @param string $url      需要检查的URL
     * @param string $baseurl  基本URL
     */
    static public function create_url($url, $baseurl) {
        $urlinfo = parse_url($baseurl);
        $baseurl = $urlinfo['scheme'].'://'.$urlinfo['host'].(substr($urlinfo['path'], -1, 1) === '/' ? substr($urlinfo['path'], 0, -1) : str_replace('\\', '/', dirname($urlinfo['path']))).'/';
        if (strpos($url, '://') === false  && !empty($url)) {
            if ($url[0] == '/') {
                $url = $urlinfo['scheme'].'://'.$urlinfo['host'].$url;
            } else {
                $url = $baseurl.$url;
            }
        }else{
            if(substr_count($url, '://')>1){
                $url=str_replace($urlinfo['scheme'].'://'.$urlinfo['host'],'',$url);
            }
        }
        return $url;
    }
 
    static public function down_img($url,$path){
        if(!Validate::checkRule($url,'url')){
            return false;
        }
        $extension = pathinfo($url, PATHINFO_EXTENSION);
        if(!in_array(strtolower($extension),['jpg','jpeg','png','gif','bmp','wepb'])){
            return false;
        }
        $img_name = md5($url);
        $upload_path=Config::get('web.upload_path');
        $filename =$upload_path.$path.'/'.date('Ymd',time()).'/'.$img_name.".".$extension;
        $get_file = Http::doGet($url);
        if($get_file){
            if(File::put($filename,$get_file)){
                return substr($filename,1);
            }
        }
        return false;
    }

    static public function echo_msg($str){
        if (!isset(self::$echo_msg_head)) {
            self::$echo_msg_head = true;
            header('X-Accel-Buffering:no');
            @ini_set('output_buffering', 'Off');
            echo '<style type="text/css">body{font:14px Verdana, "Helvetica Neue", helvetica, Arial, "Microsoft YaHei", sans-serif;background:#fff;}p{padding:5px;margin:0;}.layui-badge{position: relative;display: inline-block;padding: 0 6px;font-size: 12px;text-align: center;background-color: #FF5722;color: #fff;border-radius: 2px;line-height: 22px;margin-left: 10px;}.layui-bg-blue{background-color: #1E9FFF!important;}.layui-bg-green {background-color: #009688!important;}</style>';
        }
        echo '<p>'.$str.'</p>';
        echo '<script type="text/javascript">document.getElementsByTagName("body")[0].scrollTop=document.getElementsByTagName("body")[0].scrollHeight;</script>';
        ob_flush();
        flush();
    }
}
?>