<?php


namespace app\index\model;


use think\Model;

class Users extends Model
{


    //查询用户信息再关联查询
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
        $data = $this->with('JoinChildrenWeb,JoinChildren')->where($map)->field($field)->select();//多个的关联预载入
//        $data = $this->where($map)->field($field)->with(['JoinChildrenWeb'=>function($query){//单个的关联预载入的指定字段查询（指定的模型要带上该模型的外键）
//            $query->field('users_id,realname,relationship,nation');
//        }])->select();

        return $data;
    }


    //关联模型，关联模型外键，本模型主键
    public function JoinChildrenWeb(){
        return $this->hasMany('ChildrenWeb','users_id','id')->field('users_id,realname,relationship');//查询的field里，要带上关联的外键
    }

    //关联模型，关联模型外键，本模型主键
    public function JoinChildren(){
        return $this->hasOne('Children','idcard','idcard')->field('idcard,realname,age,sex,birthday');//查询的field里，要带上关联的外键
    }





    //查询团员信息再关联user表查询
    public function getChildrenWeb($id){
        //单表查询
        $map = [];
        $map[] = ['id', '=', $id];
        $ChildrenWebModel = new ChildrenWeb();
        $ChildrenWeb = $ChildrenWebModel::where($map)->find();
        $ChildrenWeb->BelongsToUser;//调用ChildrenWeb模型的BelongsToUser方法
        return $ChildrenWeb;
    }









}