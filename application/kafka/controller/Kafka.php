<?php
namespace app\kafka\controller;

use think\Db;
use think\Queue;
use app\kafka\logic\Kafka as kafkaLogic;

class Kafka
{


    public function getUserInfo()
    {
        $id = 5451748;
        //查询用户基本信息
        $user = Db::name('users')->where('id',$id)->find();
        print_r($user);exit();
    }


    public function KafKaProducer(){

        $topic = 'lzptopic'; //配置
        $url = '192.168.80.130:9092'; //配置


        $arr = [
            'question' => time(),
            'update_time' => date('Y-m-d H:i:s', time()),
            'create_time' => date('Y-m-d H:i:s', time()),
        ];
        $value = json_encode ($arr, JSON_FORCE_OBJECT );

        $kafkaLogic = new kafkaLogic();

        $res = $kafkaLogic->Producer($topic, $value , $url);
        var_dump($res);
    }




    public function KafKaConsumer(){

        $topics = 'lzptopic';
        $url = '192.168.80.130:9092';
        $group = 'lzptopic';



        $kafkaLogic = new kafkaLogic();

        $res = $kafkaLogic->consumer($group,$topics,$url);

        print_r($res);

    }




}
