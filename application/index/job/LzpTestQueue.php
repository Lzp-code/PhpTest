<?php

namespace app\index\job;

use think\Db;
use think\queue\Job;

class LzpTestQueue
{
//关于queue的更多信息：https://blog.csdn.net/will5451/article/details/80434174


//        在Linux查看任务数量，可以在终端输入：jobs -l
//        重启所有的消息队列：:php think queue:restart

//    在config文件夹下会有一个queue.php的配置文件，需要改redis的配置，然后再写代码

    //根目录执行
    //work模式——启动一个work进程执行消息队列
    //php think queue:work --queue LzpTestQueue

    //--queue  LzpTestQueue  //要处理的队列的名称
    //--daemon            //是否循环执行，加上此参数则循环执行，如果不加该参数，则该命令处理完下一个消息就退出
    //--delay  0         //如果本次任务执行抛出异常且任务未被删除时，设置其下次执行前延迟多少秒,默认为0
    //--force            //系统处于维护状态时是否仍然处理任务，并未找到相关说明
    //--memory 128       //该进程允许使用的内存上限，以 M 为单位
    //--sleep  3         //如果队列中无任务，则sleep多少秒后重新检查(work+daemon模式)或者退出(listen或非daemon模式)
    //--tries  2          //如果任务已经超过尝试次数上限，则触发‘任务尝试次数超限’事件，默认为0


    //listen模式——创建一个父进程，由此父进程创建work子进程执行队列
    //php think queue:listen --queue LzpTestQueue

    //--queue  LzpTestQueue    //监听的队列的名称
    //--delay  0         //如果本次任务执行抛出异常且任务未被删除时，设置其下次执行前延迟多少秒,默认为0
    //--memory 128       //该进程允许使用的内存上限，以 M 为单位
    //--sleep  3         //如果队列中无任务，则多长时间后重新检查，daemon模式下有效
    //--tries  0         //如果任务已经超过重发次数上限，则进入失败处理逻辑，默认为0
    //--timeout 6        //创建的work子进程的允许执行的最长时间，以秒为单位


    protected $data = [];

    public function fire(Job $job, $data)
    {
        //....这里执行具体的任务
        if ($job->attempts() > 1) {
            //通过这个方法可以检查这个任务已经重试了几次了
            $job->delete();
        }
        $this->data = $data;
        $this->testQueue();
        echo '已执行...' .PHP_EOL;
        //如果任务执行成功后 记得删除任务，不然这个任务会重复执行，直到达到最大重试次数后失败后，执行failed方法
        $job->delete();
    }

    /**
     * 测试队列
     * @return bool
     * @throws \PHPMailer\PHPMailer\Exception
     */
    public function testQueue()
    {
        Db::name('appeal_safety_question')->data($this->data)->insert();
        return true;
    }
}
