<?php

namespace wechatsdk;

/**
 * 微信sdk封装
 * Created by PhpStorm.
 * User: xialiangyong
 * Date: 2017/7/10
 * Time: 9:39
 */
class Wechat
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
        $className = '\\wechatsdk\\model\\' . ucfirst($key);
        if ($params) {
            $instance = new $className($params);
        } else {
            $instance = new $className();
        }
        self::$instances[$key] = $instance;
        return $instance;
    }

    /**
     * 获取wechat
     * @param $params
     * @return Wechat
     */
    public static function getWechat()
    {
        $key = 'wechat';
        if (isset(self::$instances[$key])) {
            return self::$instances[$key];
        }
        $wechat = new Wechat();
        self::$instances[$key] = $wechat;
        return $wechat;
    }

    /**
     * @param $method
     * @param $params
     * @return mixed
     */
    public function __call($method, $params)
    {
        return call_user_func_array([self::getWechat(), $method], $params);
    }


    public static function __callStatic($method, $args)
    {
        return call_user_func_array([self::getWechat(), $method], $args);
    }

    /**
     *  销毁指定实例
     */
    public function remove($key)
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
    public function removeAll()
    {
        foreach (self::$instances as $key => $instance) {
            unset($instance);
            unset(self::$instances[$key]);
        }
    }

    /**
     * 设置缓存 按需重载
     * @param $key
     * @param $value
     * @param $expired
     */
    public function setCache($key, $value, $expired)
    {

    }

    /**
     * 获取缓存 按需重载
     * @param $key
     */
    public function getCache($key)
    {

    }

    /**
     * 删除缓存 按需重载
     */
    public function removeCache()
    {

    }

    /**
     * 记录日志 按需重载
     * @param $msg
     */
    public function log($msg)
    {

    }
}