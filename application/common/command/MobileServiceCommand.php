<?php
/**
 * User: Gkh
 * Date: 2022/1/6
 * Time: 15:00
 */

namespace app\common\command;

use Exception;
use org\CMPPSubmitTwo;
use RuntimeException;
use think\console\Input;
use think\console\Output;


abstract class MobileServiceCommand extends AbstractSubscriberCommand
{

    protected $constantService;
    protected $mobileUpService;
    protected $cmppConfig;
    protected $cmpp;
    protected $inited = false;

    public function __construct($name = null)
    {
        parent::__construct($name);
        $this->constantService = $this->app->get('\app\system\service\ConstantService');
        $this->mobileUpService = $this->app->get('\app\system\service\MobileUpService');
        $this->redisConfig['pop_wait_time'] = 1;
        $this->redisConfig['host'] = config('queue.host');
        $this->redisConfig['port'] = config('queue.port');
        $this->redisConfig['password'] = config('queue.password');
        $this->redisConfig['database'] = config('queue.select');
    }

    protected function initialize(Input $input, Output $output)
    {
        if (!$this->inited) {
            $this->cmppConfig = $this->initCmppConfig($this->constantService->getConstantByTypeAndName('config', 'sms'));
            if ($this->cmppConfig === null)
                throw new RuntimeException('CMPP参数配置异常');
            $this->inited = true;
        }
    }

    protected function initCmppConfig($config): array
    {
        throw new RuntimeException('You must override this method.');
    }

    protected function initWorker($idle)
    {
        $isnew = false;
        if ($this->cmpp === null) {
            $this->cmpp = new CMPPSubmitTwo($this->cmppConfig, $this->mobileUpService);
            $isnew = true;
        }
        if ($isnew || $idle) {
            $this->cmpp->CMPP_ACTIVE_TEST();
        }
    }

    protected function executeWorker($data)
    {
        if ($data['time'] < time()) {
            return;
        }
        $this->cmpp->CMPP_SUBMIT($data['mobile'], $data['message']);
    }

    protected function closeWorker()
    {
        if ($this->cmpp !== null) {
            try {
                $this->cmpp->closeSocket();
            } catch (Exception $exception) {
                $this->cmpp = null;
                throw $exception;
            }
            $this->cmpp = null;
        }
    }

}