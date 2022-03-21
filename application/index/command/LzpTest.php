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



    /**
     * 在command目录写入要执行的逻辑
     * 然后在command.php写好命令行
     *
     * 在服务器写入定时的时间，并指向该命令
     * 比如示例——每小时的第15分钟执行一次：15 * * * * php /data/www/hebei/think LzpTest（按照执行命令行的模式：php think LzpTest，只是这里要指向任务所在的文件夹，所以写成了php /data/www/hebei/think LzpTest）
     * 然后重启：/bin/systemctl restart crond.service
     * 如果执行过程中要停止，需要杀进程：
     * ps -ef | grep LzpTest    然后  kill 进程号
     *
     *
     * 制定一个自己的定时任务：
     *https://blog.csdn.net/asasasasaq/article/details/90693570
     * Crontab命令参考：
     * https://cloud.tencent.com/developer/article/1666275
     *
     */


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