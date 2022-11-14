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

namespace app\service;
use think\facade\Log;
use think\template\TagLib;
use think\facade\Request;

class HomeTag extends TagLib{

    protected $tags   =  [
        'nav'       => ['attr' => 'id,cid,type,limit,empty'],
        'slider'    => ['attr' => 'id,limit,empty'],
        'news'      => ['attr' => 'id,cid,order,limit,pos,time,page,empty'],
        'news_page' => ['close' => 0],
        'novel'     => ['attr' => 'id,cid,order,limit,pos,time,newbook,over,author,page,empty'],
        'novel_page'=> ['close' => 0],
        'chapter'   => ['attr' => 'id,nid,order,limit,page,empty'],
        'chapter_page'=>['close' => 0],
        'filter'    => ['attr' => 'id,name,cid,type,empty'],
        'link'      => ['attr' => 'id,limit,empty'],
        'comment'   => ['attr' => 'size,limit', 'close' => 0],
        'crumbs'    => ['attr' => 'id','close' => 1,'level'=>1],
        'oauth_login'=>['attr' => 'id','close' => 1,'level'=>1],
        'ad'=>['attr' => 'id','close' => 0]
    ];

    public function tagNav($tag, $content){
        $category  = empty($tag['cid']) ? '0' : $tag['cid'];
        $type   = isset($tag['type']) ? $tag['type'] : 'false';
        $limit  = empty($tag['limit']) ? '""' : $tag['limit'];
        $empty   = isset($tag['empty']) ? $tag['empty'] : '';
        $parse  = '<?php ';
        $parse .= '$__NAV__ = (new \app\model\CommonApi())->get_nav('.$category.','.$type.','.$limit.',empty($cid)?0:$cid);?>';
        $parse .= '{volist name="__NAV__" id="'. $tag['id'] .'" empty="'.$empty.'"}';
        $parse .= $content;
        $parse .= '{/volist}';
        return $parse;
    }

    public function tagSlider($tag, $content){
        $limit    = empty($tag['limit']) ? '""' : $tag['limit'];
        $empty   = isset($tag['empty']) ? $tag['empty'] : '';
        $parse  = '<?php ';
        $parse .= '$__SLIDER__ = (new \app\model\CommonApi())->get_slider('.$limit.');?>';
        $parse .= '{volist name="__SLIDER__" id="'. $tag['id'] .'" empty="'.$empty.'"}';
        $parse .= $content;
        $parse .= '{/volist}';
        return $parse;
    }

    public function tagNews($tag, $content){
        $category  = empty($tag['cid']) ? '0' : $tag['cid'];
        if(strstr($category,',')){
            $category ="'".$category."'";
        }
        $limit    = empty($tag['limit']) ? '""' : $tag['limit'];
        $order  = empty($tag['order']) ? '"id DESC"' : '"'.$tag['order'].'"';
        $pos  = empty($tag['pos']) ? 'false' : '$pos';
        $time  = empty($tag['time']) ? '""' : '"'.$tag['time'].'"';
        $page  = empty($tag['page']) ? 0 : $tag['page'];
        $empty   = isset($tag['empty']) ? $tag['empty'] : '';
        $parse  = '<?php ';
        $parse .= '$__NEWS__ = (new \app\model\CommonApi())->get_news('.$category.','.$order.','.$limit.','.$pos.','.$time.','.$page.');?>';
        $parse .= '{volist name="__NEWS__" id="'. $tag['id'] .'" empty="'.$empty.'"}';
        $parse .= $content;
        $parse .= '{/volist}';
        return $parse;
    }

    public function tagNews_page($tag, $content){
        $parse = '{$__NEWS__|raw}';
        return $parse;
    }

    public function tagNovel($tag, $content){
        $category  = empty($tag['cid']) ? '0' : $tag['cid'];
        if(strstr($category,',')){
            $category ="'".$category."'";
        }
        $limit    = empty($tag['limit']) ? '""' : $tag['limit'];
        $order  = empty($tag['order']) ? '"id DESC"' : '"'.$tag['order'].'"';
        $pos  = empty($tag['pos']) ? 'false' : '$pos';
        $time  = empty($tag['time']) ? '""' : '"'.$tag['time'].'"';
        $over  = empty($tag['over']) ? 'false' : $tag['over'];
        $newbook  = empty($tag['newbook']) ? 'false' : $tag['newbook'];
        $author  = empty($tag['author']) ? 'false' : '"'.$tag['author'].'"';
        $page  = empty($tag['page']) ? 0 : $tag['page'];
        $empty   = isset($tag['empty']) ? $tag['empty'] : '';
        $parse  = '<?php ';
        if(strtolower(getModule()."/".Request::controller())=='home/lists' && $page){
            $category='$cid';
        }
        
        $parse .= '$__NOVEL__ = (new \app\model\CommonApi())->get_novel('.$category.','.$order.','.$limit.','.$pos.','.$time.','.$newbook.','.$over.','.$author.','.$page.');?>';
        $parse .= '{volist name="__NOVEL__" id="'. $tag['id'] .'" empty="'.$empty.'"}';
        $parse .= $content;
        $parse .= '{/volist}';
        return $parse;
    }

