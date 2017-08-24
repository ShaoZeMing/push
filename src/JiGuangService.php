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
            $tag = config('push.tag');
            $params = config('push.' . $driver . '.jigaung.' . $tag);
        }
        $this->obj = new JPush($params['gt_appkey'], $params['gt_mastersecret']);
        $this->gt_appid = $params['gt_appid'];
        $this->gt_appkey = $params['gt_appkey'];
        $this->gt_appsecret = $params['gt_appsecret'];
        $this->gt_mastersecret = $params['gt_mastersecret'];

    }

    public function getMerInstance($config=[])
    {
        if (count($config)) {
            $params = $config;
        } else {
            $driver = config('push.driver');
            $tag = config('push.tag');
            $params = config('push.' . $driver . '.jigaung.' . $tag);
        }
        $this->obj = new JPush($params['gt_appkey'], $params['gt_mastersecret']);
        $this->gt_appid = $params['gt_appid'];
        $this->gt_appkey = $params['gt_appkey'];
        $this->gt_appsecret = $params['gt_appsecret'];
        $this->gt_mastersecret = $params['gt_mastersecret'];
        return $this;
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
        $options = array(
            'sendno' => 100,
//            'time_to_live' => 100,
//            'override_msg_id' => 100,
//            'big_push_duration' => 100
        );

        $request = $this->obj->push()
            ->setPlatform($platform)
            ->addRegistrationId($deviceId)
            ->iosNotification($alert, $ios_notification)
            ->androidNotification($alert, $android_notification)
            ->message($content, $message)
            ->options($options);

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
     * @param array $data
     * @param string $function
     *
     * @return Message
     * @throws \Exception
     */
    public function pushToApp(array $data,$function = 'json_encode')
    {

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
        $options = array(
            'sendno' => 100,
//            'time_to_live' => 100,
//            'override_msg_id' => 100,
//            'big_push_duration' => 100
        );
        $request = $this->obj->push()
            ->setPlatform($platform)
            ->addAllAudience()
            ->iosNotification($alert, $ios_notification)
            ->androidNotification($alert, $android_notification)
            ->message($content, $message)
            ->options($options);

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
