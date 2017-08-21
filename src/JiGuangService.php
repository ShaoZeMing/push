<?php

namespace Shaozeming\Push;


use Illuminate\Support\Facades\Log;
use Laravel\Passport\HasApiTokens;

require_once  dirname(__FILE__).'/Drivers/jiguang/autoload.php';
use JPush\Client as JPush;


class JiGuangService extends PushBase
{
	// use AuthorizesRequests, ValidatesRequests;

    public $getui;
	public function __construct()
    {
        $driver = config('getui.driver');
        $tag = config('getui.tag');
        $params = config('getui.'.$driver.'.'.$tag);
        $this->getui = new \IGeTui($params['gt_domainurl'], $params['gt_appkey'], $params['gt_mastersecret'], $ssl = NULL);
        $this->gt_appid     = $params['gt_appid'];
        $this->gt_appkey    = $params['gt_appkey'];
        $this->gt_appsecret = $params['gt_appsecret'];
        $this->gt_mastersecret = $params['gt_mastersecret'];
    }

    public function getMerInstance()
    {
        $driver = config('getui.driver');
        $tag = config('getui.tag');
        $params = config('getui.'.$driver.'.'.$tag);
        $this->getui = new \IGeTui($params['gt_domainurl'], $params['gt_appkey'], $params['gt_mastersecret'], $ssl = NULL);
        $this->gt_appid     = $params['gt_appid'];
        $this->gt_appkey    = $params['gt_appkey'];
        $this->gt_appsecret = $params['gt_appsecret'];
        $this->gt_mastersecret = $params['gt_mastersecret'];
        return $this;
    }

    public function pushToSignal($clientId, $transContent, $content, $title, $shortUrl='', $deviceOs='ios', $logoUrl='')
    {
        //消息模版：
        // 4.NotyPopLoadTemplate：通知弹框下载功能模板
        $template = $this->getTransmissionTemplateDemo($transContent, $content, $title);
        //定义"SingleMessage"
        $message = new \IGtSingleMessage();

        $message->set_isOffline(true);//是否离线
        $message->set_offlineExpireTime(3600*12*1000);//离线时间
        $message->set_data($template);//设置推送消息类型
        //$message->set_PushNetWorkType(0);//设置是否根据WIFI推送消息，2为4G/3G/2G，1为wifi推送，0为不限制推送
        //接收方
        $target = new \IGtTarget();
        $target->set_appId($this->gt_appid);
        $target->set_clientId($clientId);
    //    $target->set_alias(Alias);
        // var_export($this->getui);exit;
        try {
            $rep = $this->getui->pushMessageToSingle($message, $target);
        } catch (\RequestException $e) {
            $requestId = $e->getRequestId();
            //失败时重发
            $rep = $this->getui->pushMessageToSingle($message, $target, $requestId);
        }
        return $rep;
    }


