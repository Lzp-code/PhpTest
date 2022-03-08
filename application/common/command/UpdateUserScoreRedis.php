<?php
/**
 * Created by PhpStorm.
 * User: mark
 * Date: 2021/1/6
 * Time: 13:55
 */

namespace app\common\command;

use think\console\Input;
use think\console\Command;
use think\console\Output;
use think\Db;
use think\facade\Cache;

class UpdateUserScoreRedis extends Command
{
    protected function configure()
    {
        $this->setName('UpdateUserScoreRedis')->setDescription('批量更新用户积分数据到redis');
    }

    protected function execute(Input $input, Output $output)
    {
        $redis =Cache::handler();
        $redis->select(6);

        $output->writeln('更新开始');
        Db::connect('hebei_tidb_databases')->name('user_score')
            ->chunk(1000, function($users)use($redis) {
                foreach ($users as $user) {

                    $redis->zAdd('Rank:user:All', $user['total_score'], $user['userid']);
                    echo $user['id'];
                    echo '--';

                }

            });
        $output->writeln('更新结束');

    }
}