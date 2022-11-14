<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006~2018 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------
use think\facade\Route;
//
//Route::get('public/:path', function (string $path) {
//    $filename = public_path() . $path;
//    return new \think\swoole\response\File($filename);
//})->pattern(['path' => '.*\.\w+$']);

Route::group('api',function (){
    Route::get('getNewChapter','Index/getNewChapter');
    Route::get('getCateGory','Index/getCateGory');
    Route::get('getBookDetails','Index/getBookDetails');
    Route::get('getBookChapter','Index/getBookChapter');
    Route::get('getBookChapterDetails','Index/getBookChapterDetails');
    Route::get('getBookList','Index/getBookList');
    Route::get('newBook','Index/newBook');
})->middleware(\app\middleware\AllowOriginMiddleware::class);