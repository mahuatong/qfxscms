<?php
namespace app\model;
use think\Model;
use think\facade\Cache;
use think\facade\Env;
use think\facade\Db;
use app\admin\validate\Collect as CollectValidate;

class CollectModel extends Model{

    protected $name = 'collect';
    protected $autoWriteTimestamp = true;
    protected $json = ['rule','category_equivalents'];
    protected $jsonAssoc = true;

    public function getTypeTextAttr($value,$data){
        $status = ['novel'=>'小说','news'=>'文章'];
        return $status[$data['type']];
    }

    public function getRuleAttr($value){
        foreach ($value as $k => $v) {
            if(!empty($v['replace'])){
                $value[$k]['replace']=json_decode($v['replace'],true);
            }
        }
        return $value;
    }

	public function info($id){
		$map['id'] = $id;
    	$info=CollectModel::where($map)->find();
		return $info;
	}

    public function lists(){
        return CollectModel::order('id asc')->paginate(config('web.list_rows'));
    }

	public function edit($data){
        $Collect = new CollectModel();
        if(empty($data['id'])){
            $result = CollectModel::create($data);
        }else{
            Cache::clear('collect');
            $result = CollectModel::update($data);
        }

        return $result;
    }

    public function sever_data($info,$field){
        $chapter=[];
        $chapter_field=[];
        $data_link=[];
        if(is_array($field)){
            $field_data['reurl']=$field["reurl"];
            $field_data['update_time']=time();
            if($info['type']=='novel'){
                $field_data['serialize']=$field['serialize'];
            }
            if(isset($field["id"]) && isset($info['update'])){
                Db::name($info['type'])->where(['id'=>$field["id"]])->update($field_data);
                rm_cache($field['id'],$info['type']);
                $data_id=$field["id"];
            }else{
                $field_data['category']=$field['category'];
                $field_data['title']=$field['title'];
                $field_data['pic']=$field['pic'];
                $field_data['content']=$field['content'];
                if($info['type']=='novel'){
                    $field_data['author']=$field['author'];
                    $field_data['tag']=$field['tag'];
                    $field_data['word']=count($field['chapter'])*2000;
                }
                $field_data['create_time']=time();
                $data_id=Db::name($info['type'])->insertGetId($field_data);
                rm_cache(NUll,$info['type']);
                $data_link[]=url('home/'.$info['type'].'/index',['id'=>$data_id],true,true);
            }
            if($info['type']=='novel'){
                foreach ($field["chapter"] as $value) {
                    $chapter_data=[];
                    $keys=uniqidReal();
                    $chapter_data['title']=$value["title"];
                    $chapter_data['intro']='';
                    $chapter_data['update_time']=time();
                    $chapter_data['issued']=1;
                    $chapter_data['word']=2000;
                    $chapter_data['reurl']=$value["url"];
                    $chapter_data['auto']=2;
                    $chapter_data['path']=$data_id.DIRECTORY_SEPARATOR.$keys.'.txt';
                    $chapter[$keys]=$chapter_data;
                }
                $chapter_db=json_encode($chapter);
                /**
                 * @var CommonApi $api
                 */
                $api= app()->make(CommonApi::class);
                $chapter_db=$api->compress_chapter($chapter_db);

                $chapter_data_last=end($chapter);
                $updated=[
                    'id'=>key($chapter),
                    'title'=>$chapter_data_last['title'],
                    'update_time'=>$chapter_data_last['update_time'],
                    'count'=>count($chapter)
                ];
                $chapter_field['updated']=json_encode($updated);

                $chapter_field['status']=1;
                $chapter_field['novel_id']=$data_id;
                $chapter_field['chapter']=$chapter_db;
                $chapter_field['reurl']=$field['chapter_url'];
                $chapter_field['collect_id']=$info['id'];
                $chapter_field['run_time']=time()+600;
                if(isset($field["id"]) && isset($info['update'])){
                    // 删除原有小说文件
                    $addons_name = Cache::remember('addons_storage',function(){
                        $map = ['status'=>1,'group'=>'storage'];
                        return Db::name('Addons')->where($map)->value('name');
                    });
                    if($addons_name){
                        $chapter=DB::name($info['type'].'_chapter')->where(['novel_id'=>$field["id"]])->value('chapter');
                        /**
                         * @var CommonApi $api
                         */
                        $api= app()->make(CommonApi::class);
                        $chapter=$api->decompress_chapter($chapter);
                        $chapter=json_decode($chapter,true);
                        if($chapter){
                            // $path=array_column($chapter,'path');
                            $addons_class = get_addon_class($addons_name);
                            if(class_exists($addons_class)){
                                $addon = new $addons_class();
                                $path=[];
                                foreach ($chapter as $v) {
                                    if($v['auto']==0){
                                        $path[]=$v['path'];
                                    }
                                }
                                $addon->unlink($path);
                            }
                        }
                    }else{
                        del_dir_file(Env::get('runtime_path').'txt'.DIRECTORY_SEPARATOR.$field["id"],true);
                    }
                    // 更新数据
                    Db::name('bookshelf')->where(['novel_id'=>$field["id"]])->update(['chapter_id'=>0,'chapter_key'=>'']);
                    $chapter_id=Db::name($info['type'].'_chapter')->where(['novel_id'=>$field["id"]])->value('id');
                    Db::name($info['type'].'_chapter')->where(['id'=>$chapter_id])->update($chapter_field);

                }else{
                    $chapter_id=Db::name($info['type'].'_chapter')->insertGetId($chapter_field);
                }
                foreach ($chapter as $key => $value) {
                    $data_link[]=url('home/chapter/index',['id'=>$chapter_id,'key'=>$key],true,true);
                }
            }
            (new CommonDataOperation())->after('add',$info['type'],$data_link);
            return $field;
        }
    }

    public function fields(){
        $data=["field"=>[
                "novel"=>[
                    "category"=>"栏目",
                    "title"=>"名称",
                    "author"=>"作者",
                    "serialize"=>"连载",
                    "pic"=>"图片",
                    "content"=>"介绍",
                    "tag"=>"标签",
                    "chapter_title"=>"章节名称",
                    "chapter_content"=>"章节内容"
                ],
                "news"=>[
                    "category"=>"栏目",
                    "title"=>"名称",
                    "pic"=>"图片",
                    "content"=>"内容"
                ]
            ],
            "category"=>[
                'novel'=>get_tree(0),
                'news'=>get_tree(1)
            ]
        ];
        return $data;
    }
}