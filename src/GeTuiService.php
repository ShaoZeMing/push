<?php

namespace Shaozeming\Push;


//use Illuminate\Support\Facades\Log;


require_once dirname(__FILE__) . '/Drivers/getui/IGt.Push.php';

class GeTuiService extends PushBase
{
    // use AuthorizesRequests, ValidatesRequests;

    const HOST = 'http://sdk.open.api.igexin.com/apiex.htm';  //http的域名
    const ALL = 0;
    const NOTICE = 1;  //http的域名
    const PENETRATE = 2;  //http的域名
    const H5 = 3;  //http的域名
    public $obj;

    public $push_type = self::ALL;
    public static $push_type_txt = [
        self::NOTICE => '通知',
        self::PENETRATE => '透传',
        self::ALL => '通知+透传',
        self::H5 => 'H5',
    ];


    public function __construct($config = [])
    {
        if (count($config)) {
            $params = $config;
        } else {
            $driver = config('push.driver');
            $tag = config('push.tag');
            $params = config('push.' . $driver . '.getui.' . $tag);
        }
        $this->obj = new \IGeTui($params['gt_domainurl'], $params['gt_appkey'], $params['gt_mastersecret']);
        $this->gt_appid = $params['gt_appid'];
        $this->gt_appkey = $params['gt_appkey'];
        $this->gt_appsecret = $params['gt_appsecret'];
        $this->gt_mastersecret = $params['gt_mastersecret'];
    }

    public function getMerInstance($config = [])
    {
        if (count($config)) {
            $params = $config;
        } else {
            $driver = config('push.driver');
            $tag = config('push.tag');
            $params = config('push.' . $driver . '.getui.' . $tag);
        }
        $this->obj = new \IGeTui($params['gt_domainurl'], $params['gt_appkey'], $params['gt_mastersecret'], $ssl = NULL);
        $this->gt_appid = $params['gt_appid'];
        $this->gt_appkey = $params['gt_appkey'];
        $this->gt_appsecret = $params['gt_appsecret'];
        $this->gt_mastersecret = $params['gt_mastersecret'];
        return $this;
    }

    public function pushToSignal($clientId, $transContent, $content, $title, $shortUrl = '', $deviceOs = 'ios', $logoUrl = '')
    {
        //消息模版：
        // 4.NotyPopLoadTemplate：通知弹框下载功能模板
        $template = $this->getTransmissionTemplateDemo($transContent, $content, $title);
        //定义"SingleMessage"
        $message = new \IGtSingleMessage();

        $message->set_isOffline(true);//是否离线
        $message->set_offlineExpireTime(3600 * 12 * 1000);//离线时间
        $message->set_data($template);//设置推送消息类型
        //$message->set_PushNetWorkType(0);//设置是否根据WIFI推送消息，2为4G/3G/2G，1为wifi推送，0为不限制推送
        //接收方
        $target = new \IGtTarget();
        $target->set_appId($this->gt_appid);
        $target->set_clientId($clientId);
        //    $target->set_alias(Alias);
        // var_export($this->obj);exit;
        try {
            $rep = $this->obj->pushMessageToSingle($message, $target);
        } catch (\RequestException $e) {
            $requestId = $e->getRequestId();
            //失败时重发
            $rep = $this->obj->pushMessageToSingle($message, $target, $requestId);
        }
        return $rep;
    }


    public function pushMessageToList($clientIds, $transContent, $content, $title, $shortUrl = '')
    {
        $template = $this->getTransmissionTemplateDemo($transContent, $content, $title);
        //定义"ListMessage"信息体
        $message = new \IGtListMessage();
        $message->set_isOffline(true);//是否离线
        $message->set_offlineExpireTime(3600 * 12 * 1000);//离线时间
        $message->set_data($template);//设置推送消息类型
        $message->set_PushNetWorkType(0);//设置是否根据WIFI推送消息，1为wifi推送，0为不限制推送
        $contentId = $this->obj->getContentId($message);
        $targetList = [];
        foreach ($clientIds as $key => $clientId) {
            $target = new \IGtTarget();
            $target->set_appId($this->gt_appid);
            $target->set_clientId($clientId);
            $targetList[] = $target;
//            Log::info('c=getuiService f=pushMessageToList clientId=' . $clientId);
        }
        try {
            $rep = $this->obj->pushMessageToList($contentId, $targetList);
        } catch (\RequestException $e) {
            $requestId = $e->getRequestId();
            $rep = $this->obj->pushMessageToList($contentId, $targetList);
        }
        return $rep;
    }

