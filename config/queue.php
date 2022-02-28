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
    'connector' => 'redis',

    'expire'     => 60,
    'default'    => 'default',
    'host'       => '119.23.49.233',
    'port'       => 6380,
    'password'   => 'zun1redis',
    'select'     => 1,
    'timeout'    => 0,
];
