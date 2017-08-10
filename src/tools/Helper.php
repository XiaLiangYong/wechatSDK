<?php

namespace wechatsdk\tools;
/**
 * Created by PhpStorm.
 * User: xialiangyong
 * Date: 2017/5/15
 * Time: 14:18
 */
class Helper
{
    /**
     * 工具函数：XML文档解析成数组
     * @param xml $xml
     * @return array
     */
    public static function extractXml($xml)
    {
        $data = (array)simplexml_load_string($xml, 'SimpleXMLElement', LIBXML_NOCDATA);
        return $data;
    }

    /**
     * 工具函数：将数组打包成xml格式返回给微信。
     * 该工具函数以前放在微信send发送消息里
     * @param array $array 要打包成xml格式的二维数组
     * @return xml $xml 打包成xml格式的信息
     */
    public static function array2Xml($array)
    {
        $xml = new SimpleXMLElement('<xml></xml>');
        self::data2xml($xml, $array);
        return $xml->asXML();
    }

    /**
     * 将数据用XML格式编码 dataToxml → data2xml
     * @param  object $xml XML对象
     * @param  mixed $data 数据
     * @param  string $item 数字索引时的节点名称
     * @return string
     */
    public static function data2xml($xml, $data, $item = 'item')
    {
        foreach ($data as $key => $value) {
            /* 指定默认的数字key */
            is_numeric($key) && $key = $item;

            /* 添加子元素 */
            if (is_array($value) || is_object($value)) {
                $child = $xml->addChild($key);
                self::data2xml($child, $value, $item);
            } else {
                if (is_numeric($value)) {
                    $child = $xml->addChild($key, $value);
                } else {
                    $child = $xml->addChild($key);
                    $node = dom_import_simplexml($child);
                    $node->appendChild($node->ownerDocument->createCDATASection($value));
                }
            }
        }
    }


    /**
     * 判断是否字符串是否是JSON
     *
     * @param type $string
     * @param type $datas
     * @return boolean
     */
    public static function isJson($string, $datas = array())
    {
        $datas = json_decode($string, true);
        if (json_last_error() == JSON_ERROR_NONE) {
            return $datas;
        }
        return false;
    }

    /**
     * 解析JSON编码，如果有错误，则返回错误并设置错误信息
     * @param json $json json数据
     * @return array
     */
    public static function parseJson($json)
    {
        $jsonArr = json_decode($json, true); // 标准utf-8格式解码json
        if (isset ($jsonArr ['errcode'])) {
            if ($jsonArr ['errcode'] == 0) {
                return $jsonArr;
            } else {
                //抛出错误异常
                throw new \Exception(ErrorCode::transformErrorCode($jsonArr ['errcode']));
            }
        } else {
            return $jsonArr;
        }
    }

    /**
     * 写入日志
     * @param $content
     */
    public static function log($content)
    {
        if (empty($content)) {
            return false;
        }
        $file = 'log.txt';
        file_put_contents($file, "$content\n", FILE_APPEND);
    }
}
