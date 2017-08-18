<?php

namespace Shaozeming\Push;

use App\Entities\Message;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;
use Laravel\Passport\HasApiTokens;
use Vinkla\Hashids\Facades\Hashids;

class PushMsgService
{


    /**
     * 发送push和写入站内信
     * @param Illuminate\Database\Eloquent\Model $model
     * @param string $title 发送标题
     * @param string $content 发送内容
     * @param string $toType 消息类型  h5 | order_detail | wallet
     * @param App\Entities\Order $order 工单模型
     * @param string $status all:push+msg，push:push, msg:msg
     * @param string $url h5 网址
     *
     * @return mixed
     * @throws Exception
     */
    public function  pushMsg(Model $model, $title, $content, $toType, $order = null, $status = 'push', $url = '')
    {
        $context = [
            'model' => json_encode($model),
            'method' => __METHOD__,
        ];
        $status = strtolower($status);
        $userId = $model->id;
        $orderId = is_object($order) ? $order->id : 0;
        $class = get_class($model);
        $userTxt = strtolower(substr(strrchr($class, '\\'), 1) . '_' . $toType);
        if($toType != 'h5'){
            $url = '';
        }
        switch ($status) {
            case 'all'://发送push+msg
                $pushType = config('getui.biz_type.push_' . $userTxt, 0);
                $msgType = config('getui.biz_type.msg_' . $userTxt, 0);
                $apps = $model->apps()->where('is_logout', 0)->first(['device_id']);
                if (!count($apps)) {
                    Log::error('推送失败，没有找到用户device_id', $context);
                } else {
                    $this->sentPush($apps->device_id, $title, $content, $pushType, $order,$url);
                }
                $this->insertMsg($userId, $orderId, $title, $content, $msgType, $class);
                break;
            case 'push':// 只发送push
                $pushType = config('getui.biz_type.push_' . $userTxt, 0);
                $apps = $model->apps()->where('is_logout', 0)->first(['device_id']);
                if (!count($apps)) {
                    Log::error('推送失败，没有找到用户device_id', $context);
                } else {
                    $this->sentPush($apps->device_id, $title, $content, $pushType, $order,$url);
                }
                break;
            case 'msg'://只发账内信
                $msgType = config('getui.biz_type.msg_' . $userTxt, 0);
                $this->insertMsg($userId, $orderId, $title, $content, $msgType, $class);
                break;

        }

    }


    /**
     * push客户端登出
     * @param Illuminate\Database\Eloquent\Model $model
     * @param string $deviceId 设备ID
     * @param string $deviceOs 设备型号
     * @param string $title 发送标题
     * @param mixed $content 发送内容
     * @param string $status all:push+msg，push:push, msg:msg
     *
     * @return mixed
     * @throws Exception
     */
    public function  sentPushLogOut($model, $deviceId = '', $deviceOs = 'ios', $title = '', $content = '')
    {
        $context = [
            'model' => json_encode($model),
            'method' => __METHOD__,
        ];
        Log::info('退出登录', $context);
        $class = get_class($model);
        $userTxt = strtolower(substr(strrchr($class, '\\'), 1));
        $typeTxt = strtolower($userTxt . '_logout');
        $pushType = config('getui.biz_type.push_' . $typeTxt, 0);

        if ($deviceId) {
            $appData = $model->apps()->where('is_logout', 0)->where('device_id', '!=', $deviceId)->get()->toArray();
            $model->apps()->where('device_id', '!=', $deviceId)->update(['is_logout' => 1]);
        } else {
            $appData = $model->apps()->where('is_logout', 0)->get()->toArray();
            $model->apps()->where($userTxt . '_id', $model->id)->update(['is_logout' => 1]);
        }
        if (count($appData)) {
            $deviceIds = array_column($appData, 'device_id');
            Log::info('app->id数据', $deviceIds);
            $content = $content ? $content : "您好，您的账户已经修改密码，如果不是您的操作，请及时修改密码";
            $title = $title ? $title : '退出登录';
            $this->sentPushList($deviceIds, $title, $content, $pushType);
        } elseif (!empty($deviceId)) {
            $data = [
                $userTxt . '_id' => $model->id,
                'device_id' => $deviceId,
                'device_os' => strtolower($deviceOs),
                'is_logout' => 0,
            ];
            $model->apps()->updateOrCreate(['device_id' => $deviceId], $data);
        }

    }


