<?php


namespace app\admin\controller;

class Upload extends Base 
{
    public function pic(){
        $file = $this->request->file('file');
        $savename = \think\facade\Filesystem::putFile( '', $file);
        $file_path = DIRECTORY_SEPARATOR.'public'.DIRECTORY_SEPARATOR.'storage'.DIRECTORY_SEPARATOR.$savename;
        if($savename){
            $this->success('上传成功！','',['path'=>$file_path]);
        }else{
            $this->error('操作失败');
        }
    }

    public function file(){
        $file = $this->request->file('file');
        $info = $file->validate(['ext'=>'txt,html,zip,josn,mp3,wma,wav,amr,mp4,apk,ipa','type'=>'text/plain,text/html,application/zip,application/json,audio/mpeg,audio/x-ms-wma,audio/x-wav,audio/amr,video/mp4,application/java-archive,application/octet-stream.ipa'])->move(config('web.upload_path').$this->request->param('path'));
        if($info){
            $this->success('上传成功！','',['path'=>substr(config('web.upload_path'),1).$this->request->param('path').'/'.str_replace('\\','/',$info->getSaveName())]);
        }else{
            $this->error($file->getError());
        }
    }

    public function sublevel_upload(){
        if($this->request->isPost()){
            if($this->request->post('status') == 'chunkCheck'){
                return $this->chunkCheck();
            }elseif($this->request->post('status') == 'chunksMerge'){
                if($this->request->post('name')){
                    if($file = $this->chunksMerge($this->request->post('name'),$this->request->post('chunks'),$this->request->post('ext'),$this->request->post('md5'))){
                        $file['code']=1;
                        return json($file);
                    }
                }
                return json(['code'=>0]);
            }else{
                $file = $this->request->file('file');
                $info = $file->validate(['ext'=>'txt,html,zip,josn,mp3,wma,wav,amr,mp4,apk,ipa'])->move(config('web.upload_path').$this->request->param('path').'/'.$this->request->post('uniqueFileName'),$this->request->post('chunk',0),true,false);
                if($info){
                    return json(['code'=>1,'msg'=>'上传成功！','path'=>substr(config('web.upload_path'),1).$this->request->param('path').'/'.$this->request->post('uniqueFileName').'/'.str_replace('\\','/',$info->getSaveName())]);
                } else {
                    return json(['code'=>0,'msg'=>$file->getError()]);
                }
            }
        }
    }

    protected function chunkCheck(){
        $upload_path = config('web.upload_path');
        $target =  $upload_path.$this->request->param('path').'/'.$this->request->post('name').'/'.$this->request->post('chunkIndex');
        if(file_exists($target) && filesize($target) == $_POST['size']){
            return json(['ifExist'=>1]);
        }
        return json(['ifExist'=>0]);
    }

    protected function chunksMerge($name, $chunks, $ext, $md5){
        $upload_path = config('web.upload_path');
        $targetDir = $upload_path.$this->request->param('path').'/'.$name;
        //检查对应文件夹中的分块文件数量是否和总数保持一致
        if($chunks >= 1 && (count(scandir($targetDir)) - 2) == $chunks){
            //同步锁机制
            $lockFd = fopen($targetDir.'.lock', "w");
            if(!flock($lockFd, LOCK_EX | LOCK_NB)){
                fclose($lockFd);
                return false;
            }
            //进行合并
            $uuid=uniqid();
            $finalName = $upload_path.$this->request->param('path').'/'.$uuid.'.'.$ext;
            $file = fopen($finalName, 'wb');
            for($index = 0; $index < $chunks; $index++){
                $tmpFile = $targetDir.'/'.$index;
                $chunkFile = fopen($tmpFile, 'rb');
                $content = fread($chunkFile, filesize($tmpFile));
                fclose($chunkFile);
                fwrite($file, $content);
                //删除chunk文件
                unlink($tmpFile);
            }
            fclose($file);
            //解锁  
            flock($lockFd, LOCK_UN);
            fclose($lockFd);
            unlink($targetDir.'.lock');
            //删除chunk文件夹
            rmdir($targetDir);
            return ['path'=>substr($finalName,1)];
        }
        return false;
    }
}
