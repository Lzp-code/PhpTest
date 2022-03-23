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
     * 安装crontab
     * yum install crontabs
     * crontab -l   查看当前用户定时任务列表
     * crontab -e   编辑当前用户定时任务列表
     * crontab -r   删除用户定时任务文件
     * crontab -i   删除用户定时任务文件时给出确认
     *
     *
     *
     * 在服务器写入定时的时间，并指向该命令
     * 比如示例——每小时的第15分钟执行一次：15 * * * * php /data/www/hebei/think LzpTest（按照执行命令行的模式：php think LzpTest，只是这里要指向任务所在的文件夹，所以写成了php /data/www/hebei/think LzpTest）
     * crontab 中的 command 尽量使用绝对路径，否则会经常因为路径错误导致任务无法执行。
     * 然后重启：/bin/systemctl restart crond.service
     *
     * 同一个任务，如果下一个任务开始时上一个任务未结束，则会同时存在多个计划任务
     * 如果执行过程中要停止，需要杀进程：
     * ps -ef | grep LzpTest    然后  kill 进程号
     *
     *
     * 制定一个自己的定时任务：
     * https://blog.csdn.net/weixin_43161811/article/details/88371571
     *https://blog.csdn.net/asasasasaq/article/details/90693570
     * Crontab命令参考：
     * https://cloud.tencent.com/developer/article/1666275
     * https://www.cnblogs.com/ftl1012/p/crontab.html
     * https://zhuanlan.zhihu.com/p/413666659
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