<?php
/**
 * Created by PhpStorm.
 * User: mark
 * Date: 2020/7/14
 * Time: 15:37
 */

namespace app\common\command;

use app\score\model\LeagueMember;
use think\console\Command;
use think\console\Input;
use think\console\Output;
use think\Db;
use think\Exception;
use think\facade\Log;


class UpdateOrgScore extends Command
{
    protected function configure()
    {
        $this->setName('UpdateOrgScore')->setDescription('累计组织下所有团员和青年的个人积分总数');
    }

    protected function execute(Input $input, Output $output)
    {
        $output->writeln('更新开始');
         LeagueMember::alias('lm')
             ->field('lm.identity,lm.userid,lm.leagueid,lm.id')
             ->join('hbgqt_organization o','o.id = lm.leagueid')
             ->where([
                     ['lm.is_delete','=',0],
                     ['o.is_deleted','=',0],
                     ['o.dissolution','=',0],
                     ['o.type','=',1],
                     ['o.organize_type','=',config('common.organization_branch')],
                     ['lm.league_status','=',config('common.lea_normal_status')],
                 ]
             )
            ->chunk(1000, function($users) {

                foreach ($users as $user) {
                    $tuanyuan = Db::connect('hebei_tidb_databases')->name('user_score')->where('userid', $user['userid'])->value('total_score') ?? 0;
                    $orgCount = Db::connect('hebei_tidb_databases')
                        ->name('org_user_points')
                        ->where([
                            ['userid', '=', $user['userid']],
                            ['leagueid', '=', $user['leagueid']],
                            ['type', '=', $user['identity'] == 0 ? 1 : 2]
                        ])
                        ->count();
                    // 启动事务
                    Db::connect('hebei_tidb_databases')->startTrans();
                    try {

                        $lmData = [];
                        //团员
                        if ($user['identity'] == 0) {
                            $addTime = intval(floor($tuanyuan / 100));
                            $addTime = $addTime - $orgCount;

                            if ($addTime > 0) {
                                $scoreid = $this->DataShu($user, $addTime);
                                for ($i = 0; $i < $addTime; $i++) {
                                    $lmData[$i]['userid'] = $user['userid'];
                                    $lmData[$i]['leagueid'] = $user['leagueid'];
                                    $lmData[$i]['score'] = config('common.org_score')['UserPoints'];
                                    $lmData[$i]['create_time'] = date('Y-m-d H:i:s', time());
                                    $lmData[$i]['type'] = 1;
                                    $lmData[$i]['scoreid'] = $scoreid;
                                }
                                $kkss = Db::connect('hebei_tidb_databases')->name('org_user_points')->insertAll($lmData);
                                if (!$kkss) {
                                    throw new Exception('插入组织下的团员、青年积分数细则表失败');
                                }
                            }

                        } else {
                            $addTime = intval(floor($tuanyuan / 50));
                            $addTime = $addTime - $orgCount;

                            if ($addTime > 0) {
                                $scoreid = $this->DataShu($user, $addTime);
                                for ($i = 0; $i < $addTime; $i++) {
                                    $lmData[$i]['userid'] = $user['userid'];
                                    $lmData[$i]['leagueid'] = $user['leagueid'];
                                    $lmData[$i]['score'] = config('common.org_score')['UserPoints'];
                                    $lmData[$i]['create_time'] = date('Y-m-d H:i:s', time());
                                    $lmData[$i]['type'] = 2;
                                    $lmData[$i]['scoreid'] = $scoreid;
                                }
                                $kkss = Db::connect('hebei_tidb_databases')->name('org_user_points')->insertAll($lmData);
                                if (!$kkss) {
                                    throw new Exception('插入组织下的团员、青年积分数细则表失败');
                                }
                            }

                        }
                        // 提交事务
                        Db::connect('hebei_tidb_databases')->commit();

                    } catch (\Exception $e) {
                        // 回滚事务
                        Db::connect('hebei_tidb_databases')->rollback();
                        Log::write($e->getMessage(), 'error');
                    }
                }


            });

        $output->writeln('更新结束');
    }

    public function DataShu($user,$addTime){

        $orgSc =Db::connect('hebei_tidb_databases')
            ->name('org_score')
            ->where('leagueid',$user['leagueid'])
            ->find();

        if($orgSc){
            $kk = Db::connect('hebei_tidb_databases')
                ->name('org_score')
                ->where('leagueid',$user['leagueid'])
                ->update(
                    [
                        'user_points' =>	Db::raw('user_points+'.(config('common.org_score')['UserPoints']*$addTime)),
                        'total_score' =>	Db::raw('total_score+'.(config('common.org_score')['UserPoints']*$addTime)),
                        'update_time' =>	Db::raw('now()')
                    ]
                );
            if($kk<=0){
                throw new Exception('更新组织分数总表失败');
            }
            $org_score_id =  $orgSc['id'];
            return $org_score_id;
        }else{
            $kk = Db::connect('hebei_tidb_databases')->name('org_score')->insertGetId(
                [
                    'leagueid' =>$user['leagueid'],
                    'user_points' =>(config('common.org_score')['UserPoints']*$addTime),
                    'total_score' =>(config('common.org_score')['UserPoints']*$addTime),
                    'create_time' =>date('Y-m-d H:i:s',time()),
                ]
            );
            if(!$kk){
                throw new Exception('插入组织分数总表失败');
            }
            $org_score_id =  $kk;
            return $org_score_id;
        }
    }
}