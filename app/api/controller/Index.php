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

class Index extends BaseController
{

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
        $page = $request->param('page',1);
        $res = (new \app\model\CommonApi())->get_chapter_list($book_id,'id desc',20,$page);
        if(!$res){
            return  $this->error('没有更多了');
        }
        return $this->success('ok',$res->toArray());
    }

    // 章节详情
    public function getBookChapterDetails(Request $request){
        $id = $request->param('id');
        $key = $request->param('key');
        $chapter=(new \app\model\CommonApi())->get_chapter($id,$key);
        return $this->success('ok',$chapter);
    }

    // 分类列表
    public function getCateGory(Request $request){
        $res = CategoryModel::where('status','=',1)->field(['id','title','pid','icon'])->select()->toArray();
        $res = list_to_tree($res);
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
        if(isset($data['size'])){
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

        if(!empty($data['order'])){
            $order_dta = explode("+",$data['order']);
            if(!empty($order_dta[0]) && !empty($order_dta[1]) && in_array($order_dta[0],['id','update_time','word']) && in_array($order_dta[1],['desc','asc'])){
                $order = $order_dta[0]." ".$order_dta[1];
            }
        }
        $res = NovelModel::where($where)
            ->field(['id','title','author','pic','content','tag','hits','rating','serialize','update_time','word'])
            ->order($order)->limit(0,10)->paginate()->toArray();
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