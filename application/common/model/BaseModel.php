<?php


namespace app\common\model;


use think\Model;

class BaseModel extends Model
{
    //获取单条数据
    public function getOneData($where=[],$field = '*',$order=['id'=>'asc']){
        $result = $this->field($field)
            ->where($where)
            ->order($order)
            ->find();
        return $result;
    }

    //获取分页数据
    public function getPageData($where=[],$field = '*',$order=['id'=>'desc'],$rows=8){
        $result = $this->field($field)
            ->where($where)
            ->order($order)
            ->paginate($rows);

        return $result;
    }

    //获取单列数据
    public function getColumnData($field = 'id',$where=[],$order=['id'=>'desc']){
        $result = $this->where($where)
            ->order($order)
            ->column($field);

        return $result;
    }
}