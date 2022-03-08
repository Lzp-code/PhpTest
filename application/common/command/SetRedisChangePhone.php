<?php
/**
 * Created by PhpStorm.
 * User: mark
 * Date: 2020/3/31
 * Time: 14:38
 */

namespace app\common\command;
use think\Db;
use think\Exception;
use think\facade\Cache;
use think\console\Command;
use think\console\Input;
use think\console\Output;

class SetRedisChangePhone extends Command
{
    private static $PhoneKey = "S:edit_phone:";

    protected function configure()
    {
        $this->setName('SetRedisChangePhone')->setDescription('修改手机号设置缓存');
    }

    protected function execute(Input $input, Output $output)
    {
        $output->writeln('数据更新开始');
        var_dump(self::setRedis());
        $output->writeln('数据更新结束');
    }

    public static function setRedis(){
        $re = Db::name('user_edit_phone')->where('status','=',1)->select();
        $redis = Cache::handler();
        $redis->select(10);
        foreach ($re as $key => $value){
            if ($value['appli_time']<(time()-(3600*24*3))){
                $redis->setex(self::$PhoneKey.$value['id'],10,1);
            }else{
                $redis->setex(self::$PhoneKey.$value['id'],($value['appli_time']-(time()-(3600*24*3))),1);
            }
            echo $value['id'];
        }
    }
}