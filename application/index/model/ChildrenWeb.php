<?php


namespace app\index\model;


use think\Model;

class ChildrenWeb extends Model
{

    public function BelongsToUser(){
        return $this->belongsTo('Users','users_id','id')->field('realname,school');//关联模型，关联模型外键，本模型主键
    }

}