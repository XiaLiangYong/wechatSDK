<?php

namespace wechatsdk;

/**
 * Created by PhpStorm.
 * User: xialiangyong
 * Date: 2017/7/10
 * Time: 9:39
 */
class WechatSDK
{
    /**
     * 架构函数
     * WechatSDK constructor.
     */
    public function __construct($config = [])
    {
        if (empty($config)) {
            throw new \Exception('请填写微信配置信息');
        }
        if (!isset($config['appid'])) {
            throw new \Exception('appid不能为空');
        }
        if (!isset($config['appsecret'])) {
            throw new \Exception('appsecret不能为空');
        }
    }
}