<?php


namespace app\common\logic;


class ReturnFormat
{
    public static function R($code = 0,$data = [],$msg = 'SUCCESS'){
        return [
            'code' => $code,
            'data' => $data,
            'msg'  => $msg,
        ];
    }
}