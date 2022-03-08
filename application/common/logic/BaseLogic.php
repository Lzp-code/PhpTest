<?php


namespace app\common\logic;


class BaseLogic
{
    protected static $instance;

    private function __construct()
    {
    }

    public static function getInstance()
    {
        if(static::$instance instanceof static){
            return static::$instance;
        }

        static::$instance = new static();
        return static::$instance;
    }


    private function __clone()
    {
        // TODO: Implement __clone() method.
    }


}