<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2021/3/16
 * Time: 18:55
 */

namespace app\common\command;

use EasyWeChat\Kernel\Exceptions\Exception;
use think\console\Command;
use think\console\Input;
use think\console\input\Argument;
use think\console\input\Option;
use think\console\Output;
use think\Db;

class RepairMinProData extends Command
{
    protected function configure()
    {
        $this->setName('RepairMinProData')->setDescription('修复小程序错误数据');
    }

    protected function execute(Input $input, Output $output)
    {
//        $list = Db::query("select * from hbgqt_users where idcard not REGEXP '[0-9]{18}|[0-9]{17}X' ");

        $cursor = Db::name('users')->where("idcard not REGEXP '[0-9]{18}|[0-9]{17}X'")->cursor();
        foreach($cursor as $user){
            //查找长度18的身份证
            $correct_user_info = Db::name('users')->where([['idcard','like',$user['idcard'].'%'],['id','<>',$user['id']],['realname','=',$user['realname']]])->find();

            if(empty($correct_user_info)){ //没有正确用户数据 直接删除
//                Db::name('users')->where('id','=',$user['id'])->update(['is_delete'=>3,'update_time'=>date('Y-m-d H:i:s',time())]); //把这批用户数据先改成3怕会有问题
                Db::startTrans();
                try{

                    Db::name('users')->where('id','=',$user['id'])->delete(); //用户表
                    Db::name('study_count')->where('member_id','=',$user['id'])->delete(); //大学习
                    Db::name('league_member')->where('userid','=',$user['id'])->delete(); //团员
                    Db::name('instructor')->where('user_id','=',$user['id'])->delete(); //辅导员
                    Db::commit();

                }catch(Exception $e){
                    echo $e->getMessage();
                }

//                echo '修改{单}用户[成功] 旧用户'.$user['id']."<br/>";

            }else{ //把旧的userid替换成正确的userid
                Db::startTrans();
                try{

                    Db::name('users')->where('id','=',$user['id'])->delete(); //用户表
                    Db::name('study_count')->where('member_id','=',$user['id'])->update(['member_id'=>$correct_user_info['id']]); //大学习


                    $mem = Db::name('league_member')->where('userid','=',$user['id'])->find();
                    if(!empty($mem)){ //已经有数据则删除
                        Db::name('league_member')->where('userid','=',$user['id'])->delete(); //团员
                    }else{ //没数据就修改
                        Db::name('league_member')->where('userid','=',$user['id'])->update(['userid'=>$correct_user_info['id']]); //团员
                    }

                    Db::name('instructor')->where('user_id','=',$user['id'])->update(['user_id'=>$correct_user_info['id']]); //辅导员
                    Db::commit();

                    echo '修改{多}用户[成功] 旧用户'.$user['id'].'|新用户:'.$correct_user_info['id']."<br/>";

                }catch(Exception $e){

                    Db::rollback();

                }

                echo '修改{多}用户[成功] 旧用户'.$user['id'].'|新用户:'.$correct_user_info['id']."<br/>";

//                exit;
            }
        }



    }





}