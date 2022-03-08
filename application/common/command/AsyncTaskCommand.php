<?php
/**
 * User: Gkh
 * Date: 2022/1/6
 * Time: 18:00
 */

namespace app\common\command;

use app\common\exception\AppMinorException;

class AsyncTaskCommand extends AbstractSubscriberCommand
{
    public function __construct($name = null)
    {
        parent::__construct($name);
        $this->redisConfig = array_merge($this->redisConfig, config('app_async_task'));
    }

    public static function submit($cmd, $param, $expire = null)
    {
        $asyncTaskCommand = app()->get('app\common\command\AsyncTaskCommand');
        $asyncTaskCommand->initRedis();
        $asyncTaskCommand->push(['cmd' => $cmd, 'param' => $param, 'expire' => $expire === null ? time() + 60 : $expire]);
    }

    protected function configure()
    {
        $this->setName('asyncTask')->setDescription('异步调用');
    }

    protected function executeWorker($data)
    {
        if (!empty($data['expire']) && $data['expire'] < time()) {
            throw new AppMinorException('任务已过期', $data);
        }
        $arr = explode(':', $data['cmd']);
        if (count($arr) !== 2) {
            throw new AppMinorException('无效调用');
        }

        $instance = $this->app->get($arr[0]);
        $method = $arr[1];
        $param = empty($data['param']) ? [] : $data['param'];

        call_user_func_array(array($instance, $method), $param);
    }
}