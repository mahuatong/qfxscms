<?php

function getModule(){
    return strtolower(app('http')->getName());
}
function exception($msg, $code = 0, $exception = '')
{
    $e = $exception ?: '\think\Exception';
    throw new $e($msg, $code);
}
/**
 * 格式化字节大小
 * @param  number $size      字节数
 * @param  string $delimiter 数字和单位分隔符
 * @return string            格式化后的带单位的大小
 */
function format_bytes($size, $delimiter = '') {
    $units = array('B', 'KB', 'MB', 'GB', 'TB', 'PB');
    for ($i = 0; $size >= 1024 && $i < 5; $i++) $size /= 1024;
    return round($size, 2) . $delimiter . $units[$i];
}

// 分析枚举类型配置值 格式 a:名称1,b:名称2
function parse_config_attr($string) {
    $array = preg_split('/[,;\r\n]+/', trim($string, ",;\r\n"));
    if(strpos($string,':')){
        $value  =   array();
        foreach ($array as $val) {
            list($k, $v) = explode(':', $val);
            $value[$k]   = $v;
        }
    }else{
        $value  =   $array;
    }
    return $value;
}

//分类列表
function get_tree($type=''){
    $map['status'] = 1;
    $map['type'] = $type;
    $list =  \think\facade\Db::name("Category")->where($map)->field('id,pid,title')->order('pid asc,sort asc')->select();
    $Tree = new \tree\Tree;
    $Tree::$treeList = [];
    return $Tree->tree($list);
}

/**
 * 检查$pos(推荐位的值)是否包含指定推荐位$contain
 * @param number $pos 推荐位的值
 * @param number $contain 指定推荐位
 * @return boolean true 包含 ， false 不包含
 */
function check_document_position($pos = 0, $contain = 0){
    if(empty($pos) || empty($contain)){
        return false;
    }
    //将两个参数进行按位与运算，不为0则表示$contain属于$pos
    $res = $pos & $contain;
    if($res !== 0){
        return true;
    }else{
        return false;
    }
}

//检测版本号
function tonum($upversion){
    $upversion=trim(str_replace(".","",$upversion));
    if($upversion){
        return $upversion;
    }
}

/**
 * 检测用户是否登录
 * @return integer 0-未登录，大于0-当前登录用户ID
 */
function is_login($type='user'){
    if($type=='user'){
        $user = cookie($type.'_auth');
        $user_sign = cookie($type.'_auth_sign');
    }else{
        $user = session($type.'_auth');
        $user_sign = session($type.'_auth_sign');
    }
    if (empty($user)){
        return 0;
    } else {
        return $user_sign == data_auth_sign($user) ? $user['uid'] : 0;
    }
}

/**
 * 数据签名认证
 * @param  array  $data 被认证的数据
 * @return string       签名
 */
function data_auth_sign($data) {
    //数据类型检测
    if(!is_array($data)){
        $data = (array)$data;
    }
    ksort($data); //排序
    $code = http_build_query($data); //url编码并生成query字符串
    $sign = sha1($code); //生成签名
    return $sign;
}

function think_ucenter_md5($str, $key = 'HXyiyuanhuanlego'){
    return '' === $str ? '' : md5(sha1($str) . $key);
}

/**
 * 获取数据库中的配置列表
 * @return array 配置数组
 */
function config_lists(){
    $data   = \think\facade\Db::name('config')->field('type,name,value')->select()->toArray();
    $config = [];
    if($data && is_array($data)){
        foreach ($data as $value) {
            if($value['name']==='official_url' && is_ssl()){
                $config[$value['name']] = str_replace('http:','https:',config_parse($value['type'], $value['value']));
            }else{
                $config[$value['name']] = config_parse($value['type'], $value['value']);
            }

        }
    }
    return $config;
}


/**
 * 根据配置类型解析配置
 * @param  integer $type  配置类型
 * @param  string  $value 配置值
 */
function config_parse($type, $value){

    switch ($type) {
        case 3: //解析数组
            $array = preg_split('/[\r\n]+/', trim($value, "\r\n"));
            if(strpos($value,':')===false){
                $value = $array;
            }else{
                $value  = [];
                foreach ($array as $val) {
                    list($k, $v) = explode(':', $val);
                    $value[$k]   = $v;
                }
            }
            break;
    }
    return $value;
}

function time_format($time = NULL,$format='Y-m-d H:i',$type=0){
    $time = $time === NULL ? time() : intval($time);
    if(empty($type)){
        return date($format, $time);
    }else{
        $current_time=time();
        $span=$current_time-$time;
        if($span<60){
            return "刚刚";
        }else if($span<3600){
            return intval($span/60)."分钟前";
        }else if($span<24*3600){
            return intval($span/3600)."小时前";
        }else if($span<(7*24*3600)){
            return intval($span/(24*3600))."天前";
        }else{
            return date($format,$time);
        }
    }
}

