<?php


namespace app\index\model;


use think\Model;

class Users extends Model
{


    public function getUserIndex(array $where = [], $field = '*', $pageStatus = false, $size = 20, $order = "Users.id desc"){

        //单表查询
//        $map = [];
//        if (isset($where['id'])) {
//            $map[] = ['id', '=', $where['id']];
//        }
//        if (isset($where['idcard'])) {
//            $map[] = ['idcard', '=', $where['idcard']];
//        }
//        if (isset($where['realname'])) {
//            $map[] = ['realname', 'like', '%'.$where['realname'] . '%'];
//        }
//        $data = $this->where($map)->select();
//        return $data;


        //多表查询
        $map = [];
        if (isset($where['id'])) {
            $map[] = ['id', '=', $where['id']];
//            $map[] = ['ChildrenWeb.users_id', '=', $where['id']];
        }
        if (isset($where['idcard'])) {
            $map[] = ['Users.idcard', '=', $where['idcard']];
            $map[] = ['Children.idcard', '=', $where['idcard']];
        }
        if (isset($where['realname'])) {
            $map[] = ['Users.realname', 'like', '%'.$where['realname'] . '%'];
        }
//        $data = self::hasWhere('JoinChildrenWeb', [], $field)->where($map)->select();

        $data = $this->with('JoinChildrenWeb')->where($map)->select();

        return $data;




//        if ($pageStatus) {
//            return self::hasWhere('relationCategory', [], $field)->with(['adminInfo'])->where($map)->group('Article.id')->order('Article.id desc')->order($order)->paginate($size);
//        } else {
//            return self::hasWhere('relationCategory', [], $field)->with(['adminInfo'])->where($map)->group('Article.id')->order('Article.id desc')->order($order)->select();
//        }
    }



    public function JoinChildrenWeb(){
        return $this->hasMany('ChildrenWeb','users_id','id')->field('realname,idcard');
    }

















}