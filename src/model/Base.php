<?php
namespace wechatsdk\model;

use wechatsdk\tools\WeixinUrl;


/**
 * Created by PhpStorm.
 * User: xialiangyong
 * Date: 2017/5/15
 * Time: 14:41
 */
class Base
{
    private $wechat = null;
    private $appid;
    private $appsecret;
    private $config = [];
    private $token;
    private $apiHost;
    private $encodingAesKey;
    private $debug = false;//是否开启debug模式 默认关闭
    private $_text_filter = true;
    //静态变量保存全局实例
    protected static $_instance;

    private $log = [];

    /**
     * 单例方法,用于访问实例的公共的静态方法
     */
    public static function getInstance()
    {
        if (!(self::$_instance instanceof self)) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    /**
     * @return null
     */
    public function getWechat()
    {
        return $this->wechat;
    }

    /**
     * @param null $wechat
     */
    public function setWechat($wechat)
    {
        $this->wechat = $wechat;
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
     * @return array
     */
    public function getConfig()
    {
        return $this->config;
    }

    /**
     * @return mixed
     */
    public function getApiHost()
    {
        return $this->apiHost;
    }

    /**
     * @param mixed $apiHost
     */
    public function setApiHost($apiHost)
    {
        $this->apiHost = $apiHost;
    }

    /**
     * @return mixed
     */
    public function getEncodingAesKey()
    {
        return $this->encodingAesKey;
    }

    /**
     * @param mixed $encodingAesKey
     */
    public function setEncodingAesKey($encodingAesKey)
    {
        $this->encodingAesKey = $encodingAesKey;
    }

    /**
     * @return bool
     */
    public function isDebug()
    {
        return $this->debug;
    }

    /**
     * @param bool $debug
     */
    public function setDebug($debug)
    {
        $this->debug = $debug;
    }

    /**
     * @return array
     */
    public function getLog()
    {
        return $this->log;
    }

    /**
     * @param array $log
     */
    public function setLog($log)
    {
        $this->log = $log;
    }

    /**
     * @return bool
     */
    public function isTextFilter()
    {
        return $this->_text_filter;
    }

    /**
     * @param bool $text_filter
     */
    public function setTextFilter($text_filter)
    {
        $this->_text_filter = $text_filter;
    }

    /**
     * 设置配置文件
     * @param array $config
     */
    public function setConfig($config)
    {
        $this->config = $config;
        if (isset($config['appid'])) {
            $this->setAppid($config['appid']);
        }
        if (isset($config['appsecret'])) {
            $this->setAppsecret($config['appsecret']);
        }
        if (isset($config['apiHost'])) {
            $this->setApiHost($config['apiHost']);
        } else {
            //不传默认
            $this->setApiHost('https://api.weixin.qq.com');
        }
        if (isset($config['token'])) {
            $this->setToken($config['token']);
        }
        if (isset($config['encodingAesKey'])) {
            $this->setEncodingAesKey($config['encodingAesKey']);
        }
        //设置debug模式
        if (isset($config['debug'])) {
            $this->setDebug($config['debug']);
        }
        if (isset($config['text_filter'])) {
            $this->setTextFilter($config['text_filter']);
        }
    }

    /**
     * 只有debug模式才打印日志
     * 此处回调wechat的方法
     * 写入日主方法
     */
    public function log($data = '')
    {
        if ($this->isDebug()) {
            call_user_func_array([$this->getWechat(), 'log'], [$data]);
        }
    }

    /**
     * 获取请求地址
     */
    public function getApiUrl($interface, $urlParams = [])
    {
        $map = WeixinUrl::$map;
        if (isset($map[$interface])) {
            $url = $this->getApiHost() . $map[$interface];
            if (strpos($url, '?') === false) {
                $url .= '?';
            }
            if ($interface != 'getAccessToken') {
                if (strpos($url, 'access_token') === false) {
                    $url .= 'access_token=' . BasicSupport::getInstance()->getAccessToken() . '&';
                }
            }
            //填入公共参数
            if ($urlParams) {
                if (is_array($urlParams)) {
                    $url .= http_build_query($urlParams);
                } else {
                    $url .= $urlParams;
                }
            }
            return $url;
        } else {
            throw new \Exception('接口地址不存在');
        }
    }
}
