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
namespace org;
// 本地文件写入存储类
class File{

    static private $contents=[];
    /**
     * 架构函数
     * @access public
     */
    public function __construct() {
    }

    /**
     * 文件内容读取
     * @access public
     * @param string $filename  文件名
     * @return string     
     */
    static public function read($filename,$type=''){
        return self::get($filename,'content',$type);
    }

    /**
     * 文件写入
     * @access public
     * @param string $filename  文件名
     * @param string $content  文件内容
     * @return boolean         
     */
    static public function put($filename,$content,$type=''){
        $dir   =  dirname($filename);
        if(!is_dir($dir))
            mkdir($dir,0755,true);
        if(false === file_put_contents($filename,$content)){
            throw new \think\Exception('文件写入错误:'.$filename);
        }else{
            self::$contents[$filename]=$content;
            return true;
        }
    }

    /**
     * 文件追加写入
     * @access public
     * @param string $filename  文件名
     * @param string $content  追加的文件内容
     * @return boolean        
     */
    static public function append($filename,$content,$type=''){
        if(is_file($filename)){
            $content =  self::read($filename,$type).$content;
        }
        return self::put($filename,$content,$type);
    }

    /**
     * 加载文件
     * @access public
     * @param string $filename  文件名
     * @param array $vars  传入变量
     * @return void        
     */
    static public function load($_filename,$vars=null){
        if(!is_null($vars))
            extract($vars, EXTR_OVERWRITE);
        include $_filename;
    }

    /**
     * 文件是否存在
     * @access public
     * @param string $filename  文件名
     * @return boolean     
     */
    static public function has($filename,$type=''){
        return is_file($filename);
    }

    /**
     * 文件删除
     * @access public
     * @param string $filename  文件名
     * @return boolean     
     */
    static public function unlink($filename,$type=''){
        unset(self::$contents[$filename]);
        return is_file($filename) ? unlink($filename) : false; 
    }

    /**
     * 读取文件信息
     * @access public
     * @param string $filename  文件名
     * @param string $name  信息名 mtime或者content
     * @return boolean     
     */
    static public function get($filename,$name,$type=''){
        if(!isset(self::$contents[$filename])){
            if(!is_file($filename)) return false;
           self::$contents[$filename]=file_get_contents($filename);
        }
        $content=self::$contents[$filename];
        $info   =   array(
            'mtime'     =>  filemtime($filename),
            'content'   =>  $content
        );
        return $info[$name];
    }

        /**
     * 获取指定路径下的信息
     * @param string $dir 路径
     * @return ArrayObject
     */
    static public function get_dir_info($dir){
        $handle = @opendir($dir);//打开指定目录
        $directory_count = 0;
        $total_size = 0;
        $file_cout = 0;
        while (FALSE !== ($file_path = readdir($handle))){
            if($file_path != "." && $file_path != ".."){
                $next_path = $dir.'/'.$file_path;
                if (is_dir($next_path)){
                    $directory_count++;
                    $result_value = self::get_dir_info($next_path);
                    $total_size += $result_value['size'];
                    $file_cout += $result_value['filecount'];
                    $directory_count += $result_value['dircount'];
                }elseif (is_file($next_path)){
                    $total_size += filesize($next_path);
                    $file_cout++;
                }
            }   
        }
        closedir($handle);//关闭指定目录
        $result_value['size'] = $total_size;
        $result_value['filecount'] = $file_cout;
        $result_value['dircount'] = $directory_count;
        return $result_value;
    }

    /**
     * 列出指定目录下符合条件的文件和文件夹
     * @param string $dirname 路径
     * @param boolean $is_all 是否列出子目录中的文件
     * @param string $exts 需要列出的后缀名文件
     * @param string $sort 数组排序
     * @return ArrayObject
     */
    static public function list_dir_info($dirname,$is_all=FALSE,$exts='',$sort='ASC'){
        //处理多于的/号
        $new = strrev($dirname);
        if(strpos($new,'/')==0){
            $new = substr($new,1);
        }
        $dirname = strrev($new);
        $sort = strtolower($sort);//将字符转换成小写
        $files = array();
        $subfiles = array();
        if(is_dir($dirname)){
            $fh = opendir($dirname);
            while (($file = readdir($fh)) !== FALSE){
                if (strcmp($file, '.')==0 || strcmp($file, '..')==0) continue;
                $filepath = $dirname.'/'.$file;
                switch ($exts){
                    case '*':
                        if (is_dir($filepath) && $is_all==TRUE){
                            $files = array_merge($files,self::list_dir_info($filepath,$is_all,$exts,$sort));
                        }
                        array_push($files,$filepath);
                        break;
                    case 'folder':
                        if (is_dir($filepath) && $is_all==TRUE){
                            $files = array_merge($files,self::list_dir_info($filepath,$is_all,$exts,$sort));
                            array_push($files,$filepath);
                        }elseif(is_dir($filepath)){
                            array_push($files,$filepath);
                        }
                        break;
                    case 'file':
                        if (is_dir($filepath) && $is_all==TRUE){
                            $files = array_merge($files,self::list_dir_info($filepath,$is_all,$exts,$sort));
                        }elseif(is_file($filepath)){
                            array_push($files, $filepath);
                        }
                        break;
                    default:
                        if (is_dir($filepath) && $is_all==TRUE){
                            $files = array_merge($files,self::list_dir_info($filepath,$is_all,$exts,$sort));
                        }elseif(preg_match("/\.($exts)/i",$filepath) && is_file($filepath)){
                            array_push($files, $filepath);
                        }
                        break;
                }
                switch ($sort){
                    case 'asc':
                        sort($files);
                        break;
                    case 'desc':
                        rsort($files);
                        break;
                    case 'nat':
                        natcasesort($files);
                        break;
                }
            }
            closedir($fh);
            return $files;
        }else{
            return FALSE;
        }
    }
        /**
     * 返回指定文件和目录的信息
     * @param string $file
     * @return ArrayObject
     */
    static public function list_info($file){
        $dir = array();
        $dir['filename']   = basename($file);//返回路径中的文件名部分。
        $dir['pathname']   = realpath($file);//返回绝对路径名。
        $dir['owner']      = fileowner($file);//文件的 user ID （所有者）。
        $dir['perms']      = fileperms($file);//返回文件的 inode 编号。
        $dir['inode']      = fileinode($file);//返回文件的 inode 编号。
        $dir['group']      = filegroup($file);//返回文件的组 ID。
        $dir['path']       = dirname($file);//返回路径中的目录名称部分。
        $dir['atime']      = fileatime($file);//返回文件的上次访问时间。
        $dir['ctime']      = filectime($file);//返回文件的上次改变时间。
        $dir['perms']      = fileperms($file);//返回文件的权限。 
        $dir['size']       = filesize($file);//返回文件大小。
        $dir['type']       = filetype($file);//返回文件类型。
        $dir['ext']        = is_file($file) ? pathinfo($file,PATHINFO_EXTENSION) : '';//返回文件后缀名
        $dir['mtime']      = filemtime($file);//返回文件的上次修改时间。
        $dir['isDir']      = is_dir($file);//判断指定的文件名是否是一个目录。
        $dir['isFile']     = is_file($file);//判断指定文件是否为常规的文件。
        $dir['isLink']     = is_link($file);//判断指定的文件是否是连接。
        $dir['isReadable'] = is_readable($file);//判断文件是否可读。
        $dir['isWritable'] = is_writable($file);//判断文件是否可写。
        $dir['isUpload']   = is_uploaded_file($file);//判断文件是否是通过 HTTP POST 上传的。
        return $dir;
    }
}
