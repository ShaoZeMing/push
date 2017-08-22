<?php
/**
 *  TestSms.php
 *
 * @author gengzhiguo@xiongmaojinfu.com
 * $Id: TestSms.php 2017-08-17 上午10:08 $
 */

namespace Shaozeming\Push\Tests;

use PHPUnit\Framework\TestCase;
use Shaozeming\Push\GeTuiService;
use Shaozeming\Push\Message;
use Shaozeming\Push\PushManager;

class TestPush extends TestCase
{
    protected $instance;

    public function setUp()
    {
        $this->instance = (new PushManager())->driver('ge_tui');
    }

    public function testPushManager()
    {
        $this->assertInstanceOf(GeTuiService::class, $this->instance);
    }

    public function testPush()
    {
        try {
        $message = new Message();
        $message->setContent('您的验证码是100987');
        $this->assertEquals('您的验证码是100987', $message->getContent());
        $message->setTitle('getui test');
        $this->assertEquals('getui test', $message->getTitle());
//
//        $this->instance->setMessage($message);
//        $this->assertInstanceOf(Message::class, $this->instance->getMessage());
        $this->expectException(\Exception::class);



        echo "发送push 中";

            $deviceId = 'b2e5b64931f06f617e363b74c8057cf6';
            $title = 'getui test';
            $content = '123123,test 您负责的的工单已经追加元';

//            $title = request()->get('title', $title);
//            $content = request()->get('content', $content);
            $transContentArr = [
                'title' => $title,
                'content' => $content,
            ];

            //        $deviceId = request()->get('device_id',$deviceId);
//
            $data = [
                'type' => 9,
                'title' => $title,
                'content' => $content,
                'device_id'=> $deviceId,
            ];
//

//        $pushs =json_encode($push);
//        $res =json_encode($getuiResponse);
//        echo '<br>';
//        echo $pushs;
//        echo '<br>';
//        echo $res;

            $transContent = json_encode($transContentArr);
//            $push = app('PushManager')->driver('ge_tui');
//            $getuiResponse = $this->instance->pushOne($data);
            $getuiResponse =  $this->instance->pushToSignal($deviceId, $transContent, $content, $title);
//            $getuiResponse = app('GeTuiService')->pushToSignal($deviceId, $transContent, $content, $title);

            $res = json_encode($getuiResponse);
            echo '<br>';
            echo $res;
            Log::info($res, [__METHOD__]);
        } catch (\Exception $e) {
            echo "Error : 错误" . $e->getMessage();
        }
    }
}
