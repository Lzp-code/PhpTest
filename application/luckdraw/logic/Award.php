<?php


namespace app\luckdraw\logic;


use app\luckdraw\model\LuckDrawGoods;
use app\luckdraw\model\Product;
use app\luckdraw\model\UserScore;
use think\Db;
use think\Exception;
use think\Queue;

class Award
{
    public static $goodId;
    public static $activityId;
    public static $userId;
    public static $goodMess;


    public function __construct($params){
        self::$goodId = $params['goodId'];
        self::$activityId = $params['activityId'];
        self::$userId = $params['uid'];
        self::$goodMess = Product::where('id',$params['goodId'])->find();
    }

    //创建订单
    public function createOrder($activity,$reward){
        Db::connect('shanxi_tidb_databases')->startTrans();
        try {
            //减积分
            UserScore::where('userid',self::$userId)->setDec('tuan_score',$activity->integral);

            //插入抽奖记录
            $exchangeId = Db::connect('shanxi_tidb_databases')->name('exchanged')->insertGetId([
                'userid'=>self::$userId,
                'product_num'=>1,
                'status' => 1,
                'activity_id' => self::$activityId,
                'type' => 2,
                'exchanged_credit' => $activity->integral,
                'create_time'=>date('Y-m-d H:i:s',time()),
                'update_time'=>date('Y-m-d H:i:s',time()),
            ]);

            $orderId = Db::connect('shanxi_tidb_databases')->name('order')->insertGetId([
                'credit_exchange_id'=>$exchangeId,
                'order_number'=>create_uuid(),
                'userid'=>self::$userId,
                'product_id'=>self::$goodId,
                'product_name'=>self::$goodMess['name'],
                'image' => self::$goodMess['image'],
                'status'=>6,
                'number'=>1,
                'price'=>$activity->integral,
                'total_price'=>$activity->integral,
                'type'=>2,
                'create_time'=>date('Y-m-d H:i:s',time()),
                'update_time'=>date('Y-m-d H:i:s',time()),
            ]);

            Db::connect('shanxi_tidb_databases')->name('order_detail')->insert([
                'order_id'=>$orderId,
                'status'=>'待领奖',
                'msg' =>'奖品待领奖状态',
                'create_time'=>date('Y-m-d H:i:s',time()),
                'update_time'=>date('Y-m-d H:i:s',time()),
            ]);

            Db::connect('shanxi_tidb_databases')->name('winning_list')->insert([
                'userid'=>self::$userId,
                'order_id'=>$orderId,
                'activity_id'=>self::$activityId,
                'goods_id'=>self::$goodId,
                'reward'=>$reward,
                'goods_name'=>self::$goodMess['name'],
                'is_get'=>0,
                'create_time'=>date('Y-m-d H:i:s',time()),
                'update_time'=>date('Y-m-d H:i:s',time()),
            ]);

            Product::where('id',self::$goodId)->setDec('number');
            LuckDrawGoods::where(['luck_draw_id'=>self::$activityId,'product_id'=>self::$goodId])->setDec('stock');
            //推送到延时队列(20210425凯文要求去掉此功能)
//            self::pushDelayedQueue($orderId);
            Db::connect('shanxi_tidb_databases')->commit();
            return ['orderId'=>$orderId,'goodName'=>self::$goodMess['name']];
        }catch (Exception $e){
            Db::connect('shanxi_tidb_databases')->rollback();
            \exception($e->getMessage());
        }
    }

    //增加积分
    public function addPoints($exchangeId,$goodName,$integral,$reward,$point){
//        Db::connect('shanxi_tidb_databases')->startTrans();
//        try {
//            $order = Order::create([
//                'credit_exchange_id'=>$exchangeId,
//                'order_number'=>create_uuid(),
//                'userid'=>self::$userId,
//                'product_id'=>self::$goodId,
//                'product_name'=>$goodName,
//                'status'=>6,
//                'number'=>1,
//                'price'=>$integral,
//                'total_price'=>$integral,
//                'type'=>2,
//
//            ]);
//
//            OrderDetail::create([
//                'order_id'=>$order->id,
//                'status'=>'已领奖',
//                'msg' =>'已领奖（积分）',
//            ]);
//
//            WinningList::create([
//                'userid'=>self::$userId,
//                'activity_id'=>self::$activityId,
//                'goods_id'=>self::$goodId,
//                'reward'=>$reward,
//                'goods_name'=>$goodName,
//                'is_get'=>1,
//            ]);
//
//            UserScore::where('userid',self::$userId)
//                ->inc('award_score',$point)
//                ->inc('tuan_score',$point)
//                ->inc('total_score',$point);
//
//            AwardScore::create([
////                'scoreid'=>,
//                'userid'=>self::$userId,
//                'score'=>$point,
//                'otype'=>7,
//            ]);
//
//            Product::where('id',self::$goodId)->setDec('number');
//            LuckDrawGoods::where(['luck_draw_id'=>self::$activityId,'product_id'=>self::$goodId])->setDec('stock');
//            Db::connect('shanxi_tidb_databases')->commit();
//        }catch (Exception $e){
//            Db::connect('shanxi_tidb_databases')->rollback();
//            \exception($e->getMessage());
//        }
    }

    //订单推进延时队列   5天没领奖 自动取消订单
    protected static function pushDelayedQueue($orderId){
        $jobHandlerClassName  = 'app\luckdraw\job\Order';
        $jobQueueName  	      = "shanxiOrderOverTime";
        $jobDataArr['orderId']= $orderId;
        $time = 432000;
        $isPushed = Queue::later($time,$jobHandlerClassName, $jobDataArr, $jobQueueName);
        if($isPushed === false){
            \exception('进入队列失败');
        }
    }
}