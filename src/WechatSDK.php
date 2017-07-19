<?php

namespace wechatsdk;

/**
 * 微信sdk封装
 * Created by PhpStorm.
 * User: xialiangyong
 * Date: 2017/7/10
 * Time: 9:39
 */
class WechatSDK
{
    private static $instances = [];

    /**
     * 获取实例
     */
    public static function getInstance($key, $params = [])
    {
        if (isset(self::$instances[$key])) {
            return self::$instances[$key];
        }
        $className = '\\wechatsdk\\model\\' . $key;
        if ($params) {
            $instance = new $className($params);
        } else {
            $instance = new $className();
        }
        self::$instances[$key] = $instance;
        return $instance;
    }

    /**
     *  销毁指定实例
     */
    public static function remove($key)
    {
        if (isset(self::$instances[$key])) {
            $instance = self::$instances[$key];
            unset($instance);
            unset(self::$instances[$key]);
        }
    }

    /**
     * 销毁容器内所有实例
     */
    public static function removeAll()
    {
        foreach (self::$instances as $key => $instance) {
            unset($instance);
            unset(self::$instances[$key]);
        }
    }
}