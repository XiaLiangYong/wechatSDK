<?php
/**
 * 素材管理
 * Created by PhpStorm.
 * User: xialiangyong
 * Date: 2017/5/16
 * Time: 13:31
 */

namespace wechatsdk\model;


use wechatsdk\tools\Helper;
use wechatsdk\tools\Http;

class MaterialManage extends Singleton
{
    /**
     * 新增临时素材
     * @param $type
     * @param $file
     * @return array
     * @throws \Exception
     */
    public function uploadMedia($type, $file = '')
    {
        $baseModel = Base::getInstance();
        $url = $baseModel->getApiUrl('uploadMedia', ['type' => $type]);
        return Http::post($url, $file, true);
    }


    /**
     * 高清语音素材获取接口
     * @param $media_id  媒体文件ID，即uploadVoice接口返回的serverID
     */
    public function getMediaByJssdk($media_id)
    {
        $baseModel = Base::getInstance();
        $url = $baseModel->getApiUrl('getMediaByJssdk', ['media_id' => $media_id]);
        return Http::get($url);
    }

    /**
     * 获取临时素材
     * @param $media_id
     * @return array
     * @throws \Exception
     */
    public function getMedia($media_id)
    {
        if (empty($media_id)) {
            throw new \Exception('media_id不能为空');
        }
        $baseModel = Base::getInstance();
        $url = $baseModel->getApiUrl('getMedia', ['media_id' => $media_id]);
        return Http::get($url);
    }

    /**
     * 新增永久图文素材
     * @param $news
     * @return array
     */
    public function addNewsMaterial($news = [])
    {
        //验证参数
        $baseModel = Base::getInstance();
        $url = $baseModel->getApiUrl('addNewsMaterial');
        return Http::post($url, $news);
    }

    /**
     * 新增其他类型永久素材
     * @param $type
     * @param string $data
     * @return array
     * @throws \Exception
     */
    public function addMaterial($type, $data = '')
    {
        //当为新增永久视频时候 title 视频素材的标题 introduction 视频素材的描述 不能为空
        if ($type == 'video') {
            if (!isset($data['title']) || empty($data['title'])) {
                throw new \Exception('新增永久视频素材时候title不能为空');
            }
            if (!isset($data['introduction']) || empty($data['introduction'])) {
                throw new \Exception('新增永久视频素材时候introduction不能为空');
            }
        }
        $baseModel = Base::getInstance();
        $url = $baseModel->getApiUrl('addMaterial', ['type' => $type]);
        return Http::post($url, $data, true);
    }

    /**
     * 上传图文消息内的图片获取URL
     * 本接口所上传的图片不占用公众号的素材库中图片数量的5000个的限制。图片仅支持jpg/png格式，大小必须在1MB以下。
     * @param $data
     */
    public function mediaUploadimg($data)
    {
        $baseModel = Base::getInstance();
        $url = $baseModel->getApiUrl('mediaUploadimg');
        return Http::post($url, $data, true);

    }

    /**
     * 获取永久素材
     * @param $media_id
     */
    public function getMaterial($media_id)
    {
        if (empty($media_id)) {
            throw new \Exception('media_id不能为空');
        }
        $baseModel = Base::getInstance();
        $url = $baseModel->getApiUrl('addNewsMaterial');
        return Http::post($url, ['media_id' => $media_id]);
    }


    /**
     * 删除永久素材
     * @param $media_id
     * @return array
     * @throws \Exception
     */
    public function delMaterial($media_id)
    {
        if (empty($media_id)) {
            throw new \Exception('media_id不能为空');
        }
        $baseModel = Base::getInstance();
        $url = $baseModel->getApiUrl('delMaterial');
        return Http::post($url, ['media_id' => $media_id]);
    }

    /**
     * 修改永久图文素材
     * @param $data
     * @return array
     */
    public function updateNews($data)
    {
        $baseModel = Base::getInstance();
        $url = $baseModel->getApiUrl('updateNews');
        return Http::post($url, $data);
    }

    /**
     * 获取素材总数
     * @return array
     */
    public function getMaterialCount()
    {
        $baseModel = Base::getInstance();
        $url = $baseModel->getApiUrl('getMaterialCount');
        return Http::get($url);
    }

    /**
     * 获取素材列表
     * @param $type
     * @param $offset
     * @param $count
     * @return array
     */
    public function getMaterialList($type, $offset, $count)
    {
        $baseModel = Base::getInstance();
        $url = $baseModel->getApiUrl('getMaterialList');
        return Http::post($url, ['type' => $type, 'offset' => $offset, 'count' => $count]);
    }
}
