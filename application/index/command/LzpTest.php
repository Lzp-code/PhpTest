<?php
/**
 * 测试测试测试-测试测试测试
 * @author lzp
 */
namespace app\index\command;


use think\console\Command;
use think\console\Input;
use think\console\Output;
use think\Db;


class LzpTest extends Command
{
    protected function configure()
    {
        $this->setName('LzpTest')->setDescription('测试测试测试');
    }

    protected function execute(Input $input, Output $output)
    {


            $data = array();
            $data['question'] = time();
            $data['update_time'] = date('Y-m-d H:i:s', time());
            $data['create_time'] = date('Y-m-d H:i:s', time());
            Db::name('appeal_safety_question')->data($data)->insert();



    }
}