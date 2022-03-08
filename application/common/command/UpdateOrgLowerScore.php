<?php
/**
 * Created by PhpStorm.
 * User: mark
 * Date: 2020/7/15
 * Time: 15:35
 */

namespace app\common\command;
use think\console\Command;
use think\console\Input;
use think\console\Output;
use think\Db;
use think\Exception;
use think\facade\Log;

class UpdateOrgLowerScore extends Command
{
    protected function configure()
    {
        $this->setName('UpdateOrgLowerScore')->setDescription('累计组织下级积分总数');
    }

    protected function execute(Input $input, Output $output)
    {
        $output->writeln('更新开始');
//        Db::connect('hebei_tidb_databases')
//            ->name('org_score')
//            ->chunk(1000, function($userss) {
//                foreach ($userss as $users){
//                    $sum = $this->getOrgSum($users['leagueid']);
//                    $this->updateScore($users['id'],$sum);
//                }
//
//            });
        Db::connect('hebei_tidb_databases')
            ->name('organization')
            ->where([
                ['is_deleted','=',0],
                ['dissolution','=',0],
                ['type','=',1]
            ])
            ->chunk(1000, function($userss) {
                foreach ($userss as $users){
                    $sum = $this->getOrgSum($users['code']);
                    $this->updateScore($users['id'],$sum);
                }

            },'code','desc');

        $output->writeln('更新结束');
    }


    public function getOrgSum($res){

        //要下一级的，所以要加 。
       $res = $res.'.%';

       $sum = Db::connect('hebei_tidb_databases')
            ->name('organization')
            ->alias('o')
            ->join('hbgqt_org_score os','o.id = os.leagueid')
            ->where([
                    ['o.is_deleted','=',0],
                    ['o.dissolution','=',0],
                    ['o.type','=',1],
                    ['o.code','like',$res]
                ]
            )
            ->sum('total_score')??0;
       return $sum;
    }


    public function updateScore($id,$sum){
        Db::connect('hebei_tidb_databases')
            ->name('org_score')
            ->where('leagueid',$id)
            ->update(['subordinate_score'=>$sum]);
    }


}