<?php


namespace app\model;
use think\Model;
use think\facade\Cache;

class MenuModel extends Model {
    protected $name = 'menu';
	public function getMenus()
    {
        return Cache::remember('admin_menu',function(){
			// 获取主菜单
            $where  =  ['pid'=>0,'hide'=>0];
            $menus['main']  =   MenuModel::where($where)->order('sort asc')->select();
            $menus['child'] = []; //设置子节点
            //高亮主菜单
            foreach ($menus['main'] as $key => $item) {
                $groups = MenuModel::where([['group','<>',''],['pid','=',$item['id']]])->order('sort asc')->column("group");
                foreach ($groups as $g) {
                    $map = ['group'=>$g,'pid'=>$item['id'],'hide'=>0];
                    $child = MenuModel::where($map)->field('id,pid,title,url,icon,tip')->order('sort asc')->select();
                    $menus['child'][$key][$g] = $child->toArray();
                }
            }
            return $menus;
		});    
    }
}