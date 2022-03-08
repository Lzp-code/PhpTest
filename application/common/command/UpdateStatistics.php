<?php
namespace app\common\command;

use think\console\Command;
use think\console\Input;
use think\console\input\Argument;
use think\console\input\Option;
use think\console\Output;
use think\Db;
use think\Queue;
use think\facade\Cache;
use think\facade\Config;

class UpdateStatistics extends Command
{
    protected function configure()
    {
        $this->setName('UpdateStatistics')->setDescription('批量同步PV数据到数据库中');
    }

    /**
     * 旧数据处理并生成日志数据
     * @param $out_league_info
     * @param $to_league_info
     * @param $user_id
     * @return bool
     */
    public function execute(Input $input, Output $output)
    {
        $arr = Config::get('common.statistics_arr');

        $now_time = date('Y-m-d',time());

        $value = Db::name('user_pv_statistics')->where('time','=',$now_time)->find();

        if(!empty($value)){
            foreach($arr as $k=>$v){
                $type = Cache::get($now_time.'_'.$v);

                if($type > $value[$v]){
                    Db::name('user_pv_statistics')->where('time','=',$now_time)->update([$v=>$type]);
                }else{
                    Cache::set($now_time.'_'.$v,$value[$v]);
                }

            }
        }else{
            Db::name('user_pv_statistics')->where('time','=',$now_time)->insert(['time'=>$now_time]);
            echo '每日插入第一条数据';
        }

    }

}
