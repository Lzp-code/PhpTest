<?php
namespace app\common\command;

use think\console\Command;
use think\console\Input;
use think\console\input\Argument;
use think\console\input\Option;
use think\console\Output;
use think\Db;

class DelWechat extends Command
{
    protected function configure()
    {
        $this->setName('DelWechat')->setDescription('删除重复微信数据');
    }

    protected function execute(Input $input, Output $output)
    {
        dump('开始删除微信表数据');

        $wechat = Db::name('wechat')->field('*,count(openid) as  count_user')->group('openid')->having('count_user > 1')->chunk(200,function($list){

            foreach($list as $k=>$v){
                $res = Db::name('wechat')->where([['openid','=',$v['openid']],['id','<>',$v['id']]])->delete();

                dump($res);
            }

        });

        dump('删除微信数据结束');
    }
}
