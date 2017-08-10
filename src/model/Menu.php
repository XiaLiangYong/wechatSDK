<?php
/**
 * Created by PhpStorm.
 * User: xialiangyong
 * Date: 2017/5/18
 * Time: 10:40
 */

namespace wechatsdk\model;


use wechatsdk\tools\Http;

class Menu extends Singleton
{
    /**
     *
     * {
     * "button": [
     * {
     * "type": "click",
     * "name": "今日歌曲",
     * "key": "V1001_TODAY_MUSIC"
     * },
     * {
     * "name": "菜单",
     * "sub_button": [
     * {
     * "type": "click",
     * "name": "赞一下我们",
     * "key": "V1001_GOOD"
     * }
     * ]
     * }
     * ]
     * }
     * 自定义菜单创建接口
     * @param $data
     * @return string
     */
    public function createMenu($data)
    {
        return Http::post(Base::getInstance()->getApiUrl('createMenu'), $data);
    }

    /**
     * 自定义菜单查询接口
     * @return array|bool|mixed
     */
    public function getMenu()
    {
        return Http::get(Base::getInstance()->getApiUrl('getMenu'));
    }

    /**
     * 定义菜单删除接口
     * @return array|bool|mixed
     */
    public function deleteMenu()
    {
        return Http::get(Base::getInstance()->getApiUrl('deleteMenu'));
    }
}