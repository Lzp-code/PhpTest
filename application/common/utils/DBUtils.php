<?php
/**
 * Created by PhpStorm.
 * User: Gkh
 * Date: 2020/2/13
 * Time: 23:22
 */

namespace app\common\utils;


use think\exception\PDOException;

class DBUtils
{
    public static function getSqlState(PDOException $exception)
    {
        $sqlstate = $exception->getData()['PDO Error Info']['SQLSTATE'];
        return empty($sqlstate) ? '' : $sqlstate;
    }
}