<?php
/**
 * 接收消息
 * Created by PhpStorm.
 * User: xialiangyong
 * Date: 2017/5/15
 * Time: 16:40
 */

namespace wechatsdk\model;


use wechatsdk\encode\ErrorCode;
use wechatsdk\encode\Prpcrypt;
use wechatsdk\encode\WXBizMsgCrypt;
use wechatsdk\tools\Helper;

class Message extends Singleton
{

    //操作数据
    private $_receive;
    //接收到微信发送的xml数据包
    private $postxml;
    //加密类型
    private $encrypt_type;

    private $appid;

    private $_msg;
    const MSGTYPE_TEXT = 'text';
    const MSGTYPE_IMAGE = 'image';
    const MSGTYPE_LOCATION = 'location';
    const MSGTYPE_LINK = 'link';
    const MSGTYPE_EVENT = 'event';
    const MSGTYPE_MUSIC = 'music';
    const MSGTYPE_NEWS = 'news';
    const MSGTYPE_VOICE = 'voice';
    const MSGTYPE_VIDEO = 'video';

    public function __construct()
    {
        if ($_SERVER['REQUEST_METHOD'] == "POST") {
            $baseModel = Base::getInstance();
            $postStr = file_get_contents("php://input");
            $this->encrypt_type = isset($_GET['encrypt_type']) ? $_GET['encrypt_type'] : '';
            if ($this->encrypt_type == 'aes') { //aes加密
                $baseModel->log('接收消息模式为=aes');
                $encodingAesKey = $baseModel->getEncodingAesKey();
                $this->appid = $baseModel->getAppid();
                $timeStamp = isset($_GET['timestamp']) ? $_GET['timestamp'] : '';
                $nonce = isset($_GET['nonce']) ? $_GET['nonce'] : '';
                $msg_sign = isset($_GET['msg_signature']) ? $_GET['msg_signature'] : '';
                $pc = new WXBizMsgCrypt($baseModel->getToken(), $encodingAesKey, $this->appid);
                $errCode = $pc->decryptMsg($msg_sign, $timeStamp, $nonce, $postStr, $msg);
                if ($errCode != ErrorCode::$OK) {
                    throw new \Exception(\wechatsdk\tools\ErrorCode::transformErrorCode($errCode));
                }
                $this->postxml = $msg;
            } else {
                $this->postxml = $postStr;
            }
            if ($this->postxml) {
                $this->setReceive(Helper::extractXml($this->postxml));
            }
            //打印xm数据
            $baseModel->log('inputXml=' . $this->postxml);
            //转成数组格式为
            $baseModel->log('inputArrayData=' . json_encode($this->getReceive(), JSON_UNESCAPED_UNICODE));
            return true;
        }
    }

    /**
     * 授权接入微信验证消息
     * For weixin server validation
     */
    public function valid()
    {
        if ($_SERVER['REQUEST_METHOD'] == "POST") {
            return true;
        } else {
            //授权接入微信
            $echoStr = $_GET["echostr"];
            if ($this->_checkSignature()) {
                exit($echoStr);
            } else {
                exit('no access');
            }
        }
    }

