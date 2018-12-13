<?php
/**
 * Created by PhpStorm.
 * User: dingo
 * Date: 2018/12/12
 * Time: 22:59
 */

namespace App\Http\Controllers\Api\Design;


use App\Http\Controllers\Controller;

class WeiXinChatRobotController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {

    }

    public function chat(){
        $this->responseMsg();
    }

    public function valid()
    {
        $echoStr = $_GET["echostr"];

        if ($echoStr) {
            if ($this->checkSignature()) {
                echo $echoStr;
                exit;
            }
        } else {
            $this->responseMsg();
        }
    }

    public function responseMsg()
    {
        /*
        获得请求时POST:XML字符串
        不能用$_POST获取，因为没有key
         */
//        $xml_str = $GLOBALS['HTTP_RAW_POST_DATA'];
        $xml_str = file_get_contents('php://input');
        if (empty($xml_str)) {
            die('');
        }
        if (!empty($xml_str)) {
            // 解析该xml字符串，利用simpleXML
            libxml_disable_entity_loader(true);
            //禁止xml实体解析，防止xml注入
            $request_xml = simplexml_load_string($xml_str, 'SimpleXMLElement', LIBXML_NOCDATA);
            //判断该消息的类型，通过元素MsgType
            switch ($request_xml->MsgType) {
                case 'event':
                    //判断具体的时间类型（关注、取消、点击）
                    $event = $request_xml->Event;
                    if ($event == 'subscribe') { // 关注事件
                        $this->_doSubscribe($request_xml);
                    } elseif ($event == 'CLICK') {//菜单点击事件
//                        $this->_doClick($request_xml);
                    } elseif ($event == 'VIEW') {//连接跳转事件
//                        $this->_doView($request_xml);
                    }
                    break;
                case 'text'://文本消息
                    $this->_doText($request_xml);
                    break;
                case 'image'://图片消息
//                    $this->_doImage($request_xml);
                    $this->_msgText($request_xml->FromUserName, $request_xml->ToUserName, '这是一张图片');
                    break;
                case 'voice'://语音消息
//                    $this->_doVoice($request_xml);
                    $this->_msgText($request_xml->FromUserName, $request_xml->ToUserName, '这是一段语音');
                    break;
                case 'video'://视频消息
//                    $this->_doVideo($request_xml);
                    $this->_msgText($request_xml->FromUserName, $request_xml->ToUserName, '这是一段视频');
                    break;
                case 'shortvideo'://短视频消息
//                    $this->_doShortvideo($request_xml);
                    $this->_msgText($request_xml->FromUserName, $request_xml->ToUserName, '这是一段短视频');
                    break;
                case 'location'://位置消息
//                    $this->_doLocation($request_xml);
                    $this->_msgText($request_xml->FromUserName, $request_xml->ToUserName, '这是一个位置信息');
                    break;
                case 'link'://链接消息
//                    $this->_doLink($request_xml);
                    $this->_msgText($request_xml->FromUserName, $request_xml->ToUserName, '这是一个链接');
                    break;
            }
        }
    }

    private $_msg_template = array(
        'text' => '<xml><ToUserName><![CDATA[%s]]></ToUserName><FromUserName><![CDATA[%s]]></FromUserName><CreateTime>%s</CreateTime><MsgType><![CDATA[text]]></MsgType><Content><![CDATA[%s]]></Content></xml>',//文本回复XML模板
        'image' => '<xml><ToUserName><![CDATA[%s]]></ToUserName><FromUserName><![CDATA[%s]]></FromUserName><CreateTime>%s</CreateTime><MsgType><![CDATA[image]]></MsgType><Image><MediaId><![CDATA[%s]]></MediaId></Image></xml>',//图片回复XML模板
        'music' => '<xml><ToUserName><![CDATA[%s]]></ToUserName><FromUserName><![CDATA[%s]]></FromUserName><CreateTime>%s</CreateTime><MsgType><![CDATA[music]]></MsgType><Music><Title><![CDATA[%s]]></Title><Description><![CDATA[%s]]></Description><MusicUrl><![CDATA[%s]]></MusicUrl><HQMusicUrl><![CDATA[%s]]></HQMusicUrl><ThumbMediaId><![CDATA[%s]]></ThumbMediaId></Music></xml>',//音乐模板
        'news' => '<xml><ToUserName><![CDATA[%s]]></ToUserName><FromUserName><![CDATA[%s]]></FromUserName><CreateTime>%s</CreateTime><MsgType><![CDATA[news]]></MsgType><ArticleCount>%s</ArticleCount><Articles>%s</Articles></xml>',// 新闻主体
        'news_item' => '<item><Title><![CDATA[%s]]></Title><Description><![CDATA[%s]]></Description><PicUrl><![CDATA[%s]]></PicUrl><Url><![CDATA[%s]]></Url></item>',//某个新闻模板
    );

    /**
     * 发送文本信息
     * @param  [type] $to      目标用户ID
     * @param  [type] $from    来源用户ID
     * @param  [type] $content 内容
     */
    private function _msgText($to, $from, $content)
    {
        $response = sprintf($this->_msg_template['text'], $to, $from, time(), $content);
        die($response);
    }

    //关注后做的事件
    private function _doSubscribe($request_xml)
    {
        //处理该关注事件，向用户发送关注信息
        $content = '你好, 我是帅丁丁';
        $this->_msgText($request_xml->FromUserName, $request_xml->ToUserName, $content);
    }

    private function _doText($request_xml)
    {
        $content = $request_xml->Content;
        $url = 'http://www.tuling123.com/openapi/api?key=26c1f5d7adf5e4e08b3bd9dec59dce27&info=' . $content;
        $response_content = json_decode($this->_request('get', $url, array(), false));
        $this->_msgText($request_xml->FromUserName, $request_xml->ToUserName, $response_content->text);
    }

    private function checkSignature()
    {
        $signature = $_GET["signature"];
        $timestamp = $_GET["timestamp"];
        $nonce = $_GET["nonce"];
        $token = "kecswhut";
        $tmpArr = array($token, $timestamp, $nonce);
        sort($tmpArr);
        $tmpStr = implode($tmpArr);
//        $tmpStr = sha1($tmpStr);

        if ($tmpStr == $signature) {
            return true;
        } else {
            return false;
        }
    }

    //发送请求方法
    /**
     * @param  string $method 'get'|'post' 请求的方式
     * @param  string $url URL
     * @param  array|json $data post请求需要发送的数据
     * @param  bool $ssl
     */
    public function _request($method='get',$url,$data=array(),$ssl=true){
        //curl完成，先开启curl模块
        //初始化一个curl资源
        $curl = curl_init();
        //设置curl选项
        curl_setopt($curl,CURLOPT_URL,$url);//url
        //请求的代理信息
        $user_agent = isset($_SERVER['HTTP_USER_AGENT'])?$_SERVER['HTTP_USER_AGENT']: 'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:38.0) Gecko/20100101 Firefox/38.0 FirePHP/0.7.4';
        curl_setopt($curl,CURLOPT_USERAGENT,$user_agent);
        //referer头，请求来源
        curl_setopt($curl,CURLOPT_AUTOREFERER,true);
        curl_setopt($curl, CURLOPT_TIMEOUT, 30);//设置超时时间
        //SSL相关
        if($ssl){
            //禁用后，curl将终止从服务端进行验证;
            curl_setopt($curl,CURLOPT_SSL_VERIFYPEER,false);
            //检查服务器SSL证书是否存在一个公用名
            curl_setopt($curl,CURLOPT_SSL_VERIFYHOST,2);
        }
        //判断请求方式post还是get
        if(strtolower($method)=='post') {
            /**************处理post相关选项******************/
            //是否为post请求 ,处理请求数据
            curl_setopt($curl,CURLOPT_POST,true);
            curl_setopt($curl,CURLOPT_POSTFIELDS,$data);
        }
        //是否处理响应头
        curl_setopt($curl,CURLOPT_HEADER,false);
        //是否返回响应结果
        curl_setopt($curl,CURLOPT_RETURNTRANSFER,true);

        //发出请求
        $response = curl_exec($curl);
        if (false === $response) {
            echo '<br>', curl_error($curl), '<br>';
            return false;
        }
        //关闭curl
        curl_close($curl);
        return $response;
    }
}