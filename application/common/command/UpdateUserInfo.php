<?php
namespace app\common\command;

use think\console\Command;
use think\console\Input;
use think\console\input\Argument;
use think\console\input\Option;
use think\console\Output;
use think\Db;
use think\Queue;

class UpdateUserInfo extends Command
{
    protected function configure()
    {
        $this->setName('UpdateUserInfo')->setDescription('批量处理关系转接数据');
    }

    protected function execute(Input $input, Output $output)
    {
        dump('开始修改数据');
        $error_count = 0;

        $count = Db::name('users')->where('idcard','<>','')->count();

        $residue = ceil($count / 100);
        $start = 5000000;

        for($i=50000;$i<$residue;$i++){
            dump($i);
            dump($start);
            dump('--------------------');
            $cursor = Db::name('users')->limit($start,100)->field('id,idcard')->where('idcard','<>','')->select();

            foreach($cursor as $k=>$v){

                if(!empty($v['idcard']) && is_string(checkIdCard($v['idcard']))){
                    $update = [];
                    $update['idcard'] = null;
                    $update['sex'] = 0;
                    $update['age'] = 0;
                    $update['birthday'] = '';

                    $res = Db::name('users')->where('id','=',$v['id'])->update($update);

                    $error_count++;
                }
            }
            $start += 100;
        }

/*        $list = Db::name('users')->field('id,idcard')->chunk(100,function($cursor){
            foreach($cursor as $k=>$v){
                if(!empty($v['idcard']) && is_string(checkIdCard($v['idcard']))){
                    $update = [];
                    $update['idcard'] = '';
                    $update['sex'] = 0;
                    $update['age'] = 0;
                    $update['birthday'] = '';

                    $res = Db::name('users')->where('id','=',$v['id'])->update($update);
                    dump($v['id']);
                }
            }
        });*/


/*        $cursor = Db::name('users')->field('id,idcard')->cursor();

        foreach($cursor as $k=>$v){
            if(!empty($v['idcard']) && is_string(checkIdCard($v['idcard']))){
                $update = [];
                $update['idcard'] = '';
                $update['sex'] = 0;
                $update['age'] = 0;
                $update['birthday'] = '';

                $res = Db::name('users')->where('id','=',$v['id'])->update($update);
                $count++;
            }
        }*/


        dump('修改数据结束');
        trace('共有'.$error_count.'条数据','error');
        dump('共有'.$error_count.'条数据');
    }
}