    public function pushMsgToApp($transContent, $content, $title)
    {
        $template = $this->getTransmissionTemplateDemo($transContent, $content, $title);
        // $template = $this->getNotificationTemplateDemo($transContent, $content, $title);
        //个推信息体
        //基于应用消息体
        $message = new \IGtAppMessage();
        $message->set_isOffline(true);
        $message->set_offlineExpireTime(10 * 60 * 1000);//离线时间单位为毫秒，例，两个小时离线为3600*1000*2
        $message->set_data($template);
        $appIdList = array($this->gt_appid);
        $phoneTypeList = array('ANDROID', 'IOS');

        // $cdt = new \AppConditions();
        // $cdt->addCondition(\AppConditions::PHONE_TYPE, $phoneTypeList);
        $message->set_appIdList($appIdList);
        // $message->set_conditions($cdt);
        $message->set_PushNetWorkType(0);//设置是否根据WIFI推送消息，1为wifi推送，0为不限制推送
        // $message->set_speed(100);
        $rep = $this->obj->pushMessageToApp($message);
//        Log::info('c=getuiService f=pushMsgToApp rep=' . json_encode($rep));
        return $rep;
    }

    public function getNotificationTemplateDemo($transContent, $content, $title, $logoUrl = '')
    {
        $template = new \IGtNotificationTemplate();
        $template->set_appId($this->gt_appid);              //应用appid
        $template->set_appkey($this->gt_appkey);            //应用appkey
        $template->set_transmissionType(1);               //透传消息类型
        $template->set_transmissionContent($transContent);   //透传内容
        $template->set_title($title);                     //通知栏标题
        $template->set_text($content);        //通知栏内容
        // $template->set_logo("logo.png");                  //通知栏logo
        $template->set_logoURL("http://wwww.igetui.com/logo.png"); //通知栏logo链接
        $template->set_isRing(true);                      //是否响铃
        $template->set_isVibrate(true);                   //是否震动
        $template->set_isClearable(true);                 //通知栏是否可清除
        //$template->set_duration(BEGINTIME,ENDTIME); //设置ANDROID客户端在此时间区间内展示消息
        return $template;
    }

    public function getTransmissionTemplateDemo($transContent, $content, $title, $logoUrl = '')
    {
        $template = new \IGtTransmissionTemplate();
        $template->set_appId($this->gt_appid);              //应用appid
        $template->set_appkey($this->gt_appkey);            //应用appkey
        $template->set_transmissionType(2);          //透传消息类型
        $template->set_transmissionContent($transContent);//透传内容
        // $template->set_title($title);                  //通知栏标题
        // $template->set_text($content);     //通知栏内容
        // $template->set_logo("logo.png"); // 通知栏logo
        // if ($logoUrl) {
        //     $template->set_logoURL($logoUrl); //通知栏logo链接
        // }
        // $template->set_isRing(true);                   //是否响铃
        // $template->set_isVibrate(true);                //是否震动
        // $template->set_isClearable(true);              //通知栏是否可清除
//
        //设置通知定时展示时间，结束时间与开始时间相差需大于6分钟，消息推送后，客户端将在指定时间差内展示消息（误差6分钟）
        //$begin = "2015-02-28 15:26:22";
        //$end = "2015-02-28 15:31:24";
        //$template->set_duration($begin,$end);
        // iOS推送需要设置的pushInfo字段
        $template->set_pushInfo($title, 1, $content, "", $transContent, "", "", $logoUrl, 1);
        return $template;
    }

    public function getPushResult($taskId)
    {
        $params = array();
        $url = 'http://sdk.open.api.igexin.com/apiex.htm';
        $params["action"] = "getPushMsgResult";
        $params["appkey"] = $this->gt_appkey;
        $params["taskId"] = $taskId;
        $sign = $this->createSign($params, $this->gt_mastersecret);
        $params["sign"] = $sign;
        $data = json_encode($params);
        $result = $this->httpPost($url, $data);
        return $result;
    }

    public function createSign($params, $masterSecret)
    {
        $sign = $masterSecret;
        foreach ($params as $key => $val) {
            if (isset($key) && isset($val)) {
                if (is_string($val) || is_numeric($val)) { // 针对非 array object 对象进行sign
                    $sign .= $key . ($val); //urldecode
                }
            }
        }
        return md5($sign);
    }

