<?php
/**
 * Created by PhpStorm.
 * User: mark
 * Date: 2019/9/23
 * Time: 9:37
 */

namespace app\common\command;
use think\console\Command;
use think\console\Input;
use think\console\Output;
use think\Db;

class CadreJoinToMember extends Command
{
    protected function configure()
    {
        $this->setName('CadreJoinToMember')->setDescription('团员审核表信息迁移最终表');
    }

    protected function execute(Input $input, Output $output)
    {
        $output->writeln('数据转化开始');

        $join_data = Db::name('league_join_cadre')->where('status','in',['0', '3'])->chunk(200,function($j_data){

            foreach($j_data as $k=>$v){
                $times = time();

                unset($j_data[$k]['id']);
                unset($j_data[$k]['error']);
                unset($j_data[$k]['nowstep']);
                unset($j_data[$k]['resubmit']);

                $j_data[$k]['status'] = 3;
                $j_data[$k]['go_home_status'] = 3;
                $j_data[$k]['league_status'] = 95;
                $j_data[$k]['create_time'] = $times;
                $j_data[$k]['update_time'] = $times;
                $j_data[$k]['work_unit'] = '';
                $j_data[$k]['identity'] = 0;

            }
            Db::name('league_cadre')->insertAll($j_data, false);
        });

        $output->writeln('数据转化完成');
    }
}