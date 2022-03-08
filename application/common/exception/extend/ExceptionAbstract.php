<?php
namespace app\common\exception\extend;

use RuntimeException;
use Throwable;
use app\common\lib\ApiCode;

/**
 * @deprecated
 */
abstract class ExceptionAbstract extends RuntimeException{
    protected $data;
    protected $serverCode;
    protected $codeArr;

    public function __construct($serverCode = 0, $data = null, $message = "", Throwable $previous = null)
    {
        $httpStatusCode = 500;
        $this->data = $data;

        if($serverCode !== 0 && is_numeric($serverCode)){
            if(isset($this->codeArr[$serverCode])){
                $message = ($message === "") ? $this->codeArr[$serverCode]['msg'] : $message;
                $httpStatusCode = $this->codeArr[$serverCode]['HttpCode'];
                $this->serverCode = $serverCode;
            }else{
                $this->serverCode = 0;
            }
        }

        parent::__construct($message, $httpStatusCode, $previous);
    }

    public final function getData()
    {
        return $this->data;
    }

    public final function getServerCode()
    {
        return $this->serverCode;
    }
}