    public function httpPost($url, $data)
    {
        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_BINARYTRANSFER, 1);
        curl_setopt($curl, CURLOPT_USERAGENT, 'GeTui PHP/1.0');
        curl_setopt($curl, CURLOPT_POST, 1);
        curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 60);
        curl_setopt($curl, CURLOPT_TIMEOUT, 60);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
        $result = curl_exec($curl);
        curl_close($curl);
        return $result;
    }


    /**
     * 推送单个或多个用户
     * @param array|string $deviceId
     * @param array $data
     * @param string $function 数据转换编码函数
     *
     * @return Message
     * @throws \Exception
     */
    public function push($deviceId, array $data, $function = 'json_encode')
    {
        if (empty($deviceId)) {
            throw new \Exception('device_id not empty');
        }

        if (!isset($data['content']) || !isset($data['title'])) {
            throw new \Exception('content and title not empty');
        }
        $type = isset($data['type']) ? $data['type'] : 0;
        $shortUrl = isset($data['url']) ? $data['url'] : '';
        $logoUrl = isset($data['logo_url']) ? $data['logo_url'] : '';
        $deviceOs = isset($data['device_os']) ? $data['device_os'] : 'ios';

        $message = new Message();
        $message->setContent($data['content']);
        $content = $message->getContent();
        $message->setTitle($data['title']);
        $title = $message->getTitle();
        $transContent = $function($data);

        if (is_array($deviceId)) {
            $result = $this->pushMessageToList($deviceId, $transContent, $content, $title, $shortUrl);

        } else {
            $result = $this->pushMessageToSingle($deviceId, $transContent, $content, $title, $shortUrl);

        }
        return $result;

    }


    /**
     * 发送给这个APP所有用户
     *
     * @param array $data
     * @param string $function
     *
     * @return Message
     * @throws \Exception
     */
    public function pushToApp(array $data, $function = 'json_encode')
    {

        if (!isset($data['content']) || !isset($data['title'])) {
            throw new \Exception('content and title not empty');
        }

        $message = new Message();
        $message->setContent($data['content']);
        $content = $message->getContent();
        $message->setTitle($data['title']);
        $title = $message->getTitle();

        $transContent = $function($data);

        $result = $this->pushMsgToApp($transContent, $content, $title);
        return $result;
    }




    //
//服务端推送接口，支持三个接口推送
//1.PushMessageToSingle接口：支持对单个用户进行推送
//2.PushMessageToList接口：支持对多个用户进行推送，建议为50个用户
//3.pushMessageToApp接口：对单个应用下的所有用户进行推送，可根据省份，标签，机型过滤推送
//

//单推接口案例
    function pushMessageToSingle($clientId, $transContent, $content, $title, $shortUrl = '')
    {
        //消息模版：
        $template = $this->getTemplate($content, $title, $transContent,$shortUrl);
        //个推信息体
        $message = new \IGtSingleMessage();
        $message->set_isOffline(true);//是否离线
        $message->set_offlineExpireTime(3600 * 12 * 1000);//离线时间
        $message->set_data($template);//设置推送消息类型
//	$message->set_PushNetWorkType(0);//设置是否根据WIFI推送消息，1为wifi推送，0为不限制推送
        //接收方
        $target = new \IGtTarget();
        $target->set_appId($this->gt_appid);
        $target->set_clientId($clientId);
//    $target->set_alias(Alias);

        try {
            $rep =  $this->obj->pushMessageToSingle($message, $target);
            return $rep;
        } catch (\RequestException $e) {
            $requstId = $e->getRequestId();
            $rep =  $this->obj->pushMessageToSingle($message, $target, $requstId);
            return $rep;
        }

    }

//多推接口案例
    function pushToList($clientIds, $content, $title, $transContent, $shortUrl = '')
    {
        putenv("gexin_pushList_needDetails=true");
        putenv("gexin_pushList_needAsync=true");
        //消息模版：
        $template = $this->getTemplate($content, $title, $transContent,$shortUrl);
        //个推信息体
        $message = new \IGtListMessage();
        $message->set_isOffline(true);//是否离线
        $message->set_offlineExpireTime(3600 * 12 * 1000);//离线时间
        $message->set_data($template);//设置推送消息类型
//    $message->set_PushNetWorkType(1);	//设置是否根据WIFI推送消息，1为wifi推送，0为不限制推送
//    $contentId = $igt->getContentId($message);
        $contentId =  $this->obj->getContentId($message, "toList任务别名功能");    //根据TaskId设置组名，支持下划线，中文，英文，数字

        //接收方1
        $targetList = [];
        foreach ($clientIds as $key => $clientId) {
            $target = new \IGtTarget();
            $target->set_appId($this->gt_appid);
            $target->set_clientId($clientId);
            $targetList[] = $target;
        }

//    $target1->set_alias(Alias);
        $rep =  $this->obj->pushMessageToList($contentId, $targetList);
        return $rep;

    }


