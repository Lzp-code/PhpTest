<?php
namespace app\common\command;

use think\console\Command;
use think\console\Input;
use think\console\input\Argument;
use think\console\input\Option;
use think\console\Output;
use think\Db;

class UpdateOrgOtype extends Command
{
    protected function configure()
    {
        $this->setName('UpdateOrgOtype')->setDescription('批量修改组织otype字段');
    }

    protected function execute(Input $input, Output $output)
    {
        $cursor = Db::name('organization')
            ->field('id,otype,pid')
            ->where(['otype'=>6])
            ->cursor();

            foreach($cursor as $k=>$v){
                $otype = Db::name('organization')->where('id',$v['pid'])->value('otype');
                if($otype){
                    $res = Db::name('organization')->where('id','=',$v['id'])->update(['otype'=>$otype]);
                }else{
                    $res = Db::name('organization')->where('id','=',$v['id'])->update(['otype'=>0]);
                }

                dump($res);
            }

            echo '修改成功';
    }
}
