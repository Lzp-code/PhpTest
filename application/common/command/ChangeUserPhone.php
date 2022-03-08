<?php
/**
 * Created by PhpStorm.
 * User: mark
 * Date: 2020/3/20
 * Time: 16:34
 */

namespace app\common\command;
use think\Db;
use think\Exception;
use think\facade\Cache;
use think\console\Command;
use think\console\Input;
use think\console\Output;

class ChangeUserPhone extends Command
{
    protected function configure()
    {
        $this->setName('ChangeUserPhone')->setDescription('修改手机号');
    }

    protected function execute(Input $input, Output $output)
    {
//     // ini_set('default_socket_timeout', -1);
//        $redis = Cache::handler();
//     //   $redis->config('notify-keyspace-events','Ex');
//        $redis->select(10);
        $redis = new \Redis();
        $redis ->connect(config('queue.host'),config('queue.port'));
        $redis ->auth(config('queue.password'));
        $redis ->select(10);
      //  $redis->setOption(\Redis::OPT_READ_TIMEOUT, -1);

        $redis->psubscribe(['__keyevent@10__:expired'],function($redis, $pattern, $channel, $msg){
            dump($msg);
            var_dump(self::change_phone($msg));
        });

    }

    public static function change_phone($values){
        $id =explode(':',$values)[2]??0;
        $re = Db::name('user_edit_phone')->where('id','=',$id)->find();
        if(!$re){
            return false;
        }
        // 启动事务
        Db::startTrans();
        try {

            $where = [];
            $where[]=['id','=',$re['userid']];

            $data = [];
            $data['status']= 3;
            $data['update_time']= date('Y-m-d H:i:s',time());

            //$red=Db::name('user_edit_phone')->where('id','=',$id)->fetchSql()->update($data);
           // var_dump($red);exit;
            $rrdata = Db::execute('update hbgqt_user_edit_phone set status = 3 ,update_time = "'.$data['update_time'].'" where id = '.$id);

            if($rrdata===false){
                throw new Exception('取消手机修改失败');
            }

            if(!(Db::name('users')->where('mobile','=',$re['phone'])->find())){
//                Db::name('users')->where($where)->update(
//                    ['mobile'=>$re['phone']]
//                );
                $rrrdata = Db::execute("update hbgqt_users set mobile = '".$re['phone']."' where id = ".$re['userid']);
                if($rrrdata===false){
                    throw new Exception('取消手机修改失败');
                }
               // Db::name('wechat')->where('user_id','=',$re['userid'])->update(['user_id'=>0]);
                $rdata = Db::execute('update hbgqt_wechat set user_id = 0  where user_id = '.$re['userid']);
                if($rdata===false){
                    throw new Exception('取消手机修改失败');
                }
            }

            // 提交事务
            Db::commit();
        } catch (\Exception $e) {
            // 回滚事务
            Db::rollback();
            return  false;
        }

        return true;

    }

}