    public function tagNovel_page($tag, $content){
        if(strtolower(Request::controller()."/".Request::action())=='search/index'){
            $parse = '{$__NOVEL__|raw}';

        }else{
            $parse = '{$__NOVEL__|raw}';
        }
        return $parse;
    }

    public function tagChapter($tag, $content){
        $nid  = empty($tag['nid']) ? '$id' : $tag['nid'];
        $limit    = empty($tag['limit']) ? '""' : $tag['limit'];
        $order  = empty($tag['order']) ? '"id asc"' : '"'.$tag['order'].'"';

        $page  = empty($tag['page']) ? 0 : $tag['page'];
        $empty   = isset($tag['empty']) ? $tag['empty'] : '';
        $parse  = '<?php ';
        $parse .= '$__CHAPTER__ = (new \app\model\CommonApi())->get_chapter_list('.$nid.','.$order.','.$limit.','.$page.');?>';
        $parse .= '{volist name="__CHAPTER__" id="'. $tag['id'] .'" empty="'.$empty.'"}';
        $parse .= $content;
        $parse .= '{/volist}';
        return $parse;
    }

    public function tagChapter_page($tag, $content){
        $parse = '{$__CHAPTER__|raw}';
        return $parse;
    }

    public function tagFilter($tag, $content){
        $category  = empty($tag['cid']) ? '0' : $tag['cid'];
        $type  = isset($tag['type']) ? $tag['type'] : 'false';
        $empty   = isset($tag['empty']) ? $tag['empty'] : '';
        if(strstr($category,',')){
            $category ="'".$category."'";
        }
        $parse  = '<?php ';
        $parse .= '$__FILTER__ = (new \app\model\CommonApi())->get_filter("'.$tag['name'].'",'.$type.','.$category.');?>';
        $parse .= '{volist name="__FILTER__" id="'. $tag['id'] .'" empty="'.$empty.'"}';
        $parse .= $content;
        $parse .= '{/volist}';
        return $parse;
    }

    public function tagLink($tag, $content){
        $limit    = empty($tag['limit']) ? '""' : $tag['limit'];
        $empty   = isset($tag['empty']) ? $tag['empty'] : '';
        $parse  = '<?php ';
        $parse .= '$__LINK__ = (new \app\model\CommonApi())->get_link('.$limit.');?>';
        $parse .= '{volist name="__LINK__" id="'. $tag['id'] .'" empty="'.$empty.'"}';
        $parse .= $content;
        $parse .= '{/volist}';
        return $parse;
    }

    public function tagComment($tag, $content){
        $limit    = empty($tag['limit']) ? '5' : $tag['limit'];
        $size    = empty($tag['size']) ? '14' : $tag['size'];
        $parse='<iframe id="comment" scrolling="no" frameborder="0" style="height: 571px; display: block !important; width: 100% !important; border: 0px none !important; overflow: hidden !important;" src="/index.php?s=/Home/comment/lists/id/{$id}/type/{$type}/limit/'.$limit.'/size/'.$size.'"></iframe>';
        return $parse;
    }

    public function tagCrumbs($tag, $content){
        $parse  = '<?php ';
        $parse .= '$__CRUMBS__ = (new \app\model\CommonApi())->get_crumbs($cid,$id);$is_last=false;?>';
        $parse .= '{volist name="__CRUMBS__" id="'. $tag['id'] .'"}';
        $parse .= '{if count($__CRUMBS__)<=$i}{assign name="is_last" value="true" /}{/if}';
        $parse .= $content;
        $parse .= '{/volist}';
        return $parse;
    }

    public function tagOauth_login($tag, $content){
        $parse  = '<?php ';
        $parse .= '$__OAUTHLOGIN__ = (new \app\model\CommonApi())->get_oauth_login($mold);?>';
        $parse .= '{volist name="__OAUTHLOGIN__" id="'. $tag['id'] .'"}';
        $parse .= $content;
        $parse .= '{/volist}';
        return $parse;
    }

    public function tagAd($tag, $content){
        $parse  = '<?php ';
        $parse .= '$__AD__ = (new \app\model\CommonApi())->get_ad('. $tag['id'] .');?>';
        $parse .= '{$__AD__|raw}';
        return $parse;
    }
}