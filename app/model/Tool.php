<?php


namespace app\model;
use think\Model;
use think\facade\Db;

class Tool extends Model{

    public function datadel_novel(){
        $novel=Db::name('novel')->field('id')->select();
        foreach ($novel as $key => $value) {
            (new NovelModel())->del($value['id']);
        }
	}

    public function datadel_news(){
        $news=Db::name('news')->field('id')->select();
        foreach ($news as $key => $value) {
            (new NewsModel())->del($value['id']);
        }
    }

    public function datadel_user(){
        $user=Db::name('user')->field('id')->select();
        foreach ($user as $key => $value) {
            (new UserModel())->del($value['id']);
        }
    }
}