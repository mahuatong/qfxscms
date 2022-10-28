<?php


namespace app\admin\controller;

class Ueditor extends Base
{
	public function index(){
		$config = preg_replace("/\/\*[\s\S]+?\*\//", "", file_get_contents("./public/static/ueditor/config.json"));
		$action = $this->request->get('action');
		switch ($action) {
			 case 'config':
		        $result = $config;
		        break;
		    case 'uploadimage':
		       	$result = $this->uploadPicture();
		        break;
		    case 'uploadvideo':
				$result = $this->uploadvideo();
		        break;
		}
		$callback=$this->request->get('callback');
		if (isset($callback)) {
		    if (preg_match("/^[\w_]+$/",$callback)) {
		        echo $callback . '(' . $result . ')';
		    } else {
		        echo json_encode(array(
		            'state'=> 'callback参数不合法'
		        ));
		    }
		} else {
		    echo $result;
		}
	}
	
	 private function uploadPicture(){
	 	$file = $this->request->file('upfile');
        $info = $file->validate(['ext'=>'jpg,jpeg,png,gif,webp,bmp','type'=>'image/jpeg,image/png,image/gif,image/webp,image/bmp'])
            ->move(config('web.upload_path').'/ueditor');
        if($info){
            $data=array(
				'state'=>'SUCCESS',
				'url'=>substr(config('web.upload_path'),1).'ueditor/'.str_replace('\\','/',$info->getSaveName()),
				'title'=>$info->getFilename(),
				'original'=>$info->getInfo('name'),
				'type'=>'.' . $info->getExtension(),
				'size'=>$info->getSize()
			);
        }else{
            $data=['state'=>''];
        }
		return json_encode($data);
    }
	
	private function uploadvideo(){
        $file = $this->request->file('upfile');
        $info = $file->validate(['ext'=>'flv,swf,mkv,avi,rm,rmvb,mpeg,mpg,mov,mp4'])->move(config('web.upload_path').'/ueditor');
		if($info){
			$data=array(
				'state'=>'SUCCESS',
				'url'=>substr(config('web.upload_path'),1).'ueditor/'.str_replace('\\','/',$info->getSaveName()),
				'title'=>$info->getFilename(),
				'original'=>$info->getInfo('name'),
				'type'=>'.' . $info->getExtension(),
				'size'=>$info->getSize()
			);
		}else{
			$data=['state'=>''];
		}
		return json_encode($data);
    }
}
?>