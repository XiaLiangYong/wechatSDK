<?php
/**
 * 基础配置
 * Created by PhpStorm.
 * User: xialiangyong
 * Date: 2017/7/19
 * Time: 10:48
 */

namespace wechatsdk\model;


class Config
{

    private $token;//微信绑定验证时使用的token
    private $encodingaeskey;//encodingaeskey
    private $appid;//公众号身份标识
    private $appsecret;//公众平台API的权限获取所需密钥Key

    private $mch_id;//支付商户号
    private $key;//商户支付密钥
    private $notify_url;//异步通知回调地址  不能携带参数,需要先声明路由
    private $back_url;//支付成功同步回调地址

    private $apiclient_cert; //使用微信红包接口等功能时需要的证书,请登录微信支付后台下载
    private $apiclient_key;
    private $rootca;

    /**
     *
     * @param array $config
     */
    public function __construct($config = [])
    {
        isset($config['token']) && $this->setToken($config['token']);
        isset($config['encodingaeskey']) && $this->setEncodingaeskey($config['encodingaeskey']);
        isset($config['appid']) && $this->setAppid($config['appid']);
        isset($config['appsecret']) && $this->setAppsecret($config['appsecret']);
        isset($config['mch_id']) && $this->setMchId($config['mch_id']);
        isset($config['key']) && $this->setKey($config['key']);
        isset($config['notify_url']) && $this->setNotifyUrl($config['notify_url']);
        isset($config['back_url']) && $this->setBackUrl($config['back_url']);
        isset($config['apiclient_cert']) && $this->setApiclientCert($config['apiclient_cert']);
        isset($config['apiclient_key']) && $this->setApiclientKey($config['apiclient_key']);
        isset($config['rootca']) && $this->setRootca($config['rootca']);
    }

    /**
     * @return mixed
     */
    public function getToken()
    {
        return $this->token;
    }

    /**
     * @param mixed $token
     */
    public function setToken($token)
    {
        $this->token = $token;
    }

    /**
     * @return mixed
     */
    public function getEncodingaeskey()
    {
        return $this->encodingaeskey;
    }

    /**
     * @param mixed $encodingaeskey
     */
    public function setEncodingaeskey($encodingaeskey)
    {
        $this->encodingaeskey = $encodingaeskey;
    }

    /**
     * @return mixed
     */
    public function getAppid()
    {
        return $this->appid;
    }

    /**
     * @param mixed $appid
     */
    public function setAppid($appid)
    {
        $this->appid = $appid;
    }

    /**
     * @return mixed
     */
    public function getAppsecret()
    {
        return $this->appsecret;
    }

    /**
     * @param mixed $appsecret
     */
    public function setAppsecret($appsecret)
    {
        $this->appsecret = $appsecret;
    }

    /**
     * @return mixed
     */
    public function getMchId()
    {
        return $this->mch_id;
    }

    /**
     * @param mixed $mch_id
     */
    public function setMchId($mch_id)
    {
        $this->mch_id = $mch_id;
    }

    /**
     * @return mixed
     */
    public function getKey()
    {
        return $this->key;
    }

    /**
     * @param mixed $key
     */
    public function setKey($key)
    {
        $this->key = $key;
    }

    /**
     * @return mixed
     */
    public function getNotifyUrl()
    {
        return $this->notify_url;
    }

    /**
     * @param mixed $notify_url
     */
    public function setNotifyUrl($notify_url)
    {
        $this->notify_url = $notify_url;
    }

    /**
     * @return mixed
     */
    public function getBackUrl()
    {
        return $this->back_url;
    }

    /**
     * @param mixed $back_url
     */
    public function setBackUrl($back_url)
    {
        $this->back_url = $back_url;
    }

    /**
     * @return mixed
     */
    public function getApiclientCert()
    {
        return $this->apiclient_cert;
    }

    /**
     * @param mixed $apiclient_cert
     */
    public function setApiclientCert($apiclient_cert)
    {
        $this->apiclient_cert = $apiclient_cert;
    }

    /**
     * @return mixed
     */
    public function getApiclientKey()
    {
        return $this->apiclient_key;
    }

    /**
     * @param mixed $apiclient_key
     */
    public function setApiclientKey($apiclient_key)
    {
        $this->apiclient_key = $apiclient_key;
    }

    /**
     * @return mixed
     */
    public function getRootca()
    {
        return $this->rootca;
    }

    /**
     * @param mixed $rootca
     */
    public function setRootca($rootca)
    {
        $this->rootca = $rootca;
    }
}