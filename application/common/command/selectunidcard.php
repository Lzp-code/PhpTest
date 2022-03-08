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

class selectunidcard extends Command
{
    protected function configure()
    {
        $this->setName('selectunidcard')->setDescription('查询不同的身份证');
    }

    protected function execute(Input $input, Output $output)
    {
        Db::name('users')->where('idcard','not null')->where([['is_delete','=',0],['realname','<>','']])->chunk(100,function($users){
            foreach($users as $k=>$v){
                $info = Db::connect('study_tidb_databases')->name('study_count')->where([['idcard','=',$v['idcard']],['realname','=',$v['realname']],['member_id','<>',$v['id']]])->find();
                /*if(!empty($info)){
                    dump($info);
                }*/
                dump($info);
            }
        });

/*        $res = Db::name('users')->where('idcard','not null')->where([['is_delete','=',0],['realname','<>','']])->cursor();

        foreach($res as $k=>$v){
            $info = Db::connect('study_tidb_databases')->name('study_count')->where([['idcard','=',$v['idcard']],['realname','=',$v['realname']],['member_id','<>',$v['id']]])->find();
            if(!empty($info)){
                dump($info);
            }

            dump($v);exit;
        }*/


    }
}