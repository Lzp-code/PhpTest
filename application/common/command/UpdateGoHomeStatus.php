<?php
namespace app\common\command;

use think\console\Command;
use think\console\Input;
use think\console\input\Argument;
use think\console\input\Option;
use think\console\Output;
use think\Db;

class UpdateGoHomeStatus extends Command
{
    protected function configure()
    {
        $this->setName('UpdateGoHomeStatus')->setDescription('批量更新用户回家状态');
    }

    protected function execute(Input $input, Output $output)
    {
        dump('修改开始');

        $map[] = ['members.league_status','=',92];
        $map[] = ['members.go_home_status','=',0];
        $map[] = ['users.mobile','>',0];
        $map[] = ['members.identity','=',0];

/*        $cursor = Db::name('users users')
            ->field('count(users.id)')
            ->join('league_member members','users.id = members.userid','left')
            ->where($map)->select();
        dump($cursor);exit;*/

        $cursor = Db::name('users users')
            ->field('users.id,users.realname,members.go_home_status,members.league_status,members.member_card_status')
            ->join('league_member members','users.id = members.userid','left')
            ->where($map)->cursor();

            foreach($cursor as $k=>$v){
                $res = Db::name('league_member')->where('userid','=',$v['id'])->update(['go_home_status'=>3]);
                dump($res);
            }

        dump('修改成功');
    }
}
