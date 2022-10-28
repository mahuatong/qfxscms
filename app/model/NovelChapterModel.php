<?php


namespace app\model;
use think\Model;
use think\facade\Db;
use think\facade\Config;
use think\facade\Env;
use think\facade\Cache;
use org\File;
use app\admin\validate\NovelChapter as NovelChapterValidate;

class NovelChapterModel extends Model{

    protected $name = 'novel_chapter';
    protected $insert = ['status'=>1];

    protected function set_chapter($data){
        $chapter=NovelChapterModel::where(['id'=>$data['id']])->value('chapter');
        $chapter=(new CommonApi())->decompress_chapter($chapter);
        $chapter=json_decode($chapter,true);
        $word=mb_strlen($data['content']);
        $chapter_data=[
            'title'=>$data['title'],
            'intro'=>$data['intro'],
            'update_time'=>time(),
            'issued'=>$data['issued'],
            'word'=>$word,
            'reurl'=>'',
            'auto'=>0
        ];
        if(empty($data['key'])){
            $key=uniqidReal();
            $novel_word=$word;
        }else{
            $key=$data['key'];
            $novel_word=$word-$chapter[$key]['word'];
            $chapter_data['reurl']=$chapter[$key]['reurl'];
            $chapter_data['update_time']=time();
        }
        $chapter_data['path']=$data['novel_id'].DIRECTORY_SEPARATOR.$key.'.txt';
        $chapter[$key]=$chapter_data;
        $novel_data=['update_time'=>time(),'word'=>Db::raw('word+'.$novel_word)];
        Db::name('novel')->where(['id'=>$data['novel_id']])->update($novel_data);
        (new CommonApi())->set_chapter_content($chapter_data['path'],$data['content']);

        $chapter_data_last=end($chapter);
        $updated=[
            'id'=>key($chapter),
            'title'=>$chapter_data_last['title'],
            'update_time'=>$chapter_data_last['update_time'],
            'count'=>count($chapter)
        ];
        $updated=json_encode($updated);
        $chapter=json_encode($chapter);
        $chapter=(new CommonApi())->compress_chapter($chapter);
        return ['chapter'=>$chapter,'key'=>$key,'updated'=>$updated];
    }

    protected function import_chapter($data){
        $word=mb_strlen($data['content']);
        $key=uniqidReal();
        $chapter_data=[
            'title'=>$data['title'],
            'intro'=>'',
            'update_time'=>time(),
            'issued'=>1,
            'word'=>$word,
            'reurl'=>'',
            'auto'=>0,
            'path'=>$data['novel_id'].DIRECTORY_SEPARATOR.$key.'.txt'
        ];
        (new CommonApi())->set_chapter_content($chapter_data['path'],$data['content']);
        return ['chapter'=>$chapter_data,'key'=>$key];
    }

     private function check_title($title){
        $title=str_replace(array("第","章","一","二","三","四","五","六","七","八","九","十","零","0","1","2","3","4","5","6","7","8","9"),"$",$title);
        if(substr_count($title,"$")>=1){
            return true;
        }else{
            return false;
        }
     }

	public function info($id,$key){
    	$info=NovelChapterModel::where(['id'=>$id])->field('id,chapter,novel_id,collect_id')->find()->toArray();
        $info['chapter']=(new CommonApi())->decompress_chapter($info['chapter']);
        $info['chapter']=json_decode($info['chapter'],true);
        if($info['chapter'][$key]['auto']==0){
            $info['chapter'][$key]['content']=(new CommonApi())->get_chapter_content($info['chapter'][$key]['path']);
        }
        $info['chapter'][$key]['id']=$id;
        $info['chapter'][$key]['key']=$key;
        $info['chapter'][$key]['novel_id']=$info['novel_id'];
        $info['chapter'][$key]['collect_id']=$info['collect_id'];
		return $info['chapter'][$key];
	}

    public function lists($id){
        $list=NovelChapterModel::where(['novel_id'=>$id])->field('id,chapter')->find();
        if(empty($list)){
            $list=['chapter'=>[],'id'=>''];
        }else{
            $list['chapter']=(new CommonApi())->decompress_chapter($list['chapter']);
            $list['chapter']=json_decode($list['chapter'],true);
            $list['chapter']=$list['chapter']?array_reverse($list['chapter'],true):[];
        }
        return $list;
    }

	public function edit($data){
        $data_link=[];
        $chapter=$this->set_chapter($data);
        $data['chapter']=$chapter['chapter'];
        $data['updated']=$chapter['updated'];
        if(empty($data['id'])){
            $result = NovelChapterModel::create($data);
            $id = $result['id'];
            $data_link[]=url('home/chapter/index',['id'=>$id,'key'=>$chapter['key']],true,true);
            (new CommonDataOperation())->after('add','chapter',$data_link);
        }else{
            $result = NovelChapterModel::update($data);
            $id = $data['id'];
            $data_link[]=url('home/chapter/index',['id'=>$id,'key'=>$chapter['key']],true,true);
            (new CommonDataOperation())->after('edit','chapter',$data_link);
        }

        if(empty($data['id'])){
            rm_cache($data['novel_id'],'novel',false);
            return ['id'=>$id,'key'=>$chapter['key']];
        }else{
            if(empty($data['issued'])){
                return $chapter['key'];
            }else{
                rm_cache($data['novel_id'],'novel',false);
                return $result;    
            }
        }
    }

