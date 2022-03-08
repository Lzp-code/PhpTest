<?php
/**
 * User: Gkh
 * Date: 2022/1/6
 * Time: 10:00
 */

namespace app\common\command;

use app\common\exception\AppMinorException;
use Exception;
use Redis;
use RuntimeException;
use think\console\Command;
use think\console\Input;
use think\console\Output;
use think\Container;
use think\facade\Log;

declare(ticks=1);

class RedisException extends RuntimeException
{

}

class WorkerException extends RuntimeException
{
}

abstract class AbstractSubscriberCommand extends Command
{
    protected $app;
    protected $redis;
    protected $last;
    protected $redisConfig = ['host' => '127.0.0.1', 'port' => 6379, 'password' => '', 'database' => 0, 'channel' => '', 'pop_wait_time' => 6];
    protected $shutdown;

    public function __construct($name = null)
    {
        $this->app = Container::get('app');
        parent::__construct($name);
    }

    protected function signal()
    {
        $this->shutdown = true;
    }

    protected function execute(Input $input, Output $output)
    {
        if (function_exists('pcntl_signal')) {
            pcntl_signal(SIGINT, array($this, 'signal'));
            pcntl_signal(SIGTERM, array($this, 'signal'));
        }

        $idle = true;
        while (true) {
            try {
                $this->saveLast();
                if ($this->shutdown)
                    break;

                $this->init($idle);
                try {
                    $data = $this->redis->brPop($this->redisConfig['channel'], $this->redisConfig['pop_wait_time']);
                } catch (Exception $e) {
                    throw new RedisException($e->getMessage(), $e->getCode(), $e);
                }
                if (empty($data)) {
                    $idle = true;
                    continue;
                }

                $data = json_decode($data[1], true);
                if ($data === null) {
                    $idle = true;
                    continue;
                }

                $idle = false;
                try {
                    $this->executeWorker($data);
                } catch (AppMinorException $e) {
                } catch (Exception $e) {
                    $this->last = $data;
                    throw new WorkerException($e->getMessage(), $e->getCode(), $e);
                }
            } catch (Exception $e) {
                $idle = true;
                $this->reportException($e);
                try {
                    if ($e instanceof RedisException) {
                        $this->closeRedis();
                    } else if ($e instanceof WorkerException) {
                        $this->closeWorker();
                    }
                } catch (Exception $exception) {
                    $this->reportException($exception);
                }
            }
        }
    }

    protected function saveLast()
    {
        if ($this->last !== null) {
            $this->initRedis();
            $last = $this->last;
            $last['reties'] = empty($last['reties']) ? 1 : $last['reties'] + 1;
            try {
                $this->push($last);
            } catch (Exception $e) {
                throw new RedisException($e->getMessage(), $e->getCode(), $e);
            }
            $this->last = null;
        }
    }

    protected function initRedis()
    {
        if ($this->redis === null) {
            $redis = new Redis();
            $redis->connect($this->redisConfig['host'], $this->redisConfig['port']);
            if (!empty($this->redisConfig['password']))
                $redis->auth($this->redisConfig['password']);
            $redis->select($this->redisConfig['database']);
            $this->redis = $redis;
        }
    }

    protected function push($data)
    {
        $this->redis->lPush($this->redisConfig['channel'], json_encode($data, JSON_UNESCAPED_UNICODE));
    }

    protected function init($idle)
    {
        try {
            $this->initWorker($idle);
        } catch (Exception $e) {
            throw new WorkerException($e->getMessage(), $e->getCode(), $e);
        }
        try {
            $this->initRedis();
        } catch (Exception $e) {
            throw new RedisException($e->getMessage(), $e->getCode(), $e);
        }
    }

    protected function initWorker($idle)
    {
    }

    protected function executeWorker($data)
    {
        throw new RuntimeException('You must override this method.');
    }

    protected function reportException($exception)
    {
        $data = [
            'file' => $exception->getFile(),
            'line' => $exception->getLine(),
            'message' => $exception->getMessage(),
            'code' => $exception->getCode(),
        ];
        $log = $this->getExceptionName($exception) . " [{$data['code']}] {$data['message']} [{$data['file']}:{$data['line']}]";
        Log::error($log);
    }

    protected function getExceptionName($exception)
    {
        $name = get_class($exception);
        $pos = strrpos($name, '\\');
        $name = $pos === false ? $name : substr($name, $pos + 1);
        return '[ ' . $name . ' ]';
    }

    protected function closeRedis()
    {
        if ($this->redis !== null) {
            try {
                $this->redis->close();
            } catch (Exception $exception) {
                $this->redis = null;
                throw $exception;
            }
            $this->redis = null;
        }
    }

    protected function closeWorker()
    {
    }

}