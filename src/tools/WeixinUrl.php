<?php
namespace wechatsdk\tools;
/**
 * Created by PhpStorm.
 * User: xialiangyong
 * Date: 2017/5/15
 * Time: 15:15
 */
class WeixinUrl
{
    public static $map = [
        'getAccessToken' => '/cgi-bin/token?',
        'getCallbackip' => '/cgi-bin/getcallbackip?',
        'uploadMedia' => '/cgi-bin/media/upload?',
        'getMedia' => '/cgi-bin/media/get?',
        'addNewsMaterial' => '/cgi-bin/material/add_news?',
        'getMaterial' => '/cgi-bin/material/get_material?',
        'delMaterial' => '/cgi-bin/material/del_material?',
        'updateNews' => '/cgi-bin/material/update_news?',
        'getMaterialCount' => '/cgi-bin/material/get_materialcount?',
        'getMaterialList' => '/cgi-bin/material/batchget_material?',
        'addMaterial' => '/cgi-bin/material/add_material?',
        'getMediaByJssdk' => '/cgi-bin/media/get/jssdk?',
        'mediaUploadimg' => '/cgi-bin/media/uploadimg?',
        'createMenu' => '/cgi-bin/menu/create?',
        'getMenu' => '/cgi-bin/menu/get?',
        'deleteMenu' => '/cgi-bin/menu/delete?',
    ];
}