    public function import($data){
        $novel_word=0;
        if(!empty($data['id']) && $data['type']!=="tests"){
            $chapter=NovelChapterModel::where(['id'=>$data['id']])->value('chapter');
            $chapter=(new CommonApi())->decompress_chapter($chapter);
            $chapter=json_decode($chapter,true);
            $chapter_db=array_column($chapter,'title','title');
        }else{
            $chapter=[];
        }
        $txt_content=File::get(".".$data['txtpath'],'content');
        if(!empty($txt_content)){
            $encode = mb_detect_encoding($txt_content, array('ASCII', 'UTF-8', 'GB2312', 'GBK', 'BIG5'));
            if (strcasecmp($encode, 'utf-8') !== 0) {
                $txt_content = iconv($encode, 'utf-8//IGNORE', $txt_content);
            }
            if($data['split']=='custom'){
                $data['split']=$data['customsplit'];
            }
            $split=str_replace(["\\r\\n","\\r","\\n"],["\r\n","\r","\n"],$data['split']);
            $txt_content_array=explode($split, $txt_content);
            if(count($txt_content_array)>1){
                foreach ($txt_content_array as $key => $value) {
                    if(empty(trim($value))){
                        continue;
                    }
                    $data['title']='';
                    $data['content']='';
                    $chapter_array=explode(PHP_EOL, $value);
                    foreach ($chapter_array as $k => $v) {
                        if(empty(trim($v))){
                             continue;
                        }else{
                            if($this->check_title($v)===true){
                                $data['title']=$v;
                                $data['content']=ltrim(str_replace($v,'',$value));
                                break;
                            }else{
                                continue 2;
                            }
                        }
                    }
                    if(isset($chapter_db[$data['title']]) || empty($data['title'])){
                        continue;
                    }
                    if($data['type']==="tests"){
                        $data['content']=str_replace(PHP_EOL,"<br>",$data['content']);
                        return $data;
                    }
                    $import_chapter=$this->import_chapter($data);
                    $chapter[$import_chapter['key']]=$import_chapter['chapter'];
                    $novel_word+=$import_chapter['chapter']['word'];
                }
                if(isset($import_chapter)){
                    $data['chapter']=(new CommonApi())->compress_chapter(json_encode($chapter));
                    $data['updated']=json_encode([
                        'id'=>$import_chapter['key'],
                        'title'=>$import_chapter['chapter']['title'],
                        'update_time'=>$import_chapter['chapter']['update_time'],
                        'count'=>count($chapter)
                    ]);
                    if(empty($data['id'])){
                        $result = NovelChapterModel::create($data);
                        $id = $result['id'];
                    }else{
                        $result = NovelChapterModel::update($data);
                        $id = $data['id'];
                    }
                    if(false === $result){
                        $this->error='';
                        return false;
                    }
                    $novel_data=['update_time'=>time(),'word'=>Db::raw('word+'.$novel_word)];
                    Db::name('novel')->where(['id'=>$data['novel_id']])->update($novel_data);
                    $data_link[]=url('home/chapter/index',['id'=>$id,'key'=>$import_chapter['key']],true,true);
                    (new CommonDataOperation())->after('add','chapter',$data_link);
                    File::unlink(".".$data['txtpath']);
                    rm_cache($data['novel_id'],'novel',false);
                }
            }else{
                $this->error='分割字符无效';
                return false;
            }
        }else{
            $this->error='txt无内容';
            return false;
        }
    }

    public function del($id,$key){
        $word=0;
        $map = ['id' => $id];
        $data=NovelChapterModel::where($map)->field('id,novel_id,chapter')->find()->toArray();
        $data['chapter']=(new CommonApi())->decompress_chapter($data['chapter']);
        $chapter=json_decode($data['chapter'],true);
        $addons_name = Cache::remember('addons_storage',function(){
            $map = ['status'=>1,'group'=>'storage'];
            return Db::name('Addons')->where($map)->value('name');
        });
        if($addons_name){
            $addons_class = get_addon_class($addons_name);
            if(class_exists($addons_class)){
                $addon = new $addons_class();
            }
        }
        foreach ($key as $v) {
            if($chapter[$v]['auto']==0){
                if($addons_name){
                    $addon->unlink($chapter[$v]['path']);
                }else{
                    File::unlink(Env::get('runtime_path').'txt'.DIRECTORY_SEPARATOR.$chapter[$v]['path']);
                }
            }
            $word+=$chapter[$v]['word'];
            unset($chapter[$v]);
        }

        $chapter_data_last=end($chapter);
        $updated=[
            'id'=>key($chapter),
            'title'=>$chapter_data_last['title'],
            'update_time'=>$chapter_data_last['update_time'],
            'count'=>count($chapter)
        ];
        $data['updated']=json_encode($updated);

        $data['chapter']=json_encode($chapter);
        $data['chapter']=(new CommonApi())->compress_chapter($data['chapter']);
        $result=NovelChapterModel::update($data);
        $chapter_data=['word'=>Db::raw('word-'.$word)];
        Db::name('novel')->where('id',$data['novel_id'])->update($chapter_data);
        if(false === $result){
            $this->error=NovelChapterModel::getError();
            return false;
        } else {
            return true;
        }
    }
}