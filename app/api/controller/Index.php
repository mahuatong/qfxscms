<?php
/**
 * @author zhushichuan
 * @email 469702544@qq.com
 * @time 2022/10/26
 *
 *
 */


namespace app\api\controller;


use app\model\CategoryModel;
use app\model\NovelChapterModel;
use app\model\NovelModel;
use app\Request;
use app\service\listener\CollectThread;
use net\Gather;
use think\facade\Cache;
use think\facade\Db;

class Index extends BaseController
{
    public function test(){
        $id = 10041;
        Db::name('collect')->where('id',$id)->update(['collect_time'=>time()]);

        $info = Db::name('collect')->where('id',$id)->find();
        Cache::set('collect_info_'.$id,$info);
        $source=Gather::convert_source_url($info['source_url']);
        Cache::set('collect_source_url_'.$id,$source);
        $source_num = Cache::get('collect_log_'.$id);
        (new CollectThread())->collect_list_set($id,$source_num);

        (new CollectThread())->threads(10041);
    }
    // 获取最新章节
    public function getNewChapter(Request $request){
        $id = $request->param('id');
        $info=(new \app\model\CommonApi())->novel_detail($id);
        return $this->success('ok',$info);
    }
    public function getBookDetails(Request $request){
        $id = $request->param('id');
        $res = NovelModel::where('id','=',$id)
            ->field(['id','title','author','pic','content','tag','hits','rating','serialize','update_time','word'])
            ->find();
        if(!$res){
            return  $this->error('书籍不存在');
        }
        return $this->success('ok',$res->toArray());
    }

    // 获取章节列表
    public function getBookChapter(Request $request){
        $book_id = $request->param('id');
        $res = (new \app\model\CommonApi())->get_chapter_list($book_id,'id asc',0,0);
        if(!$res){
            return  $this->error('没有更多了');
        }
        $new_ = [];
        foreach ($res as $v){
            array_push($new_,$v);
        }
        return $this->success('ok',$new_);
    }

    // 章节详情
    public function getBookChapterDetails(Request $request){
        $id = $request->param('id');
        $key = $request->param('key');
        $chapter=(new \app\model\CommonApi())->get_chapter($id,$key,true);
        return $this->success('ok',$chapter);
    }

    // 分类列表
    public function getCateGory(Request $request){
        $pid = $request->param('pid',0);
        $res = CategoryModel::where('status','=',1)->where('pid','=',$pid)->field(['id','title','pid','icon'])->select()->toArray();
        return $this->success('ok',$res);
    }

    // 书籍列表
    public function getBookList(Request $request){
        $data = $request->param();
        $where = [];
        if(isset($data['serialize']) && $data['serialize']!=''){
            $where [] = ['serialize','=',$data['serialize']];
        }
        if(!empty($data['category_id'])){
            $where [] = ['category','=',$data['category_id']];
        }
        if(!empty($data['search_key'])){
            $where [] = ['title|author|content','like',"%{$data['search_key']}%"];
        }
        if(isset($data['size']) && $data['size']!=''){
            // 字数
            switch ($data['size']){
                case 0:
                    $where[] = ['word','between',[0,300000]];
                    break;
                case 1:
                    $where[] = ['word','between',[300000,500000]];
                    break;
                case 2:
                    $where[] = ['word','between',[500000,1000000]];
                    break;
                case 3:
                    $where[] = ['word','between',[1000000,2000000]];
                    break;
                case 4:
                    $where[] = ['word','>',2000000];
                    break;
            }
        }
        if(isset($data['update'])){
            // 更新时间
            switch ($data['update']){
                case 0:
                    $where[] = ['update_time','>=',strtotime("-3 day")];
                    break;
                case 1:
                    $where[] = ['update_time','>=',strtotime("-7 day")];
                    break;
                case 2:
                    $where[] = ['update_time','>=',strtotime("-15 day")];
                    break;
                case 3:
                    $where[] = ['update_time','>=',strtotime("-30 day")];
                    break;
            }
        }
        $order = 'id desc';

        if(!empty($data['order_field'])){
            $order_field = $data['order_field'];
            if(in_array($order_field,['id','update_time','word','hits'])){
                $order = $order_field." desc";
            }
        }
        $res = NovelModel::where($where)
            ->field(['id','title','author','pic','content','tag','hits','rating','serialize','update_time','word'])
            ->order($order)->paginate()->toArray();
        return $this->success('ok',$res);

    }

    // 新书
    public function newBook(Request $request){
        $data = $request->param();
        if(!empty($data['category_id'])){
            $where[] = ['category','=',$data['category_id']];
        }
        $where[] = ['serialize','=',0];
        $where[] = ['create_time','>=',strtotime("-3 month")];
        $where[] = ['sole','>',1];
        $res = NovelModel::where($where)->field(['id','title','author','pic','content','tag','hits','rating','serialize','update_time','word'])->order('id','desc')->limit(0,10)->select()->toArray();
        return $this->success('ok',$res);
    }


}