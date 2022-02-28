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
Route::get('compoundInterest', 'investment/Base/compoundInterest');//复利计算


Route::pattern('id','\d+');

Route::get('getUserInfo','index/index/getUserInfo');
Route::get('SearchUser','index/index/SearchUser');

Route::get('getOrganization','index/index/getOrganization');
Route::get('SearchOrganization','index/index/SearchOrganization');




Route::get('exportExcel','index/index/exportExcel');//导出excel
Route::get('importExcel','index/index/importExcel');//导入excel

Route::get('uploadPictureMin','index/index/uploadPictureMin');//上传图片并压缩

Route::get('exportZip','index/index/exportZip');//生成压缩文件


Route::get('KafKaProducer','Kafka/Kafka/KafKaProducer');//kafka生产者推送数据
Route::get('KafKaConsumer','Kafka/Kafka/KafKaConsumer');//kafka消费者者读取数据
