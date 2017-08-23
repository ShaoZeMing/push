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
use Shaozeming\Push\PushManager;

class TestPush extends TestCase
{
    protected $instance;

    public function setUp()
    {
        $config =  [
            'gt_appid' => '87klYMPe1o515SCcbx7Co5',
            'gt_appkey' => 'dd9XpsgHff89DJgUgvW6L8',
            'gt_appsecret' => 'aKMLyeXLCc8hFpjcuf8gW8',
            'gt_mastersecret' => 'zx85PndZVf8Q1M1Iv9dEy3',
            'gt_domainurl' => 'http://sdk.open.api.igexin.com/apiex.htm',
        ];
        $this->instance = (new PushManager())->driver('ge_tui',$config);
    }

    public function testPushManager()
    {
        $this->assertInstanceOf(GeTuiService::class, $this->instance);
    }

    public function testPush()
    {
        echo "发送push 中";
        try {
            Log::info('testPush', [__METHOD__]);
            $deviceId = 'b2e5b64931f06f617e363b74c8057cf6';
            $title = 'getui test';
            $content = '123123,test 您负责的的工单已经追加元';

            $data = [
                'type' => 9,
                'title' => $title,
                'content' => $content,
            ];

            $config =  [
                'gt_appid' => '87klYMPe1o515SCcbx7Co5',
                'gt_appkey' => 'dd9XpsgHff89DJgUgvW6L8',
                'gt_appsecret' => 'aKMLyeXLCc8hFpjcuf8gW8',
                'gt_mastersecret' => 'zx85PndZVf8Q1M1Iv9dEy3',
                'gt_domainurl' => 'http://sdk.open.api.igexin.com/apiex.htm',
            ];

            $push =(new PushManager())->driver('ge_tui',$config);

//            $push = app('PushManager')->driver('ge_tui',$config);
            $getuiResponse = $push->push($deviceId, $data);

            $res = json_encode($getuiResponse);
            echo '<br>';
            echo $res;
            Log::info($res, [__METHOD__]);
        } catch (\Exception $e) {
            echo "Error : 错误" . $e->getMessage();
        }
    }
}
