<?php
/**
 * 单例模式 支持继承
 * Created by PhpStorm.
 * User: xialiangyong
 * Date: 2017/5/15
 * Time: 15:20
 */

namespace wechatsdk\model;


class Singleton
{
    static $models = [];

    protected function __construct()
    {
        //disallow new instance
    }

    protected function __clone()
    {
        //disallow clone
    }

    static public function getInstance()
    {
        $name = get_called_class();
        if (!isset(self::$models[$name])) {
            self::$models[$name] = new $name();
        }
        return self::$models[$name];
    }

}