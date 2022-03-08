<?php


namespace app\common\command;


use think\console\Command;
use think\console\Input;
use think\console\Output;
use think\Db;
use think\facade\Cache;

class ergodicRedisDatabse extends Command
{

    protected function configure()
    {
        $this->setName('ergodicRedisDatabse')->setDescription('遍历redis库中的key');
    }

    protected function execute(Input $input, Output $output)
    {
        $redis =Cache::handler();
        $redis->select(8);
        /* 设置遍历的特性为不重复查找，该情况下扩展只会scan一次，所以可能会返回空集合 */
        $redis->setOption(\Redis::OPT_SCAN, \Redis::SCAN_NORETRY);

        $it = NULL;
        $pattern = '*';
        $count = 50;  // 每次遍历50条，注意是遍历50条，遍历出来的50条key还要去匹配你的模式，所以并不等于就能够取出50条key

        do
        {
            $keysArr = $redis->scan($it, $pattern, $count);

            if ($keysArr)
            {
                foreach ($keysArr as $key)
                {
//                    echo $key . "\n";
                    $resArr = explode('_',$key);
                    if(!is_numeric($resArr[1])){
                        continue;
                    }

                    $linkMess  = Db::name('study_link')
                        ->field('id,name')
                        ->where('status',1)
                        ->cache('hbgqt_study_link',600)
                        ->find();

                    $isStudy = Db::connect('study_tidb_databases')
                        ->table('hbgqt_study_count')->where("member_id={$resArr[1]} and link_id>={$linkMess['id']}")
                        ->find();
                    
                    if(!$isStudy){
                        echo $resArr[1] . "\n";
                        $memberMess = Db::connect('slave_databases')->table('hbgqt_league_member')->field('identity,leagueid')->where('userid='.$resArr[1].' and is_delete=0')->find();
                        $omess = Db::name('organization')->field('id,name,code,otype,node_path,organize_type,is_deleted,dissolution')->where('id','=',$memberMess['leagueid'])->find();
                        if(empty($omess)){
                            $startTime  = config('common.youthStudy_startTime');
                            $endTime    = config('common.youthStudy_endTime');
                            $key        = md5($startTime.$endTime.$resArr[1].'new').'_'.$resArr[1];
                            $redis->del($key);
                            continue;
                        }
                        $oname          = Db::connect('slave_databases')->table('hbgqt_organization')->field('name')->whereIn('id',$omess['node_path'])->column('name');
                        unset($oname[0]);unset($oname[1]);
                        $usermess       = Db::connect('slave_databases')->table('hbgqt_users')->field('realname,idcard,mobile')->where('id',$resArr[1])->find();
                        $isCadre        = Db::connect('slave_databases')->table('hbgqt_league_cadre')->where('userid='.$resArr[1].' and is_on!=120')->value('id');
//                        $perperiod_num  = Db::name('study_link')->where('status',1)->cache('hbgqt_study_link',7200)->value('name');
                        $memberIdentity = $memberMess['identity'];

                        if($usermess['idcard']){
                            $isChildren     = Db::connect('slave_databases')->table('hbgqt_children')->where(['idcard'=>$usermess['idcard'],'is_delete'=>0])->value('id');
                            $isInstructor   = Db::connect('slave_databases')->table('hbgqt_instructor')->where(['idcard'=>$usermess['idcard'],'is_delete'=>0])->value('id');
                        }else{
                            $isChildren = '';
                            $isInstructor = '';
                        }

                        $data['member_id']   = $resArr[1];
                        $data['organize_id'] = $omess['id'];
                        $data['code']        = $omess['code'];
                        $data['oname']       = implode(',',$oname);
                        $data['create_time'] = 1638778670;
                        $data['table_date']  = date('Y-m-d',1638778670);
                        $data['otype']       = $omess['otype'];
                        $data['status']      = 1;
                        $data['click_count'] = 1;
                        $data['idcard']      = $usermess['idcard'];
                        $data['mobile']      = $usermess['mobile'];
                        $data['realname']    = $usermess['realname'];
                        $data['organize_name'] = $omess['name'];
                        $data['period_num']  = $linkMess['name'];
                        $data['is_cadre']    = $isCadre?1:(($memberIdentity==1)?3:2);
                        $data['is_children'] = $isChildren?1:0;
                        $data['is_instructor'] = $isInstructor?1:0;
//                        $data['user_ip']     = $omess['ip'];
                        $data['link_id']     = $linkMess['id'];

                        Db::connect('study_tidb_databases')->table('hbgqt_study_count')->insert($data);
                    }

                }
            }

        } while ($it > 0);   //每次调用 Scan会自动改变 $it 值，当$it = 0时 这次遍历结束 退出循环


        echo '---------------------------------------------------------------------------------' . "\n";


    }

}