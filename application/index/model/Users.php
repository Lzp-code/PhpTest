<?php


namespace app\index\model;


use think\Model;

class Users extends Model
{


    public function getUserIndex(array $where = [], $field = '*', $pageStatus = false, $size = 20, $order = "Users.id desc"){

        //单表查询
        $map = [];
        if (isset($where['id'])) {
            $map[] = ['id', '=', $where['id']];
        }
        if (isset($where['idcard'])) {
            $map[] = ['idcard', '=', $where['idcard']];
        }
        if (isset($where['realname'])) {
            $map[] = ['realname', 'like', '%'.$where['realname'] . '%'];
        }
//        $data = $this->where($map)->field($field)->select();//普通查询

//        $data = $this->with(['JoinChildrenWeb'])->where($map)->field($field)->select();//单个的关联预载入

//        $data = $this->with('JoinChildrenWeb,JoinChildren')->where($map)->field($field)->select();//多个的关联预载入

        $data = $this->where($map)->field($field)->with(['JoinChildrenWeb'=>function($query){//单个的关联预载入的指定字段查询（指定的模型要带上该模型的外键）
            $query->field('users_id,realname,relationship,nation');
        }])->select();


        return $data;


        //多表查询
//        $map = [];
//        if (isset($where['id'])) {
//            $map[] = ['id', '=', $where['id']];
//            $map[] = ['ChildrenWeb.users_id', '=', $where['id']];
//        }
//        if (isset($where['idcard'])) {
//            $map[] = ['Users.idcard', '=', $where['idcard']];
//            $map[] = ['Children.idcard', '=', $where['idcard']];
//        }
//        if (isset($where['realname'])) {
//            $map[] = ['Users.realname', 'like', '%'.$where['realname'] . '%'];
//        }
//        $data = self::hasWhere('JoinChildrenWeb', [], $field)->where($map)->select();
//        $data = $this->with('JoinChildrenWeb')->where($map)->select();
//        return $data;




//        if ($pageStatus) {
//            return self::hasWhere('relationCategory', [], $field)->with(['adminInfo'])->where($map)->group('Article.id')->order('Article.id desc')->order($order)->paginate($size);
//        } else {
//            return self::hasWhere('relationCategory', [], $field)->with(['adminInfo'])->where($map)->group('Article.id')->order('Article.id desc')->order($order)->select();
//        }
    }



    public function JoinChildrenWeb(){
        return $this->hasMany('ChildrenWeb','users_id','id');//关联模型，关联模型外键，本模型主键
    }

    public function JoinChildren(){
        return $this->hasOne('Children','idcard','idcard');//关联模型，关联模型外键，本模型主键
    }















}