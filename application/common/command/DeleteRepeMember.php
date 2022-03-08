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

class DeleteRepeMember extends Command
{
    protected function configure()
    {
        $this->setName('DeleteRepeMember')->setDescription('删除团员重复数据');
    }

    protected function execute(Input $input, Output $output)
    {
        $output->writeln('删除重复数据开始');

        Db::name('league_member')->field('max(id) as id,userid,count(userid) as count_userid')->group('userid')->having('count_userid > 1')->chunk(200,function($data){
            foreach($data as $k=>$v){
                $res = Db::name('league_member')->where([['userid','=',$v['userid']],['id','<>',$v['id']]])->delete();
            }

        });

        $output->writeln('删除重复数据完成');
    }
}