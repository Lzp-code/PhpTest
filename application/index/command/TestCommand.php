<?php
/**
 * Created by PhpStorm.
 * User: mark
 * Date: 2020/6/9
 * Time: 19:43
 */

namespace app\regiment\command;

use think\console\Command;
use think\console\Input;
use think\console\Output;
use think\Db;
use think\Queue;
use think\console\input\Argument;

class TestCommand extends Command
{
    protected function configure()
    {
        $this->setName('TestCommand')
            ->addArgument('type',Argument::OPTIONAL, "type")
            ->setDescription('测试命令行');
    }

    protected function execute(Input $input, Output $output)
    {
        $type = trim($input->getArgument('type'));
        if(empty($type)){
            echo '缺少type参数';
            return;
        }
        Db::name('user_certificate')->where('id',123)->update(['is_delete'=>1]);
        echo '成功';
    }
}