/**
 * 获取插件类的类名
 * @param strng $name 插件名
 */
function get_addon_class($name){
    $class = "addons\\{$name}\\{$name}";
    return $class;
}

/**
 * 把返回的数据集转换成Tree
 * @param array $list 要转换的数据集
 * @param string $pid parent标记字段
 * @param string $level level标记字段
 * @return array
 */
function list_to_tree($list, $pk='id', $pid = 'pid', $child = '_child', $root = 0) {
    // 创建Tree
    $tree = [];
    if(is_array($list)) {
        // 创建基于主键的数组引用
        $refer = [];
        foreach ($list as $key => $data) {
            $refer[$data[$pk]] =& $list[$key];
        }
        foreach ($list as $key => $data) {
            // 判断是否存在parent
            $parentId =  $data[$pid];
            if ($root == $parentId) {
                $tree[] =& $list[$key];
            }else{
                if (isset($refer[$parentId])) {
                    $parent =& $refer[$parentId];
                    $parent[$child][] =& $list[$key];
                }
            }
        }
    }
    return $tree;
}

/**
 * 将list_to_tree的树还原成列表
 * @param  array $tree  原来的树
 * @param  string $child 孩子节点的键
 * @param  string $order 排序显示的键，一般是主键 升序排列
 * @param  array  $list  过渡用的中间数组，
 * @return array        返回排过序的列表数组
 */
function tree_to_list($tree, $child = '_child', $order='id', &$list = []){
    if(is_array($tree)) {
        $refer = [];
        foreach ($tree as $key => $value) {
            $reffer = $value;
            if(isset($reffer[$child])){
                unset($reffer[$child]);
                tree_to_list($value[$child], $child, $order, $list);
            }
            $list[] = $reffer;
        }
        $list = list_sort_by($list, $order, $sortby='asc');
    }
    return $list;
}

/**
 * 生成随机字符
 * @param  array $lenght  位数
 */
function uniqidReal($lenght = 13) {
    if (function_exists("random_bytes")) {
        $bytes = random_bytes(ceil($lenght / 2));
    } elseif (function_exists("openssl_random_pseudo_bytes")) {
        $bytes = openssl_random_pseudo_bytes(ceil($lenght / 2));
    } else {
        $bytes = uniqid(rand(10,99));
    }
    return substr(bin2hex($bytes), 0, $lenght);
}

/**
 * 检测访问域名
 */

function checkDomain($url,$ourl){
    if(strpos($url,'://') !==false){
        $url=checkScheme($url);
    }
    if(strpos($ourl,'://') !==false){
        $ourl=checkScheme($ourl);
    }
    if($url==$ourl){
        return true;
    }else{
        return false;
    }
}

function checkScheme($url){
    $url=explode('://',$url);
    return $url[1];
}

/**
 * 判断是否SSL协议
 */

function is_ssl() {
    if(isset($_SERVER['HTTPS']) && ('1' == $_SERVER['HTTPS'] || 'on' == strtolower($_SERVER['HTTPS']))){
        return true;
    }elseif(isset($_SERVER['SERVER_PORT']) && ('443' == $_SERVER['SERVER_PORT'] )) {
        //https使用端口443
        return true;
    }
    return false;
}

//清除缓存
function rm_cache($id=NUll,$type='novel',$all=true){
    if(config('web.data_cache')){
        $options = ['cache_subdir' => false];
        if($all){
            $options['path']=env('runtime_path').'data'.DIRECTORY_SEPARATOR.'index'.DIRECTORY_SEPARATOR;
//            cache($options)->rm('home_index_index');
            $options['path']=env('runtime_path').'data'.DIRECTORY_SEPARATOR.'lists'.DIRECTORY_SEPARATOR;
//            cache($options)->rm('home_lists_index');
//            cache($options)->rm('home_lists_lists');
        }
        if(!is_null($id)){
            $options['path']=env('runtime_path').'data'.DIRECTORY_SEPARATOR.$type.DIRECTORY_SEPARATOR;
//            cache($options)->rm('home_'.$type.'_index_'.$id);
        }
    }
}

/**
 * 删除目录及目录下所有文件或删除指定文件
 * @param str $path   待删除目录路径
 * @param int $delDir 是否删除目录，1或true删除目录，0或false则只删除文件保留目录（包含子目录）
 * @return bool 返回删除状态
 */
function del_dir_file($path, $delDir = FALSE) {
    if(is_dir($path)){
        $handle = opendir($path);
        if ($handle) {
            while (false !== ( $item = readdir($handle) )) {
                if ($item != "." && $item != "..")
                    is_dir("$path/$item") ? del_dir_file("$path/$item", $delDir) : unlink("$path/$item");
            }
            closedir($handle);
            if ($delDir)
                return rmdir($path);
        }else {
            if (file_exists($path)) {
                return unlink($path);
            } else {
                return FALSE;
            }
        }
    }
}