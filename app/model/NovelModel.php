<?php


namespace app\model;
use think\Model;
use think\facade\Db;
use think\facade\Request;
use think\facade\Config;
use think\facade\Env;
use think\facade\Cache;
use org\File;
use app\admin\validate\Novel as NovelValidate;

class NovelModel extends Model{

    protected $name = 'novel';
    protected $autoWriteTimestamp = true;
    protected $auto = ['position'];
    protected $insert = ['status'=>1];

    public function getCategoryTextAttr($value,$data){
        return (new CommonApi())->get_category($data['category'],'title');
    }

    public function getSerializeTextAttr($value,$data){
        $serialize = [0=>'连载',1=>'完结'];
        return $serialize[$data['serialize']];
    }

    public function getCollectAttr($value,$data){
        $chapter=Db::name('novel_chapter')->where(['novel_id'=>$data['id']])->field('collect_id')->find();
        return (new CollectModel())->info($chapter['collect_id']);
    }

    public function setPositionAttr($value){
        if(!is_array($value)){
            return 0;
        }else{
            $pos = 0;
            foreach ($value as $key=>$value){
                $pos += $value;
            }
            return $pos;
        }
    }

	public function info($id){
		$map['id'] = $id;
    	$info=NovelModel::where($map)->find();
		return $info;
	}

    public function lists($extra=[]){
        $map = [];
        if(Request::param('category')){
            $map[]  = ['category','=',Request::param('category')];
        }
        $serialize=Request::param('serialize');
        if(isset($serialize)){
            $map[]  = ['serialize','=',Request::param('serialize')];
        }
        $keywords = Request::param('keywords');
        if($keywords){
            $map[]  = ['title','like','%'.$keywords.'%'];
        }
        if(Request::param('position')){
            $map[] = ['position','exp',Db::raw('& '.Request::param('position').' = '.Request::param('position'))];
        }
        $status=Request::param('status');
        if(isset($status)){
            $map[] = ['status','=',$status];
        }
        if(Request::param('order')){
            $order = Request::param('order');
            if(strstr($order,'+')){
                $order=str_replace('+',' ',$order);
            }
        }else{
            $order = 'update_time desc';
        }
        if(isset($extra)){
            $map=array_merge($map,$extra);
        }
        $list=NovelModel::where($map)->order($order)->paginate(['list_row'=>config('web.list_rows'),'query'=>['keywords'=>$keywords]])->each(function($item, $key){
            $item->comment_count = Db::name('comment')->where(['type'=>'novel','mid'=>$item->id,'pid'=>0])->count('id');
        });
        return $list;
    }

	public function edit($data,$type){
        $data_link=[];

        $Novel = new NovelModel();
        if(empty($data['id'])){
            $result = $Novel->save($data);
            rm_cache();
            $data_link[]=url('home/novel/index',['id'=>$Novel->id],true,true);
            (new CommonDataOperation())->after('add','novel',$data_link);
        }else{
            if($type=='position' || $type=='category'){
                $result = $Novel->update($data);
            }else{
                $pic=NovelModel::where('id',$data['id'])->value('pic');
                if($pic!=$data['pic'] && strpos($pic,'://')===false){
                    File::unlink(".".$pic);
                }
                $result = $Novel->update($data);
            }
            $data_link[]=url('home/novel/index',['id'=>$data['id']],true,true);
            (new CommonDataOperation())->after('edit','novel',$data_link);
        }
        if(false === $result){
            $this->error='';
            return false;
        }
        return $result;
    }

    public function edit_field($data){
        $id=$data['id'];
        unset($data['id']);
        $result = NovelModel::whereIn('id', $id)->update($data);
        if(false === $result){
            $this->error=NovelModel::getError();
            return false;
        }
        return $result;
    }

    public function del($id){
        $map = ['id' => $id];
        $data = NovelModel::field('id,pic')->where($map)->select();
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
        foreach ($data as $value) {
            if(!filter_var($value['pic'],FILTER_VALIDATE_URL)){
                File::unlink(".".$value['pic']);
            }
            if($addons_name){
                $chapter=Db::name('novel_chapter')->where(['novel_id'=>$value['id']])->value('chapter');
                $chapter=(new CommonApi())->decompress_chapter($chapter);
                $chapter=json_decode($chapter,true);
                if($chapter){
                    $path=[];
                    foreach ($chapter as $v) {
                        if($v['auto']==0){
                            $path[]=$v['path'];
                        }
                    }
                    $addon->unlink($path);
                }
            }else{
                del_dir_file(Env::get('runtime_path').'txt'.DIRECTORY_SEPARATOR.$value['id'],true);
            }
            rm_cache($value['id']);
        }
        $result = NovelModel::where($map)->delete();
        Db::name('bookshelf')->where(['novel_id'=>$id])->delete();
        Db::name('novel_chapter')->where(['novel_id'=>$id])->delete();
        Db::name('comment')->where(['mid'=>$id,'type'=>'novel'])->delete();
        if(false === $result){
            $this->error=NovelModel::getError();
            return false;
        }else{
            return $result;
        }
    }
}