<?php
/**
 * User: Gkh
 * Date: 2021/11/25
 * Time: 11:04
 */

namespace app;

use Exception;
use think\Container;
use think\exception\Handle;
use think\exception\HttpException;
use think\exception\PDOException;
use think\exception\RouteNotFoundException;
use think\exception\ValidateException;
use app\common\exception\AppException;
use app\common\exception\AppMinorException;
class ExceptionHandle extends Handle
{


    public function render($e)
    {
        ob_clean();
        if ($e instanceof RouteNotFoundException || $e instanceof HttpException && $e->getCode() === 404) {//如果是请求路径没找到或HTTP请求结果为404，返回404
            $data = ['code' => 404, 'msg' => '404 Not found'];
        } else if ($e instanceof ValidateException) {//如果是验证时出错，调用RuntimeException
            $data = ['code' => 5000, 'msg' => $e->getError()];
        } else if ($e instanceof AppException || $e instanceof AppMinorException) {//如果是运行时出错，调用RuntimeException
            $data = ['code' => $e->getCode(), 'msg' => $e->getMessage(), 'data' => $e->getData()];
        } else {
            $data = ['code' => 999, 'msg' => Container::get('app')->isDebug() ? $e->getMessage() : '错误信息：'.$e->getMessage()]; //不能直接返回具体的异常信息，这样暴露了系统细节，不安全
        }
        if (!empty($e->debug))
            $data['debug'] = $e->debug;
        return json($data);
    }

}