<?php
namespace app\kafka\logic;

use think\Db;
use think\Queue;


class Kafka
{
    public function __construct()
    {
        date_default_timezone_set('PRC');
    }

    /*
    * Produce
    */
    public function Producer($topic, $value , $url)
    {
        $config = \Kafka\ProducerConfig::getInstance();
        //  $config->setMetadataRefreshIntervalMs(10000);
        $config->setMetadataBrokerList($url);
        $config->setBrokerVersion('1.0.0');
        $config->setRequiredAck(1);
        $config->setIsAsyn(false);
        $config->setProduceInterval(500);
        $producer = new \Kafka\Producer(function () use($value,$topic){
            return [
                [
                    'topic' => $topic,
                    'value' => $value,
                    'key' => '',
                ],
            ];
        });

        $res = [];
        $producer->success(function ($result)use(&$res){
            $res = ['status'=>1,'msg'=>'推送成功'];
            //return ['status'=>-1,'msg'=>$result];
        });
        $producer->error(function ($errorCode)use(&$res){
            $res = ['status'=>-1,'msg'=>$errorCode];
            //   return ['status'=>-1,'msg'=>$errorCode];
        });
        $producer->send(true);
        return $res;
    }

    /*
    * Consumer
    */
    public function consumer($group,$topics,$url){
        $config = \Kafka\ConsumerConfig::getInstance();
//        $config->setMetadataRefreshIntervalMs(500);
        $config->setMetadataBrokerList($url);
        $config->setGroupId($group);
        $config->setBrokerVersion('1.0.0');
        $config->setTopics([$topics]);
//        $config->setOffsetReset('earliest');

        try{


            $consumer = new \Kafka\Consumer();

//            print_r($consumer);
//            exit();

//            $consumer->setLogger($logger);
            $consumer->start(function($topic, $part, $message) {

//                print_r($message);
                return $message['message']['value'];
            });


        }catch(\Exception $e){



            return $e->getMessage();

        }



    }



}
