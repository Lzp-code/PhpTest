<?php


namespace app\common\exception;


use app\common\exception\extend\ExceptionAbstract;
use app\common\lib\ApiCode;
use Exception;
use org\Response;
use think\exception\Handle;
use think\exception\HttpException;
use think\exception\PDOException;
use think\exception\RouteNotFoundException;
use think\exception\ValidateException;
use think\facade\Log;
use Throwable;

/**
 * @deprecated
 */
class Http extends Handle
{
    /**
     * 记录异常信息（包括日志或者其它方式记录）
     *
     * @access public
     * @param Throwable $exception
     * @return void
     */
    public function report($exception)
    {
        try {
            //SQL错误记录
            if ($exception instanceof PDOException) {
                $sql = $exception->getData()['Database Status']['Error SQL'];
                if (!empty($sql)) {
                    Log::error('PDOException: ' . $sql);
                }
            }

            //应用错误记录
            if ($exception instanceof ExceptionAbstract) {
                $log = 'ServiceException: [' . $exception->getCode() . ']' . $exception->getMessage();
                $data = $exception->getData();
                if (!empty($data)) {
                    $log .= ' ' . json_encode($data);
                }
                Log::error($log);
            }

            if (!config('log.record_trace')) {
                Log::error($exception->getTraceAsString());
            }

            // 使用内置的方式记录异常日志
            parent::report($exception);

        } catch (Exception $ex) {
            Log::error($ex->getMessage());
            Log::error($ex->getTraceAsString());
        }
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @access public
     * @param Throwable $e
     * @return \think\Response
     */
    public function render($e)
    {
        if ($e instanceof RouteNotFoundException || $e instanceof HttpException && $e->getCode() === 404) {
            //路由错误或者http错误
            return json(['code' => ApiCode::NOT_FOUND, 'msg' => '404 Not found', 'info' => $e->getMessage()]);
        } else if ($e instanceof ValidateException) {
            //参数验证错误
            return json(['code' => ApiCode::INVALID_PARAMETER, 'msg' => $e->getMessage()]);
        } else if ($e instanceof ExceptionAbstract) {
            //应用错误
            return json(['code' => $e->getServerCode(), 'msg' => $e->getMessage(), 'data' => $e->getData()], $e->getCode());
        }
        return json(['code' => ApiCode::ERROR, 'msg' => '系统错误']);
    }

}