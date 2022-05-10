<?php
/**
 * Created by PhpStorm.
 * User: mark
 * Date: 2019/11/5
 * Time: 14:30
 */

namespace app\index\logic;
use think\Db;

class Index
{



    //ES查询
    public function getWhere($params){
        $where = array();
        $where['is_delete'] = 0;
        if(!empty($params['id'])){            $where['id'] = $params['id'];  }
        if(!empty($params['idcard'])){            $where['idcard'] = $params['idcard'];  }
        if(!empty($params['mobile'])){            $where['mobile'] = $params['mobile'];  }
        if(!empty($params['realname'])){            $where['realname'] = $params['realname'];  }
        if(!empty($params['birthday'])){            $where['birthday'] = $params['birthday'];  }
        if(!empty($params['create_time'])){            $where['create_time'] = $params['create_time'];  }
        if(!empty($params['photo'])){            $where['photo'] = $params['photo'];  }

        //must：搜索的结果必须匹配，类似SQL的AND,must_not: 搜索的结果必须不匹配，类似SQL的NOT
        //filter：精确过滤
        //should: 搜索的结果至少匹配到一个，类似SQL的OR
        $condition = ['must' => [],'filter'=> []];

        //在搜索多个字段的时候，可以使用multi_match
        //minimum_should_match表示必须匹配的最小子句（为数字表示最小子句数，为百分比表示向下取整的字段最小子句数）
        if(!empty($where['realname'])){
            array_push($condition['must'],['multi_match'=>['query'=>$where['realname'],'fields'=>['realname'],'minimum_should_match'=>'90%']]);
        }

        //通配符查询-wildcard，*代表匹配一个0个或多个字符，包括空值；?代表匹配一个任意字符
        if(!empty($where['mobile'])){
            array_push($condition['must'],['wildcard' => ['mobile'=>$where['mobile'].'*']]);
        }

        //filter_精确过滤，ierms_多个精确值
        array_push($condition['filter'],['terms' => ['id'=>array(5447823,15897,15809)]]);

//        //filter_精确过滤，ierm_单个精确值
//        array_push($condition['filter'],['term' => ['idcard'=> 371321198805205636]]);

        //range_范围查询
        array_push($condition['filter'],['range' => ['mobile' => ['gt'=> 0 ,'lt' => 18830243540]]]);
        return $condition;
    }



    //错误捕获与处理
    public function errorTest(){
        //指定异常处理的位置，在config/app.php 的参数 exception_handle（此处指定到app/ExceptionHandle）

        //如下随便写几句，让这一段报错，走到app/ExceptionHandle 的 render方法去
        $data = Db::name('dfsdfsdfsdsdfsd')->field('id,name')->where('id','<',6666)->select();
        $data = new UsersModel();
        return $data;
    }

}