    /**
     * 发送push和写入站内信
     * @param Illuminate\Database\Eloquent\Model $model
     * @param string $title 发送标题
     * @param string $content 发送内容
     * @param string $toType 消息类型  h5 | order_detail | wallet
     * @param App\Entities\Order $order 工单模型
     * @param string $status all:push+msg，push:push, msg:msg
     *
     * @return mixed
     * @throws Exception
     */
    public function  pushMsgList(Model $model, $title, $content, $toType, $order = null, $status = 'push' , $url = '')
    {
        $context = [
            'model' => json_encode($model),
            'method' => __METHOD__,
        ];
        $status = strtolower($status);
        $userId = $model->id;
        $orderId = is_object($order) ? $order->id : 0;
        $class = get_class($model);
        $userTxt = strtolower(substr(strrchr($class, '\\'), 1) . '_' . $toType);

        switch ($status) {
            case 'all'://发送push+msg
                $pushType = config('getui.biz_type.push_' . $userTxt, 0);
                $msgType = config('getui.biz_type.msg_' . $userTxt, 0);
                $apps = $model->apps()->where('is_logout', 0)->get(['device_id'])->toArray();
                if (!count($apps)) {
                    Log::error('推送失败，没有找到用户device_id', $context);
                } else {
                    $deviceIds = array_column($apps, 'device_id');
                    $this->sentPushList($deviceIds, $title, $content, $pushType,$order,$url);
                }
                $this->insertMsg($userId, $orderId, $title, $content, $msgType, $class);
                break;
            case 'push':// 只发送push
                $pushType = config('getui.biz_type.push_' . $userTxt, 0);
                $apps = $model->apps()->where('is_logout', 0)->get(['device_id'])->toArray();
                if (!count($apps)) {
                    Log::error('推送失败，没有找到用户device_id', $context);
                } else {
                    $deviceIds = array_column($apps, 'device_id');
                    $this->sentPushList($deviceIds, $title, $content, $pushType,$order,$url);
                }
                break;
            case 'msg'://只发账内信
                $msgType = config('getui.biz_type.msg_' . $userTxt, 0);
                $this->insertMsg($userId, $orderId, $title, $content, $msgType, $class);
                break;

        }

    }


    /**
     * 发送push
     * @param mixed $title 发送标题
     * @param mixed $content 发送内容
     * @param string $bizType 消息类型  h5 | order_detail | order_place | wallet
     * @param \App\Entities\Order $order 工单模型
     * @param string $status all:push+msg，push:push, msg:msg
     * @param string $url h5页面url
     *
     * @return mixed
     * @throws Exception
     */
    public function  sentPush($deviceId, $title, $content, $bizType, $order = null,$url = '')
    {
        $context = [
            'method' => __METHOD__,
        ];
        $transContentArr = [
            'title' => $title,
            'content' => $content,
            'type' => $bizType,
        ];
        if ($url) {
            $transContentArr['link_url'] = $url;
        }
        if (is_object($order)) {
            $transContentArr['id'] = Hashids::encode($order->id);
            $transContentArr['state'] = $order->state;
        }
        $transContent = json_encode($transContentArr);
        $getuiResponse = app('GeTuiService')->pushToSignal($deviceId, $transContent, $content, $title);
        Log::info(json_encode($getuiResponse), $context);

    }


    /**
     * 发送push
     * @param array $deviceIds 发送标题
     * @param mixed $title 发送标题
     * @param mixed $content 发送内容
     * @param string $bizType 消息类型  h5 | order_detail | order_place | wallet
     * @param \App\Entities\Order $order 工单模型
     * @param string $status all:push+msg，push:push, msg:msg
     *
     * @return mixed
     * @throws Exception
     */
    public function  sentPushList(array $deviceIds, $title, $content, $bizType, $order = null, $url = '')
    {
        $context = [
            'method' => __METHOD__,
        ];
        $transContentArr = [
            'title' => $title,
            'content' => $content,
            'type' => $bizType,
        ];
        if ($url) {
            $transContentArr['link_url'] = $url;
        }
        if (is_object($order)) {
            $transContentArr['id'] = Hashids::encode($order->id);
            $transContentArr['state'] = $order->state;
        }
        $transContent = json_encode($transContentArr);
        $getuiResponse = app('MerGeTuiService')->pushMessageToList($deviceIds, $transContent, $content, $title);
        Log::info(json_encode($getuiResponse), $context);

    }

    /**
     * 写入账内信
     * @param int $userId 商家或师傅ID
     * @param int $orderId 工单ID
     * @param string $title 标题
     * @param string $content 内容
     * @param int $pushType 属性
     * @param string $objType 对象属性
     *
     * @return mixed
     * @throws Exception
     */

    public function insertMsg($userId, $orderId, $title, $content, $pushType, $objType)
    {
        // 写入站内信消息
        $context = [
            'method' => __METHOD__,
        ];
        $data = [
            'title' => $title,
            'content' => $content,
            'push_type' => $pushType,
            'state' => 1,
            'msg_type' => 1,
            'order_id' => $orderId,
            'sent_at' => date('Y-m-d H:i:s'),
        ];

        $message = Message::create($data);
        if (!$message) {
            Log::error('写入站内信失败', [__METHOD__]);
            throw new \Exception('写入站内信失败');
        }
        $message->receivers()->create([
            'receiver_id' => $userId,
            'receiver_type' => $objType,
        ]);

        Log::info(json_encode($message), $context);
        return $message;
    }


}
