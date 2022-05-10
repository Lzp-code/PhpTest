<?php


namespace app\luckdraw\job;


use think\Db;
use think\queue\Job;

class Order
{
    public function fire(Job $job,$mess){
        Db::connect('shanxi_tidb_databases')->startTrans();
        try{
            $status = Db::connect('shanxi_tidb_databases')->name('order')->where('id',$mess['orderId'])->value('status');
            if($status == 6){
                Db::connect('shanxi_tidb_databases')->name('order')->where('id',$mess['orderId'])->update(['status'=>4]);
                Db::connect('shanxi_tidb_databases')->name('order_detail')->insert([
                    'order_id'=>$mess['orderId'],
                    'status'=>'超时订单',
                    'msg' =>'订单超时未领奖,已自动取消',
                    'update_time'=>date('Y-m-d H:i:s',time()),
                ]);
                Db::connect('shanxi_tidb_databases')->name('winning_list')->where('order_id',$mess['orderId'])->update(['is_delete'=>1]);
                Db::connect('shanxi_tidb_databases')->commit();
                $job->delete();
            }else{
                $job->delete();
            }

        }catch (\Exception $e){
            echo $mess['orderId'].'error:'.$e->getMessage();
            Db::connect('shanxi_tidb_databases')->rollback();
            $job->release(10);
        }
    }
}