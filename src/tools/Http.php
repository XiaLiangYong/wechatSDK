<?php
namespace wechatsdk\tools;

use wechatsdk\model\Base;

/**
 * Created by PhpStorm.
 * User: xialiangyong
 * Date: 2017/5/15
 * Time: 14:14
 */
class Http
{

    /**
     * GET 请求
     * @param string $url
     */
    public static function get($url)
    {
        if (function_exists('curl_init')) {
            $oCurl = curl_init();
            if (stripos($url, "https://") !== FALSE) {
                curl_setopt($oCurl, CURLOPT_SSL_VERIFYPEER, FALSE);
                curl_setopt($oCurl, CURLOPT_SSL_VERIFYHOST, FALSE);
                curl_setopt($oCurl, CURLOPT_SSLVERSION, 1); //CURL_SSLVERSION_TLSv1
            }
            curl_setopt($oCurl, CURLOPT_URL, $url);
            curl_setopt($oCurl, CURLOPT_RETURNTRANSFER, 1);
            $sContent = curl_exec($oCurl);
            $aStatus = curl_getinfo($oCurl);
            curl_close($oCurl);
            $log = [];
            $log['url'] = $url;
            $log['aStatus'] = $aStatus;
            Base::getInstance()->log('get请求=' . json_encode($log, JSON_UNESCAPED_UNICODE));
            if (intval($aStatus["http_code"]) == 200) {
                //如果是json 直接返回json
                if (Helper::isJson($sContent)) {
                    return Helper::parseJson($sContent);
                }
                return $sContent;
            } else {
                return false;
            }
        } else {
            throw new Exception('Do not support CURL function.');
        }
    }


    /**
     * POST 请求
     * @param string $url
     * @param array $param
     * @param boolean $post_file 是否文件上传
     * @return string content
     */
    public static function post($url, $param, $post_file = false)
    {
        if ($post_file) {
            $param = static::initUploadData($param);
        }
        if (function_exists('curl_init')) {
            $oCurl = curl_init();
            if (stripos($url, "https://") !== FALSE) {
                curl_setopt($oCurl, CURLOPT_SSL_VERIFYPEER, FALSE);
                curl_setopt($oCurl, CURLOPT_SSL_VERIFYHOST, false);
                curl_setopt($oCurl, CURLOPT_SSLVERSION, 1); //CURL_SSLVERSION_TLSv1
            }
            if (is_string($param) || $post_file) {
                $strPOST = $param;
            } else {
                $strPOST = json_encode($param, JSON_UNESCAPED_UNICODE);
            }
            curl_setopt($oCurl, CURLOPT_URL, $url);
            curl_setopt($oCurl, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($oCurl, CURLOPT_POST, true);
            curl_setopt($oCurl, CURLOPT_POSTFIELDS, $strPOST);
            $sContent = curl_exec($oCurl);
            $aStatus = curl_getinfo($oCurl);
            curl_close($oCurl);
            if (Base::getInstance()->isDebug()) {
                $log = [];
                $log['url'] = $url;
                $log['param'] = $param;
                $log['post_file'] = $post_file;
                $log['aStatus'] = $aStatus;
                Base::getInstance()->log('post请求=' . json_encode($log, JSON_UNESCAPED_UNICODE));
            }

            if (intval($aStatus["http_code"]) == 200) {
                //如果是json 直接返回json
                if (Helper::isJson($sContent)) {
                    return Helper::parseJson($sContent);
                }
                return $sContent;
            } else {
                throw new \Exception('post请求失败');
            }
        } else {
            throw new Exception('Do not support CURL function.');
        }
    }


    /**
     * 兼容@识别的php方法必须是5.5以下识别
     * @param $file 文件名 form-data方式上传可不传文件名，函数自动处理
     * 初始化上传文件处理
     */
    public static function initUploadData($file = '')
    {
        if ($_FILES) {
            $tmpname = $_FILES['media']['name'];
            $file = $_FILES['media']['tmp_name'];
            $tmpType = $_FILES['media']['type'];
            if (version_compare("5.5", PHP_VERSION, "<")) {
                $data = [
                    "media" => new \CURLFile(realpath($file), $tmpType, $tmpname),
                ];
            } else {
                $data = array(
                    'media' => '@' . realpath($file) . ";type=" . $tmpType . ";filename=" . $tmpname
                );
            }
        } else {
            if (version_compare("5.5", PHP_VERSION, "<")) {
                $data = [
                    "media" => new \CURLFile(realpath($file))
                ];
            } else {
                $data = array(
                    'media' => '@' . realpath($file)
                );
            }
        }
        return $data;
    }

}
