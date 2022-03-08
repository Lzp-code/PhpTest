<?php
/**
 * Created by PhpStorm.
 * User: mark
 * Date: 2020/3/27
 * Time: 10:50
 */

namespace app\common\command;


use think\console\Command;
use think\console\Input;
use think\console\input\Argument;
use think\console\input\Option;
use think\console\Output;
use think\Db;

class UpdateOrgChildren extends Command
{

    protected function configure()
    {
        $this->setName('UpdateOrgChildren')->setDescription('更新团组织和少先队组织的乱插入');
    }

    protected function execute(Input $input, Output $output)
    {
        $output->writeln('组织数据更新开始');

        Db::name('organization')
            ->where([
                    ['type','=','1'],
                    ['code','like','000.018%']
                ]
            )
            ->chunk(1000,function($org_info){
                foreach ($org_info as $key =>$value){
                    Db::name('league_member')->where('leagueid','=',$value['id'])->update(['is_delete'=>1,'update_time'=>time()]);
                    Db::name('league_cadre')->where('leagueid','=',$value['id'])->update(['is_on'=>120,'update_time'=>time()]);
                    Db::name('admin')->where('organize_id','=',$value['id'])->update(['dissolution'=>1,'update_time'=>time()]);
                    Db::name('organization')->where('id','=',$value['id'])->update(['dissolution'=>1,'update_time'=>time()]);
                    $num =  Db::name('organization')->where([['pid','=',$value['pid']],['is_deleted','=',0],['dissolution','=',0]])->count();
                    if($num<=0){
                        Db::name('organization')->where('id','=',$value['pid'])->update(['nodetype'=>2,'update_time'=>time()]);
                    }
                 echo $value['id'];
                 echo PHP_EOL;
                }
            });

        $output->writeln('组织 数据更新结束');
    }
}