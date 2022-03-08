<?php


namespace app\common\command;


class yiWangSendCode extends MobileServiceCommand
{
    public function __construct($name = null)
    {
        parent::__construct($name);
        $this->redisConfig['channel'] = 'yiWangSendCode';
    }

    protected function configure()
    {
        $this->setName('yiWangSendCode')->setDescription('发送电信联通号码短信');
    }

    protected function initCmppConfig($config): array
    {
        return $config['yiwang'];
    }

}