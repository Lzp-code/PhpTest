<?php

namespace app\index\job;

use think\Db;
use think\queue\Job;

class LzpTestQueue
{
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
