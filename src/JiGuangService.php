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


    public function push($deviceId, array $data, $function = 'json_encode')
    {

        if (empty($deviceId)) {
            throw new \Exception('device_id not empty');
        }

        if (!isset($data['content']) || !isset($data['title'])) {
            throw new \Exception('content and title not empty');
        }
        $title = $data['title'];
        $content = $data['content'];
        $type = isset($data['type']) ? $data['type'] : 0;
        $shortUrl = isset($data['url']) ? $data['url'] : '';
        $logoUrl = isset($data['logo_url']) ? $data['logo_url'] : '';
        $deviceOs = isset($data['device_os']) ? $data['device_os'] : 'ios';

        $message = new Message();
        $message->setContent($content);
        $content = $message->getContent();
        $message->setTitle($title);
        $title = $message->getTitle();


        $client = $this->obj;
        $push = $client->push();

        $cid = $deviceId;
        $platform = array('ios', 'android');
        $alert = $content;
//            $tag = array('tag1', 'tag2');
//            $regId = array('rid1', 'rid2');
        $ios_notification = array(
            'sound' => 'hello jpush',
            'badge' => '+1',
//                'content-available' => true,
//                'category' => 'jiguang',
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
            'time_to_live' => 100,
            'override_msg_id' => 100,
            'big_push_duration' => 100
        );


        if (is_array($cid)) {
            foreach ($cid as $item) {
                try {

                    $response = $push->setCid($item)
                        ->setPlatform($platform)
                        ->iosNotification($alert, $ios_notification)
                        ->androidNotification($alert, $android_notification)
                        ->message($content, $message)
                        ->options($options)
                        ->send();
//            print_r($response);

                } catch (\JPush\Exceptions\APIConnectionException $e) {
                    // try something here
                    $response = $push->setCid($item)
                        ->setPlatform($platform)
//                ->addTag($tag)
//                ->addRegistrationId($regId)
                        ->iosNotification($alert, $ios_notification)
                        ->androidNotification($alert, $android_notification)
                        ->message($content, $message)
                        ->options($options)
                        ->send();
                } catch (\JPush\Exceptions\APIRequestException $e) {
                    // try something here
                    $response = $push->setCid($item)
                        ->setPlatform($platform)
//                ->addTag($tag)
//                ->addRegistrationId($regId)
                        ->iosNotification($alert, $ios_notification)
                        ->androidNotification($alert, $android_notification)
                        ->message($content, $message)
                        ->options($options)
                        ->send();
                }
            }
        } else {
            try {
                $response = $push->setCid($cid)
                    ->setPlatform($platform)
                    ->iosNotification($alert, $ios_notification)
                    ->androidNotification($alert, $android_notification)
                    ->message($content, $message)
                    ->options($options)
                    ->send();
                return $response;
//            print_r($response);

            } catch (\JPush\Exceptions\APIConnectionException $e) {
                // try something here
                $response = $push->setCid($cid)
                    ->setPlatform($platform)
//                ->addTag($tag)
//                ->addRegistrationId($regId)
                    ->iosNotification($alert, $ios_notification)
                    ->androidNotification($alert, $android_notification)
                    ->message($content, $message)
                    ->options($options)
                    ->send();
                return $response;
            } catch (\JPush\Exceptions\APIRequestException $e) {
                // try something here
                $response = $push->setCid($cid)
                    ->setPlatform($platform)
//                ->addTag($tag)
//                ->addRegistrationId($regId)
                    ->iosNotification($alert, $ios_notification)
                    ->androidNotification($alert, $android_notification)
                    ->message($content, $message)
                    ->options($options)
                    ->send();
                return $response;
            }
        }
    }
}