//群推接口案例
    function pushMessageToApp($transContent, $content, $title, $shortUrl = '')
    {
        $template = $this->getTemplate($content, $title, $transContent,$shortUrl);
        //个推信息体
        //基于应用消息体
        $message = new \IGtAppMessage();
        $message->set_isOffline(true);
        $message->set_offlineExpireTime(10 * 60 * 1000);//离线时间单位为毫秒，例，两个小时离线为3600*1000*2
        $message->set_data($template);

        $appIdList = array($this->gt_appid);
        $phoneTypeList = array('ANDROID');
        $provinceList = array('浙江');
        $tagList = array('haha');
        //用户属性
        //$age = array("0000", "0010");

        //$cdt = new AppConditions();
        // $cdt->addCondition(AppConditions::PHONE_TYPE, $phoneTypeList);
        // $cdt->addCondition(AppConditions::REGION, $provinceList);
        //$cdt->addCondition(AppConditions::TAG, $tagList);
        //$cdt->addCondition("age", $age);

        $message->set_appIdList($appIdList);
        //$message->set_conditions($cdt->getCondition());

        $rep =  $this->obj->pushMessageToApp($message, "任务组名");

        return $rep;

    }


    protected function getTemplate($content, $title, $transContent, $shortUrl = '')
    {
        $type = $this->push_type;
        switch ($type) {
            case self::ALL:
                return $this->IGtNotificationTemplateDemo($content, $title, $transContent);
            case self::NOTICE:
                return $this->IGtNotyPopLoadTemplateDemo($content, $title, $transContent);
            case self::PENETRATE:
                return $this->IGtTransmissionTemplateDemo($content, $title, $transContent);
            case self::H5:
                return $this->IGtLinkTemplateDemo($content, $title, $shortUrl);
        }
    }


    /**
     * @param mixed $mobile
     *
     * @return Message
     */
    public function setPushType($pushType)
    {
        $this->push_type = $pushType;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getPushTypeTxt($pushType)
    {
        $result = isset(self::$push_type_txt[$pushType]) ? self::$push_type_txt[$pushType] : '没有这个类型，只支持 0|1|2|3';
        return $result;
    }





//所有推送接口均支持四个消息模板，依次为通知弹框下载模板，通知链接模板，通知透传模板，透传模板
//注：IOS离线推送需通过APN进行转发，需填写pushInfo字段，目前仅不支持通知弹框下载功能


//推送通知
    function IGtNotyPopLoadTemplateDemo($content, $title, $transContent)
    {
        $template = new \IGtNotyPopLoadTemplate();

        $template->set_appId($this->gt_appid);//应用appid
        $template->set_appkey($this->gt_appkey);//应用appkey
        //通知栏
        $template->set_notyTitle($title);//通知栏标题
        $template->set_notyContent($content);//通知栏内容
        $template->set_notyIcon("");//通知栏logo
        $template->set_isBelled(true);//是否响铃
        $template->set_isVibrationed(true);//是否震动
        $template->set_isCleared(true);//通知栏是否可清除
        //弹框
        $template->set_popTitle($title);//弹框标题
        $template->set_popContent($transContent);//弹框内容
        $template->set_popImage("");//弹框图片
        $template->set_popButton1("下载");//左键
        $template->set_popButton2("取消");//右键
        //下载
        $template->set_loadIcon("");//弹框图片
        $template->set_loadTitle("地震速报下载");
        $template->set_loadUrl("http://dizhensubao.igexin.com/dl/com.ceic.apk");
        $template->set_isAutoInstall(false);
        $template->set_isActived(true);
        //$template->set_duration(BEGINTIME,ENDTIME); //设置ANDROID客户端在此时间区间内展示消息

        return $template;
    }

    //推送通知链接模板
    function IGtLinkTemplateDemo($content, $title, $url)
    {
        $template = new \IGtLinkTemplate();
        $template->set_appId($this->gt_appid);//应用appid
        $template->set_appkey($this->gt_appkey);//应用appkey
        $template->set_title($title);//通知栏标题
        $template->set_text($content);//通知栏内容
        $template->set_logo("");//通知栏logo
        $template->set_isRing(true);//是否响铃
        $template->set_isVibrate(true);//是否震动
        $template->set_isClearable(true);//通知栏是否可清除
        $template->set_url($url);//打开连接地址
        //$template->set_duration(BEGINTIME,ENDTIME); //设置ANDROID客户端在此时间区间内展示消息
        return $template;
    }


    //透传模板
    function IGtTransmissionTemplateDemo($content, $title, $transContent)
    {
        $template = new \IGtTransmissionTemplate();
        $template->set_appId($this->gt_appid);//应用appid
        $template->set_appkey($this->gt_appkey);//应用appkey
        $template->set_transmissionType(1);//透传消息类型
        $template->set_transmissionContent($transContent);//透传内容
        //$template->set_duration(BEGINTIME,ENDTIME); //设置ANDROID客户端在此时间区间内展示消息
        //APN简单推送
//        $template = new IGtAPNTemplate();
//        $apn = new IGtAPNPayload();
//        $alertmsg=new SimpleAlertMsg();
//        $alertmsg->alertMsg="";
//        $apn->alertMsg=$alertmsg;
////        $apn->badge=2;
////        $apn->sound="";
//        $apn->add_customMsg("payload","payload");
//        $apn->contentAvailable=1;
//        $apn->category="ACTIONABLE";
//        $template->set_apnInfo($apn);
//        $message = new IGtSingleMessage();

        //APN高级推送
        $apn = new \IGtAPNPayload();
        $alertmsg = new \DictionaryAlertMsg();
        $alertmsg->body = $content;
        $alertmsg->actionLocKey = "ActionLockey";
        $alertmsg->locKey = "LocKey";
        $alertmsg->locArgs = array("locargs");
        $alertmsg->launchImage = "launchimage";
//        IOS8.2 支持
        $alertmsg->title = $title;
        $alertmsg->titleLocKey = "TitleLocKey";
        $alertmsg->titleLocArgs = array("TitleLocArg");

        $apn->alertMsg = $alertmsg;
        $apn->badge = 7;
        $apn->sound = "";
        $apn->add_customMsg("payload", "payload");
        $apn->contentAvailable = 1;
        $apn->category = "ACTIONABLE";
        $template->set_apnInfo($apn);

        //PushApn老方式传参
//    $template = new IGtAPNTemplate();
//          $template->set_pushInfo("", 10, "", "com.gexin.ios.silence", "", "", "", "");

        return $template;
    }


    //通知+透传模板
    function IGtNotificationTemplateDemo($content, $title, $transContent)
    {
        $template = new \IGtNotificationTemplate();
        $template->set_appId($this->gt_appid);//应用appid
        $template->set_appkey($this->gt_appkey);//应用appkey
        $template->set_transmissionType(1);//透传消息类型
        $template->set_transmissionContent($transContent);//透传内容
        $template->set_title($title);//通知栏标题
        $template->set_text($content);//通知栏内容
        $template->set_logo("http://wwww.igetui.com/logo.png");//通知栏logo
        $template->set_isRing(true);//是否响铃
        $template->set_isVibrate(true);//是否震动
        $template->set_isClearable(true);//通知栏是否可清除
        //$template->set_duration(BEGINTIME,ENDTIME); //设置ANDROID客户端在此时间区间内展示消息
        return $template;
    }



    function getPersonaTagsDemo() {
        $ret = $this->obj->getPersonaTags($this->gt_appid);
        var_dump($ret);
    }

    function getUserCountByTagsDemo() {
        $tagList = array("金在中","龙卷风");
        $ret = $this->obj->getUserCountByTags($this->gt_appid, $tagList);
        var_dump($ret);
    }

    function getPushMessageResultDemo(){

        $igt = $this->obj;

        $ret = $igt->getPushResult("OSA-0522_QZ7nHpBlxF6vrxGaLb1FA3");
        var_dump($ret);

        $ret = $igt->queryAppUserDataByDate($this->gt_appid,"20140807");
        var_dump($ret);

        $ret = $igt->queryAppPushDataByDate($this->gt_appid,"20140807");
        var_dump($ret);
    }


//用户状态查询
    function getUserStatus($cid) {
        $rep = $this->obj->getClientIdStatus($this->gt_appid,$cid);
        var_dump($rep);
        echo ("<br><br>");
    }

//推送任务停止
    function stoptask(){
        $this->obj->stop("OSA-1127_QYZyBzTPWz5ioFAixENzs3");
    }

//通过服务端设置ClientId的标签
    function setTag($cid){
        $tagList = array('','中文','English');
        $rep = $this->obj->setClientTag($this->gt_appid,$cid,$tagList);
        var_dump($rep);
        echo ("<br><br>");
    }

    function getUserTags($cid) {
        $rep = $this->obj->getUserTags($this->gt_appid,$cid);
        //$rep.connect();
        var_dump($rep);
        echo ("<br><br>");
    }


}
