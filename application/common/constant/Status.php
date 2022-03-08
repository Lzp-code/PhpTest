<?php
/**
 * Created by PhpStorm.
 * User: Gkh
 * Date: 2020/2/10
 * Time: 16:29
 */

namespace app\common\constant;


abstract class Status
{
    public const ENABLED = 1;
    public const DISABLED = 0;
    public const DELETED = -1;

    public const items = [
        ['id' => Status::ENABLED, 'name' => '正常'],
        ['id' => Status::DISABLED, 'name' => '已作废'],
        ['id' => Status::DELETED, 'name' => '已删除']
    ];
}