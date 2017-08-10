<?php
/**
 * 基础支持
 * Created by PhpStorm.
 * User: xialiangyong
 * Date: 2017/5/15
 * Time: 15:02
 */

namespace wechatsdk\model;


use wechatsdk\tools\Helper;
use wechatsdk\tools\Http;

class BasicSupport extends Singleton
{
    /**
     * 获取access_token
     */
    public function getAccessToken()
    {
        $baseModel = Base::getInstance();
        $cacheName = 'access_token_' . $baseModel->getAppid();
        //读取缓存
        $accessTokenInfo = $baseModel->getWechat()->getCache($cacheName);
        if ($accessTokenInfo) {
            //判断过期时间
            $accessTokenInfo_arr = json_decode($accessTokenInfo, true);
            if (($accessTokenInfo_arr['time'] + $accessTokenInfo_arr['expires_in']) > time()) {
                return $accessTokenInfo_arr['access_token'];
            }
        }
        $url = $baseModel->getApiUrl('getAccessToken', [
            'grant_type' => 'client_credential',
            'appid' => $baseModel->getAppid(),
            'secret' => $baseModel->getAppsecret()
        ]);
        $accessTokenInfo_arr = Http::get($url);
        if ($accessTokenInfo_arr) {
            $access_token = $accessTokenInfo_arr['access_token'];
            $expire_time = $accessTokenInfo_arr['expires_in'];
            $accessTokenInfo_arr['time'] = time();
            $baseModel->getWechat()->setCache($cacheName, json_encode($accessTokenInfo_arr), $expire_time);
            //写入缓存 默认写入json文件
            return $access_token;
        }
        return false;
    }

    /**
     * 获取微信服务器IP地址
     */
    public function getCallbackip()
    {
        $baseModel = Base::getInstance();
        $url = $baseModel->getApiUrl('getCallbackip');
        return Http::get($url);
    }
}
