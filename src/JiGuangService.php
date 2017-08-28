<?php

namespace Shaozeming\Push;


require_once dirname(__FILE__) . '/Drivers/jiguang/autoload.php';
use JPush\Client as JPush;


class JiGuangService extends PushBase
{
    // use AuthorizesRequests, ValidatesRequests;

    public $obj;

    public function __construct($config = [])
    {
        if (count($config)) {
            $params = $config;
        } else {
            $driver = config('push.driver');
            $params = config('push.' . $driver . '.jigaung');
        }
        $this->obj = new JPush($params['gt_appkey'], $params['gt_mastersecret']);
        $this->gt_appkey = $params['gt_appkey'];
        $this->gt_mastersecret = $params['gt_mastersecret'];

    }

    public function getMerInstance($config=[])
    {
        if (count($config)) {
            $params = $config;
        } else {
            $driver = config('push.driver');
            $params = config('push.' . $driver . '.jigaung');
        }
        $this->obj = new JPush($params['gt_appkey'], $params['gt_mastersecret']);
        $this->gt_appkey = $params['gt_appkey'];
        $this->gt_mastersecret = $params['gt_mastersecret'];
        return $this;
    }



    /**
     * 推送单个或多个用户
     * @param array|string $deviceId
     * @param array $data
     * @param bool $isNotice  是否通知
     * @param string $function 数据转换编码函数
     *
     * @return Message
     * @throws \Exception
     */
    public function push($deviceId, array $data, $isNotice = true, $function = 'json_encode')
    {

        if (empty($deviceId)) {
            throw new \Exception('device_id not empty');
        }

        if (!isset($data['content']) || !isset($data['title'])) {
            throw new \Exception('content and title not empty');
        }

        $message = new Message();
        $message->setContent($data['content']);
        $content = $message->getContent();
        $message->setTitle($data['title']);
        $title = $message->getTitle();

        $platform = array('ios', 'android');
        $alert = $content;
        $ios_notification = array(
            'sound' => 'hello jpush',
            'badge' => '+1',
            'extras' => $data,
        );
        $android_notification = array(
            'title' => $title,
            'build_id' => 2,
            'extras' => $data,

        );
        $message = array(
            'title' => $title,
            'content_type' => 'text',
            'extras' => $data,
        );

        $options['time_to_live'] = 3600*24;

        //仅仅透传不提示，不通知，过期时间100秒
        if($isNotice){
            $request = $this->obj->push()
                ->setPlatform($platform)
                ->addRegistrationId($deviceId)
                ->iosNotification($alert, $ios_notification)
                ->androidNotification($alert, $android_notification)
                ->message($content, $message)
                ->options($options);
        }else{
            $options['time_to_live'] = 100;
            $request = $this->obj->push()
                ->setPlatform($platform)
                ->addRegistrationId($deviceId)
                ->message($content, $message)
                ->options($options);
        }


        try {
            $response  =  $request ->send();
            return $response;
        } catch (\JPush\Exceptions\APIConnectionException $e) {
            $response  =  $request ->send();
            return $response;
        } catch (\JPush\Exceptions\APIRequestException $e) {
            $response  =  $request ->send();
            return $response;
        }

    }



    /**
     * 发送给这个APP所有用户
     *
     * @param array $data  数据
     * @param bool $isNotice  是否通知
     * @param string $function 编码函数
     *
     * @return Message
     * @throws \Exception
     */
    public function pushToApp(array $data, $isNotice = true,$function = 'json_encode')
    {

        if (!isset($data['content']) || !isset($data['title'])) {
            throw new \Exception('content and title not empty');
        }

        $message = new Message();
        $message->setContent($data['content']);
        $content = $message->getContent();
        $message->setTitle($data['title']);
        $title = $message->getTitle();


        $platform = 'all';
        $alert = $content;
        $ios_notification = array(
            'sound' => 'hello jpush',
            'badge' => '+1',
            'extras' => $data,
        );
        $android_notification = array(
            'title' => $title,
            'build_id' => 2,
            'extras' => $data,

        );
        $message = array(
            'title' => $title,
            'content_type' => 'text',
            'extras' => $data,
        );

        $options['time_to_live'] = 3600*24;
        //仅仅透传不提示，不通知，过期时间100秒
        if($isNotice){
            $request = $this->obj->push()
                ->setPlatform($platform)
                ->addAllAudience()
                ->iosNotification($alert, $ios_notification)
                ->androidNotification($alert, $android_notification)
                ->message($content, $message)
                ->options($options);
        }else{
            $options['time_to_live'] = 100;
            $request = $this->obj->push()
                ->setPlatform($platform)
                ->addAllAudience()
                ->message($content, $message)
                ->options($options);
        }


        try {
            $response  =  $request ->send();
            return $response;
        } catch (\JPush\Exceptions\APIConnectionException $e) {
            $response  =  $request ->send();
            return $response;
        } catch (\JPush\Exceptions\APIRequestException $e) {
            $response  =  $request ->send();
            return $response;
        }

    }
}
