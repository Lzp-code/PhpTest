<?php


namespace app\index\validate;

use think\Validate;
use think\facade\Cache;
use app\api\validate\BaseValidate;



class Index extends BaseValidate
{
    protected $rule = [
        'id' => 'require|number',


    ];

    protected $message = [
        'id.require'=>'未获取到id参数',
        'id.number'=>'id参数错误',
    ];


    protected $scene = [
        'check'  =>  ['id'],
    ];
}