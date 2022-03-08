<?php
/**
 * User: Gkh
 * Date: 2021/12/29
 * Time: 10:40
 */

namespace app\common\command;


use think\console\Command;
use think\console\Input;
use think\console\Output;
use think\Container;


class CleanMobileUpCommand extends Command
{
    protected $app;
    protected $mobileUpService;

    public function __construct($name = null)
    {
        $this->app = Container::get('app');
        $this->mobileUpService = $this->app->get('\app\system\service\MobileUpService');
        parent::__construct($name);
    }

    protected function configure()
    {
        $this->setName('cleanMobileUp')
            ->setDescription('清理过期上行短信数据（每分钟执行一次）');
    }

    protected function execute(Input $input, Output $output)
    {
        $this->mobileUpService->removeExpired();
    }
}