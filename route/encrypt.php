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
//Route::miss(function(){
//    return json('404,不存在此路径！！！！！')->code(404);
//});

Route::pattern('id','\d+');


//加密
Route::group('Encrypt',function (){
    Route::get('AesEncrypt','index/Encrypt/AesEncrypt');//aes加密
    Route::get('AesDecrypt','index/Encrypt/AesDecrypt');//aes解密
});