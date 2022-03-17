<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: yunwuxin <448901948@qq.com>
// +----------------------------------------------------------------------

return [
    //'connector' => 'Sync'
    'connector' => 'redis',//redis驱动

    'expire'     => 60,// 任务的过期时间，默认为60秒; 若要禁用，则设置为 null
    'default'    => 'default',//默认队列名称
    'host'       => '119.23.49.233',
    'port'       => 6380,
    'password'   => 'zun1redis',
    'select'     => 1,// 使用哪一个 db，默认为 db0
    'timeout'    => 0,// redis连接的超时时间
];
