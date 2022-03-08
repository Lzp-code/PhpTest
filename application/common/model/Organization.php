<?php


namespace app\common\model;


use think\Model;

class Organization extends Model
{
    // 定义全局的查询范围
    protected function base($query)
    {
        $query->where(['is_deleted'=>0,'dissolution'=>0]);
    }
}