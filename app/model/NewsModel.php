<?php


namespace app\model;
use think\facade\Db;
use think\Model;
use think\facade\Request;
use org\File;
use app\admin\validate\News as NewsValidate;

class NewsModel extends Model{

    protected $name = 'news';
    protected $autoWriteTimestamp = true;
    protected $auto = ['position'];
    protected $insert = ['status'=>1];

    public function getCategoryTextAttr($value,$data){
        return (new CommonApi())->get_category($data['category'],'title');
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
    	$info=NewsModel::where($map)->find();
		return $info;
	}

    public function lists(){
         $map = [];
        if(Request::param('category')){
            $map[]  = ['category','=',Request::param('category')];
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
        $list=NewsModel::where($map)->order($order)->paginate(['list_rows'=>config('web.list_rows'),'query'=>['keywords'=>$keywords]])->each(function($item, $key){
            $item->comment_count = Db::name('comment')->where(['type'=>'news','mid'=>$item->id,'pid'=>0])->count('id');
        });
        return $list;
    }

	public function edit($data,$type){
        $data_link=[];

        if(empty($data['id'])){
            $result = NewsModel::create($data);
            $data_link[]=url('home/news/index',['id'=>$result->id],true,true);
            (new CommonDataOperation())->after('add','news',$data_link);
        }else{
            if($type=='position'){
                $result = NewsModel::update($data);
            }else{
                $pic=NewsModel::where('id',$data['id'])->value('pic');
                if($pic!=$data['pic'] && isset($pic)){
                    File::unlink(".".$pic);
                }
                $result = NewsModel::update($data);
            }
//            rm_cache($data['id'],'news');
            $data_link[]=url('home/news/index',['id'=>$data['id']],true,true);
            (new CommonDataOperation())->after('edit','news',$data_link);
        }

        return $result;
    }

    public function edit_field($data){
        $id=$data['id'];
        unset($data['id']);
        $result = NewsModel::whereIn('id', $id)->update($data);
        if(false === $result){
            $this->error=NewsModel::getError();
            return false;
        }
        return $result;
    }

    public function del($id){
        $map = ['id' => $id];
        $data = NewsModel::field('id,pic')->where($map)->select();
        foreach ($data as $value) {
            rm_cache($value['id'],'news');
            if(!filter_var($value['pic'],FILTER_VALIDATE_URL)){
                File::unlink(".".$value['pic']);
            }
        }
        $result = NewsModel::where($map)->delete();
        DB::name('comment')->where(['mid'=>$id,'type'=>'news'])->delete();
        if(false === $result){
            $this->error=NewsModel::getError();
            return false;
        }else{
            return $result;
        }
    }
}