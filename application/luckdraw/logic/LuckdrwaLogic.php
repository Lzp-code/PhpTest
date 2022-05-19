<?php


namespace app\luckdraw\logic;


use think\Db;
use think\Exception;

class LuckdrwaLogic{



    //抽奖算法
    public function GetLuckdrwa(){

        try{

            //获取活动信息
            $activity = [
                'id'=>123,
                'titie'=>'双十一百亿大抽奖',
                'base_num' => 10000     //概率基数 用来算百分比
            ];

            //可供抽取的奖品（原陕西团务抽奖系统是直接在数据库查，实际可根据需要再优化）
            $goodsArr = [
                [ 'product_id'=>1,   'name'=>'一等奖',  'probability'=>10], //奖品id，奖品名称，中奖概率
                [ 'product_id'=>2,   'name'=>'二等奖',  'probability'=>20],
                [ 'product_id'=>3,   'name'=>'三等奖',  'probability'=>70],
                [ 'product_id'=>4,   'name'=>'四等奖',  'probability'=>100],
            ];

            $newGoodsArr = array_column($goodsArr,'probability','product_id');

            //如果所有奖品加起来的概率不等于概率基数,则设置未中奖概率
            $goodArrNum = array_sum($newGoodsArr);
            if(($goodArrNum != $activity['base_num'])){
                //设置未中奖的id值为O
                $newGoodsArr[0] = $activity['base_num'] - $goodArrNum;
            }

            //至此可得最终抽奖数组如下：（奖品id => 中奖概率）
            $newGoodsArr = [
                0 => 9800,      //未中奖概率 = 活动的中奖基数（$activity['base_num']） - 所有实际奖品的中奖率之和（$goodArrNum）。
                1 => 10,
                2 => 20,
                3 => 70,
                4 => 200,
            ];

            //概率计算
            $gid = self::getRang($newGoodsArr);

            if($gid == 0){      //如果奖品id是0，表示未中奖
                return ['status'=>-1];
            }

            //否则，就是已中奖。
            //生成订单 并且返回奖品信息
            $key = array_search($gid,array_column($goodsArr,'product_id'));
            $params = ['activityId'=>self::$id,'goodId'=>$gid,'uid'=>self::$uid];
            $award = new Award($params);
            switch ($goodsArr[$key]['type']){
                case 1:
                    $res = $award->createOrder($activity,$goodsArr[$key]['name']);
                    break;
                case 2:
//                    $res = $award->addPoints($exchangeId,$activity->integral,$goodsArr[$key]['name'],$point=1);
                    break;

            }
            return ['status'=>1,'award'=>$goodsArr[$key]['name'],'goodName'=>$res['goodName'],'orderId'=>$res['orderId']];
        }catch (\Exception $e){
            return ['status'=>0,'msg'=>$e->getMessage()];
        }


    }



    //计算中奖概率
    public static function getRang($proArr){
        $result = '';
        //概率数组的总概率精度
        $proSum = array_sum($proArr);

        foreach ($proArr as $key=>$proCur){     //循环奖品数组，一个一个抽。
            $randNum = mt_rand(1,$proSum);       //生成一个1到总精度的随机数
            if($randNum <= $proCur){            //若此随机数在此次循环的奖品的中奖概率内
                $result = $key;                 //则返回奖品id，表示已中奖
                break;
            }else{
                $proSum -= $proCur;             //否则，表示未中此奖品，减去此奖品的概率，进入下一次循环
            }
        }

        //在本例当中就是第一次不中就减去9800，
        //也就是说第二个数是在1，200这个范围内筛选的。
        //这样筛选到最终，总会有一个数满足要求。
        //就相当于去一个箱子里摸东西，第一个不是，第二个不是，第三个还不是，那最后一个一定是。

        unset($proArr);
        return $result;
    }











}