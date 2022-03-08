<?php


namespace app\common\command;


class yiDongSendCode extends MobileServiceCommand
{
    public function __construct($name = null)
    {
        parent::__construct($name);
        $this->redisConfig['host'] = config('queue.host');
        $this->redisConfig['port'] = config('queue.port');
        $this->redisConfig['password'] = config('queue.password');
        $this->redisConfig['database'] = config('queue.select');
        $this->redisConfig['channel'] = 'yiDongSendCode';
    }

    protected function configure()
    {
        $this->setName('yiDongSendCode')->setDescription('发送移动号码短信');
    }

    protected function initCmppConfig($config): array
    {
        return $config['yidong'];
    }

}