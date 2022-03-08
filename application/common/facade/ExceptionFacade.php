<?php


namespace app\common\facade;

use app\common\exception\ExceptionFactor;
use think\Facade;

/**
 * @deprecated
 * @see ExceptionFactor
 * @package think\facade
 * @mixin ExceptionFactor
 * @method static ExceptionFactor name(string $name) 自定义异常的模块名
 * @method static ExceptionFactor serverCode(int $serverCode = 0) 自定义异常返回的状态码
 * @method static ExceptionFactor message(string $msg = '') 自定义异常返回的字符串信息
 * @method static ExceptionFactor data(array $data = null) 自定义异常返回的数据
 * @method static ExceptionFactor|\Exception exception() 输出异常
 */
class ExceptionFacade extends Facade
{
    protected static function getFacadeClass()
    {
        return 'app\common\exception\ExceptionFactor';
    }
}