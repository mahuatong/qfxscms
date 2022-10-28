<?php


namespace app\model;
use think\Model;
use think\facade\Db;
use think\facade\Request;
use think\facade\Cache;
use org\File;
use app\admin\validate\Template as TemplateValidate;

class TemplateModel extends Model{
    protected $name = 'template';
    private $extensions = [
        "docx"  => "doc",
        "xsl"   => "excel",
        "xslx"  => "excel",
        "txt"   => "text", "md"=>"text",
        "html"  => "html", "js"=>"html", "json"=>"html", "css"=>"html", "php"=>"html", "htm"=>"html",
        "mpg"   => "video", "mp4"=>"video","avi"=>"video","mkv"=>"video",
        "png"   => "tupianwenjian", "jpg"=>"tupianwenjian", "gif"=>"tupianwenjian",
        "mp3"   => "audio", "ogg"=>"audio",
        "zip"   => "archive", "rar"=>"archive", "7z"=>"archive", "tar"=>"archive", "gz"=>"archive"
    ];

    private $template_type = [
        "header.html"  => "头文件",
        "footer.html"   => "尾文件",
        "index.html"   => "首页文件", 
        "type.html"=>"栏目文件",
        "lists.html"  => "筛选文件", 
        "news.html"   => "文章内容页",
        "newslists.html"   => "文章列表页", 
        "novel.html"   => "小说内容页",
        "search.html"   => "搜索页"
    ];

    public function getMoldAttr($value){
        return explode(',', $value);
    }

	public function info($id){
		$map['id'] = $id;
    	$info=TemplateModel::where($map)->find();
		return $info;
	}

    public function set_default($data){
        $validate = new TemplateValidate;
        if (!$validate->scene('default')->check($data)) {
            $this->error=$validate->getError();
            return false;
        }
        foreach ($data['mold'] as $k => $v) {
            $map=[];
            $map[] = ['','exp',Db::raw('find_in_set("'.$v.'",`mold`)')];
            $mold_data=TemplateModel::where($map)->field('id,mold')->select();
            foreach ($mold_data as $key => $value) {
                $mold=array_diff($value['mold'],$data['mold']);
                $mold=implode(",",$mold);
                if($mold){
                    TemplateModel::where('id',$value['id'])->setField('mold',$mold);
                }else{
                    TemplateModel::where('id',$value['id'])->update(['default'=>0,'mold'=>$mold]);
                }
            }
            Cache::delete('check_template_'.$v);
        }
        $data['mold']=implode(",",$data['mold']);
        $data['default']=1;
        $result = TemplateModel::allowField(true)->isUpdate(true)->save($data);
        if(false === $result){
            $this->error=TemplateModel::getError();
            return false;
        }
        return $result;
    }

    public function file_list($path,$is_all=FALSE,$exts='*'){
        $file_info=[];
        $list_info=File::list_dir_info($path,$is_all,$exts);
        foreach ($list_info as $key => $value) {
            $file_info[$key]=File::list_info($value);
            if($file_info[$key]['isFile']){
                $file_info[$key]['extensions']=$this->extensions[$file_info[$key]['ext']];
                $file_info[$key]['template_type']=@$this->template_type[$file_info[$key]['filename']];
                if(!$file_info[$key]['template_type']){
                    $file_info[$key]['template_type']='其它模版';
                }
            }else{
                $file_info[$key]['extensions']='folder';
                $file_info[$key]['template_type']='文件夹';
            }
            
        }
        return $file_info;
    }

    public function file_info($path){
        return File::read($path);
    }

    public function add(){
        $data=Request::post();
        $validate = new TemplateValidate;
        if (!$validate->scene('add')->check($data)) {
            $this->error=$validate->getError();
            return false;
        }
        $data['version']='1.0.0';
        $result = TemplateModel::allowField(true)->save($data);
        if(false === $result){
            $this->error=TemplateModel::getError();
            return false;
        }
        $this->add_tpl_file($data);
        return $result;
    }

    public function edit($data){
        return File::put($data['path'],$data['content']);
    }

    public function del($id){
        $map = ['id' => $id];
        $name = TemplateModel::where($map)->column('name');
        foreach ($name as $value) {
            del_dir_file('./'.config('web.default_tpl').DIRECTORY_SEPARATOR.$value,true);
        }
        $result = TemplateModel::where($map)->delete();
        if(false === $result){
            $this->error=TemplateModel::getError();
            return false;
        }else{
            return $result;
        }
    }

    private function add_tpl_file($data){
        $file=[
            ['tpl'=>'header.html','type'=>'header'],
            ['tpl'=>'footer.html','type'=>'footer'],
            ['tpl'=>'index.html','type'=>'html'],
            ['tpl'=>'type.html','type'=>'html'],
            ['tpl'=>'lists.html','type'=>'html'],
            ['tpl'=>'newslists.html','type'=>'html'],
            ['tpl'=>'search.html','type'=>'html'],
            ['tpl'=>'novel.html','type'=>'html'],
            ['tpl'=>'news.html','type'=>'html'],
            ['tpl'=>'css/style.css','type'=>'css'],
            ['tpl'=>'js/global.js','type'=>'js'],
        ];
        $tpl_html=$html = <<<END
<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="initial-scale=1, maximum-scale=1, user-scalable=no, width=device-width">
        <meta name="keywords" content="{\$web['meta_keywords']}">
        <meta name="description" content="{\$web['meta_description']}">
        <title>{\$web['meta_title']}</title>
        <link rel="stylesheet" href="{\$home_tplpath}css/swiper.min.css" type="text/css"/>
        <link rel="stylesheet" href="{\$home_tplpath}css/style.css" type="text/css"/>
    </head>
    <body>
        {include file="template/home/{$data['name']}/header" /}
        <!-- 模版内容 -->
        {include file="template/home/{$data['name']}/footer" /}
        <script type="text/javascript" src="{\$home_tplpath}js/global.js"></script>
    </body>
</html>
END;
        foreach ($file as $key => $value) {
            if($value['type']=='html'){
                File::put('./'.config('web.default_tpl').DIRECTORY_SEPARATOR.$data['name'].DIRECTORY_SEPARATOR.$value['tpl'],$tpl_html);
            }else{
                File::put('./'.config('web.default_tpl').DIRECTORY_SEPARATOR.$data['name'].DIRECTORY_SEPARATOR.$value['tpl'],'');
            }
        }
    }
}