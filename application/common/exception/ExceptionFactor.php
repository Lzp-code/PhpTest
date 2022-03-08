<?php
namespace app\common\exception;

use think\Exception;

/**
 * @deprecated
 * 异常处理工厂
 * Class ExceptionFactor
 * @package app\common\exception
 * @author xin.guo
 */
class ExceptionFactor{
    private $name = '';
    private $data = null;
    private $message = '';
    private $serverCode = 0;

    public function name($name = ''): ExceptionFactor
    {
        $this->name = $name;
        return $this;
    }

    public function serverCode($serverCode = 0): ExceptionFactor
    {
        $this->serverCode = $serverCode;
        return $this;
    }

    public function message($msg = ''): ExceptionFactor
    {
        $this->message = $msg;
        return $this;
    }

    public function data($data = null): ExceptionFactor
    {
        $this->data = $data;
        return $this;
    }

    public function exception(){
        $name = 'app\common\exception\extend\\'.$this->name;
        if($name !== '' && class_exists($name)){
            throw new $name($this->serverCode, $this->data, $this->message);
        }else{
            throw new Exception($message = "",500);
        }
    }
}