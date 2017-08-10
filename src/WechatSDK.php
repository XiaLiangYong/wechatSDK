<?php
namespace wechatsdk;

use wechatsdk\model\Base;
use wechatsdk\model\BasicSupport;
use wechatsdk\model\MaterialManage;
use wechatsdk\model\Menu;
use wechatsdk\model\Message;

/**
 * sdk接口
 * 基于psr4规范开发 直接composer安装调用即可
 * 调用说明
 * Example:
 * $config = [];
 * $config['appid'] = '**********';
 * $config['appsecret'] = '**********';
 * $config['token'] = ''**********';';
 * try {
 * $sdk = new wechatsdk\WechatSDK($config);
 * $sdk->valid();
 * } catch (Exception $e) {
 * echo $e->getMessage();
 * }
 * ===============================基础支持=============================================
 * @method getAccessToken() 获取access_token
 *
 * @method getCallbackip() 获取微信服务器IP地址
 * ===============================基础支持=============================================
 *
 * ===============================消息处理=============================================
 * @method valid()  授权接入微信验证消息
 *
 * @method getRevType() 接收消息类型
 *
 * @method getReceive() 获取微信推送的数据的数据（array）
 *
 * @method reply(array $msg, bool $return) 回复微信服务器, 此函数支持链式操作
 *          $msg 要发送的信息, 默认取$this->_msg 是否返回信息而不抛出到浏览器 默认:否
 *
 * @method text(string $text) 设置回复文本信息
 *          $text 回复微信的描述信息
 *          Example: $sdk->text($text)->reply(); 如果还需要处理自己的业务路基，可把$return =false
 *
 * @method image(string $mediaid) 设置图片消息
 *          $mediaid 通过素材管理接口上传多媒体文件，得到的id。必须
 *          Example:$sdk->image($mediaid)->reply();
 *
 * @method voice(string $mediaid) 设置语音消息
 *          $mediaid 通过素材管理接口上传多媒体文件，得到的id。
 *          Example:$sdk->voice($mediaid)->reply();
 *
 * @method video(string $mediaid, string $title, string $description) 设置图片消息
 *          $mediaid:通过素材管理接口上传多媒体文件，得到的id 必须
 *          $title:视频消息的标题 视频消息的标题 非必须
 *          $description:视频消息的描述 非必须
 *          Example:$sdk->video($mediaid,$title,$description)->reply();
 *
 * @method music(string $title, string $desc, string $musicurl, string $hgmusicurl, string $thumbmediaid) 设置图片消息
 *          $title:音乐标题 非必须
 *          $desc:音乐描述 非必须
 *          $musicurl:音乐链接 非必须
 *          $hgmusicurl:高质量音乐链接，WIFI环境优先使用该链接播放音乐 非必须
 *          $thumbmediaid:缩略图的媒体id，通过素材管理接口上传多媒体文件，得到的id 非必须
 *          Example:$sdk->music($title,$desc,$musicurl,$hgmusicurl,$thumbmediaid)->reply();
 *
 * @method news(array $newsData) 设置回复图文消息
 *          $newsData: 图文消息体
 *          格式如下：
 *          数组结构:
 *           array(
 *            "0"=>array(
 *            'Title'=>'titleMSg',//图文消息标题
 *            'Description'=>'hello',//图文消息描述
 *            'PicUrl'=>'http://www.test.com/1.jpg', //图片链接，支持JPG、PNG格式，较好的效果为大图360*200，小图200*200
 *            'Url'=>'http://www.test.com/1.html', //点击图文消息跳转链接
 *            ),
 *          )
 *          Example: $sdk->text($text)->reply(); 如果还需要处理自己的业务路基，可把$return =false
 * ===============================消息处理=============================================
 *
 * ============================素材管理================================================
 * @method uploadMedia(string $type, string $data) 新增临时素材
 *          $type 媒体文件类型，分别有图片（image）、语音（voice）、视频（video）和缩略图（thumb）
 *          $data 文件名 当是form-data file上传时候 键名改成media选择文件上传即可，此时，可不传文件名
 *          当上传固定地址时候，才传入文件名
 *
 * @method  getMedia(string $meda_id) 下载临时素材
 *           Example：$img=$sdk->getMedia($meda_id);
 *           如果要看到图片：
 *           header('Content-type: image/jpeg');
 *           echo $img;
 *
 * @method getMediaByJssdk(string $media_id)高清语音素材获取接口
 *           $media_id 媒体文件ID，即uploadVoice接口返回的serverID
 *
 * @method addNewsMaterial(array $data)  新增永久图文素材
 *          Example:
 *                  $data = [
 *                       'articles' => [
 *                       '0' => [
 *                       'title' => 'TITLE', //标题
 *                       'thumb_media_id' => 'THUMB_MEDIA_ID',//图文消息的封面图片素材id（必须是永久mediaID）
 *                       'author' => 'AUTHOR',//作者
 *                       'digest' => 'DIGEST',//图文消息的摘要，仅有单图文消息才有摘要，多图文此处为空
 *                       'show_cover_pic' => 'SHOW_COVER_PIC(0 / 1)',//是否显示封面，0为false，即不显示，1为true，即显示
 *                       'content' => 'CONTENT',图文消息的具体内容，支持HTML标签，必须少于2万字符，小于1M，且此处会去除JS,涉及图片url必须来源"上传图文消息内的图片获取URL"接口获取。外部图片url将被过滤。
 *                       'content_source_url' => 'CONTENT_SOURCE_URL' //图文消息的原文地址，即点击“阅读原文”后的URL
 *                        ],
 *                     ]
 *                 ];
 *
 * @method addMaterial(string $type, $data)  新增其他类型永久素材
 *          $type 媒体文件类型，分别有图片（image）、语音（voice）、视频（video）和缩略图（thumb）
 *          $data 文件名 当是form-data file上传时候 键名改成media选择文件上传即可，此时，可不传文件名
 *          当上传固定地址时候，才传入文件名
 *
 * @method mediaUploadimg($data) 上传图文消息内的图片获取URL
 *          $data 文件名 当是form-data file上传时候 键名改成media选择文件上传即可，此时，可不传文件名
 *                当上传固定地址时候，才传入文件名
 *
 * @method getMaterial(string $media_id) 获取永久素材
 *          $media_id  要获取的素材的media_id
 *
 * @method delMaterial(string $media_id) 删除永久素材
 *          $media_id 要获取的素材的media_id
 *
 * @method updateNews(array $data)修改永久图文素材
 *              $data = [
 *                  'media_id' => 'MEDIA_ID',//要修改的图文消息的id
 *                  'index' => 'INDEX',//要更新的文章在图文消息中的位置（多图文消息时，此字段才有意义），第一篇为0
 *                  'articles' => [
 *                         'title' => 'TITLE', //标题
 *                         'thumb_media_id' => 'THUMB_MEDIA_ID',//图文消息的封面图片素材id（必须是永久mediaID）
 *                         'author' => 'AUTHOR',//作者
 *                         'digest' => 'DIGEST',//图文消息的摘要，仅有单图文消息才有摘要，多图文此处为空
 *                         'show_cover_pic' => 'SHOW_COVER_PIC(0 / 1)',//是否显示封面，0为false，即不显示，1为true，即显示
 *                         'content' => 'CONTENT',//图文消息的具体内容，支持HTML标签，必须少于2万字符，小于1M，且此处会去除JS
 *                         'content_source_url' => 'CONTENT_SOURCE_URL'//图文消息的原文地址，即点击“阅读原文”后的URL
 *                      ]
 *                   ];
 *
 * @method getMaterialCount() 获取素材总数
 *
 * @method getMaterialList(string $type, string $offset, string $count) 获取素材列表
 *          $type 素材的类型，图片（image）、视频（video）、语音 （voice）、图文（news）
 *          $offset 从全部素材的该偏移位置开始返回，0表示从第一个素材 返回
 *          $count 返回素材的数量，取值在1到20之间
 *
 * ============================素材管理================================================
 *
 * ============================自定义菜单==============================================
 * @method createMenu(string $data) 自定义菜单创建接口
 * @method getMenu() 自定义菜单查询接口
 * @method deleteMenu() 自定义菜单删除接口
 * ============================自定义菜单==============================================
 * User: xialiangyong
 * Date: 2017/5/13
 * Time: 11:27
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
        //获取base实例
        $baseModel = Base::getInstance();
        //1.注入wechat
        $baseModel->setConfig($config);
        //2.注入wechat
        $baseModel->setWechat($this);
    }

    //基础支持模型
    const BASE_MODEL = BasicSupport::class;
    //消息接口模型
    const MESSAGE_MODEL = Message::class;
    //素材管理
    const MATERIAL_MODEL = MaterialManage::class;
    //菜单管理
    const MENU_MODEL = Menu::class;

    //接口映射
    public static $interface = [
        //-------------基础支持---------------------
        'getAccessToken' => self::BASE_MODEL,
        'getCallbackip' => self::BASE_MODEL,

        //--------------消息接口--------------------
        //授权接入微信验证消息
        'valid' => self::MESSAGE_MODEL,
        //接到到的消息类型
        'getRevType' => self::MESSAGE_MODEL,
        //获取传输的数据
        'getReceive' => self::MESSAGE_MODEL,
        //回复微信
        'reply' => self::MESSAGE_MODEL,
        //设置文本消息
        'text' => self::MESSAGE_MODEL,
        //设置图片消息
        'image' => self::MESSAGE_MODEL,
        //设置语音消息
        'voice' => self::MESSAGE_MODEL,
        //设置视频消息
        'video' => self::MESSAGE_MODEL,
        //设置音乐消息
        'music' => self::MESSAGE_MODEL,
        //设置图文消息
        'news' => self::MESSAGE_MODEL,

        //--------------素材管理--------------------
        //新增临时素材
        'uploadMedia' => self::MATERIAL_MODEL,
        //获取临时素材
        'getMedia' => self::MATERIAL_MODEL,
        //高清语音素材获取接口
        'getMediaByJssdk' => self::MATERIAL_MODEL,
        //新增永久素材
        'addNewsMaterial' => self::MATERIAL_MODEL,
        //获取永久素材
        'getMaterial' => self::MATERIAL_MODEL,
        //删除永久素材
        'delMaterial' => self::MATERIAL_MODEL,
        //修改永久图文素材
        'updateNews' => self::MATERIAL_MODEL,
        //获取素材总数
        'getMaterialCount' => self::MATERIAL_MODEL,
        //获取素材列表
        'getMaterialList' => self::MATERIAL_MODEL,
        //新增其他类型永久素材
        'addMaterial' => self::MATERIAL_MODEL,
        //上传图文消息内的图片获取URL
        'mediaUploadimg' => self::MATERIAL_MODEL,

        //--------------菜单管理--------------------
        //自定义菜单创建接口
        'createMenu' => self::MENU_MODEL,
        //自定义菜单查询接口
        'getMenu' => self::MENU_MODEL,
        //自定义菜单删除接口
        'deleteMenu' => self::MENU_MODEL,
    ];


    /**
     * 设置缓存 按需重载
     * @param $name
     * @param $value
     * @param $expire_time
     */
    public function setCache($name, $value, $expire_time = '')
    {
        $file_path = $name . '.txt';
        $myfile = fopen($file_path, "w") or die("Unable to open file!");
        fwrite($myfile, $value);
        fclose($myfile);
    }


    /**
     * 获取缓存 按需重载
     * @param $name
     */
    public function getCache($name)
    {
        $file_path = $name . '.txt';
        if (!file_exists($file_path)) {
            return null;
        }
        $myfile = fopen($file_path, "r") or die("Unable to open file!");
        $data = fread($myfile, filesize($file_path));
        fclose($myfile);
        return $data;
    }

    /**
     * 删除缓存 按需重载
     */
    public function removeCache($name)
    {
        $this->setCache($name, '');
    }

    /**
     * 日志打印方法 按需重载
     * @param string $data
     */
    public function log($data = '')
    {
        file_put_contents('wechat.log', date("Y-m-d H:i:s") . " " . $data . PHP_EOL, FILE_APPEND | LOCK_EX);
    }

    /**
     * 接口调用
     * @param $reqName
     * @param array $args
     */
    public function __call($method, $args = [])
    {
        try {
            $map = self::$interface;
            $class = isset($map[$method]) ? $map[$method] : '';
            if (is_callable([$class, $method])) {
                $res = call_user_func_array([$class::getInstance(), $method], $args);
            } else {
                throw new \Exception($method . '接口不存在');
            }
        } catch (\Exception $e) {
            $res = [];
            $res['errcode'] = -1;
            $res['errmsg'] = $e->getMessage();
        }
        return $res;
    }
}