    /**
     *
     * 校验接入微信服务器
     * @return bool|string
     */
    public function _checkSignature($str = '')
    {
        $signature = $_GET["signature"];
        $timestamp = $_GET["timestamp"];
        $nonce = $_GET["nonce"];
        $token = Base::getInstance()->getToken();
        $tmpArr = array($token, $timestamp, $nonce, $str);
        sort($tmpArr, SORT_STRING);
        $tmpStr = implode($tmpArr);
        $tmpStr = sha1($tmpStr);
        Helper::log('test=' . $tmpStr);
        if ($tmpStr == $signature) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * 设置回复文本消息
     * Example: $sdk->text('hello')->reply();
     * @param string $text
     */
    public function text($text = '')
    {
        $msg = array(
            'ToUserName' => $this->getRevFrom(),
            'FromUserName' => $this->getRevTo(),
            'MsgType' => self::MSGTYPE_TEXT,
            'Content' => $this->_auto_text_filter($text),
            'CreateTime' => time(),
        );
        $this->Message($msg);
        return $this;
    }

    /**
     * 设置回复图片消息
     * Example: $sdk->image('media_id')->reply();
     * @param string $mediaid
     */
    public function image($mediaid = '')
    {
        $msg = array(
            'ToUserName' => $this->getRevFrom(),
            'FromUserName' => $this->getRevTo(),
            'MsgType' => self::MSGTYPE_IMAGE,
            'Image' => array('MediaId' => $mediaid),
            'CreateTime' => time(),
        );
        $this->Message($msg);
        return $this;
    }

    /**
     * 设置回复语音消息
     * Example: $sdk->voice('media_id')->reply();
     * @param string $mediaid
     */
    public function voice($mediaid = '')
    {
        $msg = array(
            'ToUserName' => $this->getRevFrom(),
            'FromUserName' => $this->getRevTo(),
            'MsgType' => self::MSGTYPE_VOICE,
            'Voice' => array('MediaId' => $mediaid),
            'CreateTime' => time(),
        );
        $this->Message($msg);
        return $this;
    }

    /**
     * 设置回复视频消息
     * Example: $sdk->video('media_id','title','description')->reply();
     * @param string $mediaid
     */
    public function video($mediaid = '', $title = '', $description = '')
    {
        $msg = array(
            'ToUserName' => $this->getRevFrom(),
            'FromUserName' => $this->getRevTo(),
            'MsgType' => self::MSGTYPE_VIDEO,
            'Video' => array(
                'MediaId' => $mediaid,
                'Title' => $title,
                'Description' => $description
            ),
            'CreateTime' => time(),
        );
        $this->Message($msg);
        return $this;
    }

    /**
     * 设置回复音乐
     * @param string $title
     * @param string $desc
     * @param string $musicurl
     * @param string $hgmusicurl
     * @param string $thumbmediaid 音乐图片缩略图的媒体id，非必须
     */
    public function music($title = '', $desc = '', $musicurl = '', $hgmusicurl = '', $thumbmediaid = '')
    {
        $msg = array(
            'ToUserName' => $this->getRevFrom(),
            'FromUserName' => $this->getRevTo(),
            'CreateTime' => time(),
            'MsgType' => self::MSGTYPE_MUSIC,
            'Music' => array(
                'Title' => $title,
                'Description' => $desc,
                'MusicUrl' => $musicurl,
                'HQMusicUrl' => $hgmusicurl
            ),
        );
        if ($thumbmediaid) {
            $msg['Music']['ThumbMediaId'] = $thumbmediaid;
        }
        $this->Message($msg);
        return $this;
    }

    /**
     * 设置回复图文
     * @param array $newsData
     * 数组结构:
     *  array(
     *    "0"=>array(
     *        'Title'=>'msg title',
     *        'Description'=>'summary text',
     *        'PicUrl'=>'http://www.domain.com/1.jpg',
     *        'Url'=>'http://www.domain.com/1.html'
     *    ),
     *    "1"=>....
     *  )
     */
    public function news($newsData = array())
    {
        $count = count($newsData);
        $msg = array(
            'ToUserName' => $this->getRevFrom(),
            'FromUserName' => $this->getRevTo(),
            'MsgType' => self::MSGTYPE_NEWS,
            'CreateTime' => time(),
            'ArticleCount' => $count,
            'Articles' => $newsData,
        );
        $this->Message($msg);
        return $this;
    }

    /**
     * 设置发送消息
     * @param array $msg 消息数组
     * @param bool $append 是否在原消息数组追加
     */
    public function Message($msg = '', $append = false)
    {
        if (is_null($msg)) {
            $this->_msg = array();
        } elseif (is_array($msg)) {
            if ($append) {
                $this->_msg = array_merge($this->_msg, $msg);
            } else {
                $this->_msg = $msg;
            }
            return $this->_msg;
        } else {
            return $this->_msg;
        }
    }

    /**
     *
     * 回复微信服务器, 此函数支持链式操作
     * Example: $this->text('msg tips')->reply();
     * @param string $msg 要发送的信息, 默认取$this->_msg
     * @param bool $return 是否返回信息而不抛出到浏览器 默认:否
     */
    public function reply($msg = array(), $return = false)
    {
        if (empty($msg)) {
            if (empty($this->_msg)) {   //防止不先设置回复内容，直接调用reply方法导致异常
                return false;
            }
            $msg = $this->_msg;
        }
        $xmldata = $this->xml_encode($msg);
        if ($this->encrypt_type == 'aes') { //如果来源消息为加密方式
            $encodingAesKey = Base::getInstance()->getEncodingAesKey();
            $appid = Base::getInstance()->getAppid();
            $token = Base::getInstance()->getToken();
            $pc = new WXBizMsgCrypt($token, $encodingAesKey, $appid);
            $timestamp = time();
            $nonce = rand(77, 999) * rand(605, 888) * rand(11, 99);
            $errCode = $pc->encryptMsg($xmldata, $timestamp, $nonce, $encryptMsg);
            if ($errCode != ErrorCode::$OK) {
                throw new \Exception(\wechatsdk\tools\ErrorCode::transformErrorCode($errCode));
            }
            $xmldata = $encryptMsg;
        }
        Base::getInstance()->log('replyWeixinXml=' . $xmldata);
        if ($return) {
            return $xmldata;
        } else {
            die($xmldata);
        }
    }

    public static function xmlSafeStr($str)
    {
        return '<![CDATA[' . preg_replace("/[\\x00-\\x08\\x0b-\\x0c\\x0e-\\x1f]/", '', $str) . ']]>';
    }

    /**
     * 数据XML编码
     * @param mixed $data 数据
     * @return string
     */
    public static function data_to_xml($data)
    {
        $xml = '';
        foreach ($data as $key => $val) {
            is_numeric($key) && $key = "item id=\"$key\"";
            $xml .= "<$key>";
            $xml .= (is_array($val) || is_object($val)) ? self::data_to_xml($val) : self::xmlSafeStr($val);
            list($key,) = explode(' ', $key);
            $xml .= "</$key>";
        }
        return $xml;
    }

    /**
     * XML编码
     * @param mixed $data 数据
     * @param string $root 根节点名
     * @param string $item 数字索引的子节点名
     * @param string $attr 根节点属性
     * @param string $id 数字索引子节点key转换的属性名
     * @param string $encoding 数据编码
     * @return string
     */
    public function xml_encode($data, $root = 'xml', $item = 'item', $attr = '', $id = 'id', $encoding = 'utf-8')
    {
        if (is_array($attr)) {
            $_attr = array();
            foreach ($attr as $key => $value) {
                $_attr[] = "{$key}=\"{$value}\"";
            }
            $attr = implode(' ', $_attr);
        }
        $attr = trim($attr);
        $attr = empty($attr) ? '' : " {$attr}";
        $xml = "<{$root}{$attr}>";
        $xml .= self::data_to_xml($data, $item, $id);
        $xml .= "</{$root}>";
        return $xml;
    }


    /**
     * @return mixed
     */
    public function getReceive()
    {
        return $this->_receive;
    }

    /**
     * @param mixed $receive
     */
    public function setReceive($receive)
    {
        $this->_receive = $receive;
    }

    /**
     * @return mixed
     */
    public function getPostxml()
    {
        return $this->postxml;
    }

    /**
     * @param mixed $postxml
     */
    public function setPostxml($postxml)
    {
        $this->postxml = $postxml;
    }


    /**
     * 获取消息发送者
     */
    public function getRevFrom()
    {
        return isset($this->_receive['FromUserName']) ? $this->_receive['FromUserName'] : false;
    }

    /**
     * 获取消息接受者
     */
    public function getRevTo()
    {
        return isset($this->_receive['ToUserName']) ? $this->_receive['ToUserName'] : false;
    }

    /**
     * 获取接收消息类型
     * @return bool
     */
    public function getRevType()
    {
        return isset($this->_receive['MsgType']) ? $this->_receive['MsgType'] : false;
    }

    /**
     * 获取消息ID
     */
    public function getRevID()
    {
        return isset($this->_receive['MsgId']) ? $this->_receive['MsgId'] : false;
    }

    /**
     * 获取消息发送时间
     */
    public function getRevCtime()
    {
        return isset($this->_receive['CreateTime']) ? $this->_receive['CreateTime'] : false;
    }

    /**
     * 获取接收消息内容正文
     */
    public function getRevContent()
    {
        if (isset($this->_receive['Content'])) {
            return $this->_receive['Content'];
        } else if (isset($this->_receive['Recognition'])) { //获取语音识别文字内容，需申请开通
            return $this->_receive['Recognition'];
        } else {
            return false;
        }
    }

    /**
     * 获取接收消息图片
     */
    public function getRevPic()
    {
        if (isset($this->_receive['PicUrl'])) {
            return [
                'mediaid' => $this->_receive['MediaId'],
                'picurl' => (string)$this->_receive['PicUrl'],    //防止picurl为空导致解析出错
            ];
        } else {
            return false;
        }
    }

    /**
     * 获取接收消息链接
     */
    public function getRevLink()
    {
        if (isset($this->_receive['Url'])) {
            return [
                'url' => $this->_receive['Url'],
                'title' => $this->_receive['Title'],
                'description' => $this->_receive['Description']
            ];
        } else {
            return false;
        }
    }

    /**
     * 获取接收地理位置
     */
    public function getRevGeo()
    {
        if (isset($this->_receive['Location_X'])) {
            return [
                'x' => $this->_receive['Location_X'],
                'y' => $this->_receive['Location_Y'],
                'scale' => $this->_receive['Scale'],
                'label' => $this->_receive['Label']
            ];
        } else {
            return false;
        }
    }

    /**
     * 获取上报地理位置事件
     */
    public function getRevEventGeo()
    {
        if (isset($this->_receive['Latitude'])) {
            return [
                'x' => $this->_receive['Latitude'],
                'y' => $this->_receive['Longitude'],
                'precision' => $this->_receive['Precision'],
            ];
        } else {
            return false;
        }
    }

    /**
     * 获取接收事件推送
     */
    public function getRevEvent()
    {
        if (isset($this->_receive['Event'])) {
            $array['event'] = $this->_receive['Event'];
        }
        if (isset($this->_receive['EventKey'])) {
            $array['key'] = $this->_receive['EventKey'];
        }
        if (isset($array) && count($array) > 0) {
            return $array;
        } else {
            return false;
        }
    }


    /**
     * 获取自定义菜单的扫码推事件信息
     *
     * 事件类型为以下两种时则调用此方法有效
     * Event     事件类型，scancode_push
     * Event     事件类型，scancode_waitmsg
     *
     * @return: array | false
     * array (
     *     'ScanType'=>'qrcode',
     *     'ScanResult'=>'123123'
     * )
     */
    public function getRevScanInfo()
    {
        if (isset($this->_receive['ScanCodeInfo'])) {
            if (!is_array($this->_receive['ScanCodeInfo'])) {
                $array = (array)$this->_receive['ScanCodeInfo'];
                $this->_receive['ScanCodeInfo'] = $array;
            } else {
                $array = $this->_receive['ScanCodeInfo'];
            }
        }
        if (isset($array) && count($array) > 0) {
            return $array;
        } else {
            return false;
        }
    }

    /**
     * 获取自定义菜单的图片发送事件信息
     *
     * 事件类型为以下三种时则调用此方法有效
     * Event     事件类型，pic_sysphoto        弹出系统拍照发图的事件推送
     * Event     事件类型，pic_photo_or_album  弹出拍照或者相册发图的事件推送
     * Event     事件类型，pic_weixin          弹出微信相册发图器的事件推送
     *
     * @return: array | false
     * array (
     *   'Count' => '2',
     *   'PicList' =>array (
     *         'item' =>array (
     *             0 =>array ('PicMd5Sum' => 'aaae42617cf2a14342d96005af53624c'),
     *             1 =>array ('PicMd5Sum' => '149bd39e296860a2adc2f1bb81616ff8'),
     *         ),
     *   ),
     * )
     *
     */
    public function getRevSendPicsInfo()
    {
        if (isset($this->_receive['SendPicsInfo'])) {
            if (!is_array($this->_receive['SendPicsInfo'])) {
                $array = (array)$this->_receive['SendPicsInfo'];
                if (isset($array['PicList'])) {
                    $array['PicList'] = (array)$array['PicList'];
                    $item = $array['PicList']['item'];
                    $array['PicList']['item'] = array();
                    foreach ($item as $key => $value) {
                        $array['PicList']['item'][$key] = (array)$value;
                    }
                }
                $this->_receive['SendPicsInfo'] = $array;
            } else {
                $array = $this->_receive['SendPicsInfo'];
            }
        }
        if (isset($array) && count($array) > 0) {
            return $array;
        } else {
            return false;
        }
    }

    /**
     * 获取自定义菜单的地理位置选择器事件推送
     *
     * 事件类型为以下时则可以调用此方法有效
     * Event     事件类型，location_select        弹出地理位置选择器的事件推送
     *
     * @return: array | false
     * array (
     *   'Location_X' => '33.731655000061',
     *   'Location_Y' => '113.29955200008047',
     *   'Scale' => '16',
     *   'Label' => '某某市某某区某某路',
     *   'Poiname' => '',
     * )
     *
     */
    public function getRevSendGeoInfo()
    {
        if (isset($this->_receive['SendLocationInfo'])) {
            if (!is_array($this->_receive['SendLocationInfo'])) {
                $array = (array)$this->_receive['SendLocationInfo'];
                if (empty($array['Poiname'])) {
                    $array['Poiname'] = "";
                }
                if (empty($array['Label'])) {
                    $array['Label'] = "";
                }
                $this->_receive['SendLocationInfo'] = $array;
            } else {
                $array = $this->_receive['SendLocationInfo'];
            }
        }
        if (isset($array) && count($array) > 0) {
            return $array;
        } else {
            return false;
        }
    }


    /**
     * 获取接收语音推送
     */
    public function getRevVoice()
    {
        if (isset($this->_receive['MediaId'])) {
            return [
                'mediaid' => $this->_receive['MediaId'],
                'format' => $this->_receive['Format'],
            ];
        } else {
            return false;
        }
    }

    /**
     * 获取接收视频推送
     */
    public function getRevVideo()
    {
        if (isset($this->_receive['MediaId'])) {
            return array(
                'mediaid' => $this->_receive['MediaId'],
                'thumbmediaid' => $this->_receive['ThumbMediaId']
            );
        } else {
            return false;
        }
    }


    /**
     * 获取接收TICKET
     */
    public function getRevTicket()
    {
        if (isset($this->_receive['Ticket'])) {
            return $this->_receive['Ticket'];
        } else {
            return false;
        }
    }

    /**
     * 获取二维码的场景值
     */
    public function getRevSceneId()
    {
        if (isset($this->_receive['EventKey'])) {
            return str_replace('qrscene_', '', $this->_receive['EventKey']);
        } else {
            return false;
        }
    }

    /**
     * 获取主动推送的消息ID
     * 经过验证，这个和普通的消息MsgId不一样
     * 当Event为 MASSSENDJOBFINISH 或 TEMPLATESENDJOBFINISH
     */
    public function getRevTplMsgID()
    {
        if (isset($this->_receive['MsgID'])) {
            return $this->_receive['MsgID'];
        } else {
            return false;
        }
    }

    /**
     * 获取模板消息发送状态
     */
    public function getRevStatus()
    {
        if (isset($this->_receive['Status'])) {
            return $this->_receive['Status'];
        } else {
            return false;
        }
    }


    /**
     * 获取群发或模板消息发送结果
     * 当Event为 MASSSENDJOBFINISH 或 TEMPLATESENDJOBFINISH，即高级群发/模板消息
     */
    public function getRevResult()
    {
        if (isset($this->_receive['Status'])) //发送是否成功，具体的返回值请参考 高级群发/模板消息 的事件推送说明
            $array['Status'] = $this->_receive['Status'];
        if (isset($this->_receive['MsgID'])) //发送的消息id
            $array['MsgID'] = $this->_receive['MsgID'];
        //以下仅当群发消息时才会有的事件内容
        if (isset($this->_receive['TotalCount']))     //分组或openid列表内粉丝数量
            $array['TotalCount'] = $this->_receive['TotalCount'];
        if (isset($this->_receive['FilterCount']))    //过滤（过滤是指特定地区、性别的过滤、用户设置拒收的过滤，用户接收已超4条的过滤）后，准备发送的粉丝数
            $array['FilterCount'] = $this->_receive['FilterCount'];
        if (isset($this->_receive['SentCount']))     //发送成功的粉丝数
            $array['SentCount'] = $this->_receive['SentCount'];
        if (isset($this->_receive['ErrorCount']))    //发送失败的粉丝数
            $array['ErrorCount'] = $this->_receive['ErrorCount'];
        if (isset($array) && count($array) > 0) {
            return $array;
        } else {
            return false;
        }
    }

    /**
     * 获取多客服会话状态推送事件 - 接入会话
     * 当Event为 kfcreatesession 即接入会话
     * @return string | boolean  返回分配到的客服
     */
    public function getRevKFCreate()
    {
        if (isset($this->_receive['KfAccount'])) {
            return $this->_receive['KfAccount'];
        } else {
            return false;
        }
    }


    /**
     * 获取多客服会话状态推送事件 - 关闭会话
     * 当Event为 kfclosesession 即关闭会话
     * @return string | boolean  返回分配到的客服
     */
    public function getRevKFClose()
    {
        if (isset($this->_receive['KfAccount'])) {
            return $this->_receive['KfAccount'];
        } else {
            return false;
        }
    }

    /**
     * 获取多客服会话状态推送事件 - 转接会话
     * 当Event为 kfswitchsession 即转接会话
     * @return array | boolean  返回分配到的客服
     * {
     *     'FromKfAccount' => '',      //原接入客服
     *     'ToKfAccount' => ''            //转接到客服
     * }
     */
    public function getRevKFSwitch()
    {
        if (isset($this->_receive['FromKfAccount']))     //原接入客服
            $array['FromKfAccount'] = $this->_receive['FromKfAccount'];
        if (isset($this->_receive['ToKfAccount']))    //转接到客服
            $array['ToKfAccount'] = $this->_receive['ToKfAccount'];
        if (isset($array) && count($array) > 0) {
            return $array;
        } else {
            return false;
        }
    }

    /**
     * 获取卡券事件推送 - 卡卷审核是否通过
     * 当Event为 card_pass_check(审核通过) 或 card_not_pass_check(未通过)
     * @return string|boolean  返回卡券ID
     */
    public function getRevCardPass()
    {
        if (isset($this->_receive['CardId']))
            return $this->_receive['CardId'];
        else {
            return false;
        }
    }

    /**
     * 获取卡券事件推送 - 领取卡券
     * 当Event为 user_get_card(用户领取卡券)
     * @return array|boolean
     */
    public function getRevCardGet()
    {
        if (isset($this->_receive['CardId']))     //卡券 ID
            $array['CardId'] = $this->_receive['CardId'];
        if (isset($this->_receive['IsGiveByFriend']))    //是否为转赠，1 代表是，0 代表否。
            $array['IsGiveByFriend'] = $this->_receive['IsGiveByFriend'];
        if (isset($this->_receive['UserCardCode']) && !empty($this->_receive['UserCardCode'])) //code 序列号。自定义 code 及非自定义 code的卡券被领取后都支持事件推送。
            $array['UserCardCode'] = $this->_receive['UserCardCode'];
        if (isset($array) && count($array) > 0) {
            return $array;
        } else {
            return false;
        }
    }

    /**
     * 获取卡券事件推送 - 删除卡券
     * 当Event为 user_del_card(用户删除卡券)
     * @return array|boolean
     */
    public function getRevCardDel()
    {
        if (isset($this->_receive['CardId']))     //卡券 ID
            $array['CardId'] = $this->_receive['CardId'];
        if (isset($this->_receive['UserCardCode']) && !empty($this->_receive['UserCardCode'])) //code 序列号。自定义 code 及非自定义 code的卡券被领取后都支持事件推送。
            $array['UserCardCode'] = $this->_receive['UserCardCode'];
        if (isset($array) && count($array) > 0) {
            return $array;
        } else {
            return false;
        }
    }


    /**
     * 过滤文字回复\r\n换行符
     * @param string $text
     * @return string|mixed
     */
    private function _auto_text_filter($text)
    {
        if (!Base::getInstance()->isTextFilter()) return $text;
        return str_replace("\r\n", "\n", $text);
    }

}
