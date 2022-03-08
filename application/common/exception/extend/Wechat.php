<?php
namespace app\common\exception\extend;

use app\common\lib\ApiCode;

/**
 * @deprecated
 */
class Wechat extends ExceptionAbstract{
    protected $codeArr = [
        40401 => [
            'HttpCode' => ApiCode::SUCCESS,
            'msg'      => '授权失败'
        ],
        40402 => [
            'HttpCode' => ApiCode::SUCCESS,
            'msg'      => '用户未登录'
        ]
    ];
}