<?php
use think\facade\Route;

/**
 * 前台电脑端路由
 */
Route::get('/hello/:name', 'index/hello');
//Route::group(function () {
//    Route::get('think', function () {
//        return 'hello,ThinkPHP6index!';
//    });
//
//    Route::get('/hello/:name', 'index/hello');
//
//})->middleware(['DeviceDetection']);

