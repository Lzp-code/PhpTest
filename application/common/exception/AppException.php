<?php

namespace app\common\exception;

use app\common\ApiCode;
use RuntimeException;
use Throwable;

class AppException extends RuntimeException
{
    protected $data;

    public function __construct($message = '', $data = null, $code = ApiCode::SERVICE_ERROR, Throwable $previous = null)
    {
        $this->data = $data;
        parent::__construct($message, $code, $previous);
    }

    public function getData()
    {
        return $this->data;
    }
}