    public function pushMessageToList($clientIds, $transContent, $content, $title, $shortUrl = '')
    {
        $template = $this->getTransmissionTemplateDemo($transContent, $content, $title);
        //定义"ListMessage"信息体
        $message = new \IGtListMessage();
        $message->set_isOffline(true);//是否离线
        $message->set_offlineExpireTime(3600*12*1000);//离线时间
        $message->set_data($template);//设置推送消息类型
        $message->set_PushNetWorkType(0);//设置是否根据WIFI推送消息，1为wifi推送，0为不限制推送
        $contentId  = $this->getui->getContentId($message);
        $targetList = [];
        foreach($clientIds as $key => $clientId) {
            $target = new \IGtTarget();
            $target->set_appId($this->gt_appid);
            $target->set_clientId($clientId);
            $targetList[] = $target;
            Log::info('c=getuiService f=pushMessageToList clientId='.$clientId);
        }
        try{
            $rep = $this->getui->pushMessageToList($contentId, $targetList);
        } catch (\RequestException $e) {
            $requestId = $e->getRequestId();
            $rep = $this->getui->pushMessageToList($contentId, $targetList, $requestId);
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
        $appIdList     = array($this->gt_appid);
        $phoneTypeList = array('ANDROID', 'IOS');

        // $cdt = new \AppConditions();
        // $cdt->addCondition(\AppConditions::PHONE_TYPE, $phoneTypeList);
        $message->set_appIdList($appIdList);
        // $message->set_conditions($cdt);
        $message->set_PushNetWorkType(0);//设置是否根据WIFI推送消息，1为wifi推送，0为不限制推送
        // $message->set_speed(100);
        $rep = $this->getui->pushMessageToApp($message);
        Log::info('c=getuiService f=pushMsgToApp rep='.json_encode($rep));
        return $rep;
    }

    public function getNotificationTemplateDemo($transContent, $content, $title, $logoUrl='')
    {
        $template =  new \IGtNotificationTemplate();
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

    public function getTransmissionTemplateDemo($transContent, $content, $title, $logoUrl='')
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
        $sign   = $this->createSign($params,$this->gt_mastersecret);
        $params["sign"] = $sign;
        $data   = json_encode($params);
        $result = $this->httpPost($url,$data);
        return $result;
    }
    public function createSign($params,$masterSecret)
    {
        $sign=$masterSecret;
        foreach ($params as $key => $val){
            if (isset($key) && isset($val) ){
                if(is_string($val) || is_numeric($val) ){ // 针对非 array object 对象进行sign
                    $sign .= $key . ($val); //urldecode
                }
            }
        }
        return md5($sign);
    }

    public function httpPost($url,$data)
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



    public function pushOne()
    {

        $app_key = getenv('app_key');
        $master_secret = getenv('master_secret');
        $registration_id = getenv('registration_id');

        $client = new JPush($app_key, $master_secret);

// 简单推送示例
// 这只是使用样例,不应该直接用于实际生产环境中 !!

        $push_payload = $client->push()
            ->setPlatform('all')
            ->addAllAudience()
            ->setNotificationAlert('Hi, JPush');
        try {
            $response = $push_payload->send();
            print_r($response);
        } catch (\JPush\Exceptions\APIConnectionException $e) {
            // try something here
            print $e;
        } catch (\JPush\Exceptions\APIRequestException $e) {
            // try something here
            print $e;
        }

// 完整的推送示例
// 这只是使用样例,不应该直接用于实际生产环境中 !!
        try {
            $response = $client->push()
                ->setPlatform(array('ios', 'android'))
                // 一般情况下，关于 audience 的设置只需要调用 addAlias、addTag、addTagAnd  或 addRegistrationId
                // 这四个方法中的某一个即可，这里仅作为示例，当然全部调用也可以，多项 audience 调用表示其结果的交集
                // 即是说一般情况下，下面三个方法和没有列出的 addTagAnd 一共四个，只适用一个便可满足大多数的场景需求

                // ->addAlias('alias')
                ->addTag(array('tag1', 'tag2'))
                // ->addRegistrationId($registration_id)

                ->setNotificationAlert('Hi, JPush')
                ->iosNotification('Hello IOS', array(
                    'sound' => 'sound.caf',
                    // 'badge' => '+1',
                    // 'content-available' => true,
                    // 'mutable-content' => true,
                    'category' => 'jiguang',
                    'extras' => array(
                        'key' => 'value',
                        'jiguang'
                    ),
                ))
                ->androidNotification('Hello Android', array(
                    'title' => 'hello jpush',
                    // 'builder_id' => 2,
                    'extras' => array(
                        'key' => 'value',
                        'jiguang'
                    ),
                ))
                ->message('message content', array(
                    'title' => 'hello jpush',
                    // 'content_type' => 'text',
                    'extras' => array(
                        'key' => 'value',
                        'jiguang'
                    ),
                ))
                ->options(array(
                    // sendno: 表示推送序号，纯粹用来作为 API 调用标识，
                    // API 返回时被原样返回，以方便 API 调用方匹配请求与返回
                    // 这里设置为 100 仅作为示例

                    // 'sendno' => 100,

                    // time_to_live: 表示离线消息保留时长(秒)，
                    // 推送当前用户不在线时，为该用户保留多长时间的离线消息，以便其上线时再次推送。
                    // 默认 86400 （1 天），最长 10 天。设置为 0 表示不保留离线消息，只有推送当前在线的用户可以收到
                    // 这里设置为 1 仅作为示例

                    // 'time_to_live' => 1,

                    // apns_production: 表示APNs是否生产环境，
                    // True 表示推送生产环境，False 表示要推送开发环境；如果不指定则默认为推送生产环境

                    'apns_production' => false,

                    // big_push_duration: 表示定速推送时长(分钟)，又名缓慢推送，把原本尽可能快的推送速度，降低下来，
                    // 给定的 n 分钟内，均匀地向这次推送的目标用户推送。最大值为1400.未设置则不是定速推送
                    // 这里设置为 1 仅作为示例

                    // 'big_push_duration' => 1
                ))
                ->send();
            print_r($response);

        } catch (\JPush\Exceptions\APIConnectionException $e) {
            // try something here
            print $e;
        } catch (\JPush\Exceptions\APIRequestException $e) {
            // try something here
            print $e;
        }

    }


    public function pushAll()
    {
        parent::pushAll(); // TODO: Change the autogenerated stub
    }
}
