<?php
/**
 *  TestSms.php
 *
 * @author szm19920426@gmail.com
 * $Id: TestSms.php 2017-08-17 上午10:08 $
 */

namespace Shaozeming\Push\tests;
require_once dirname(__FILE__) . '/../src/Drivers/getui/IGt.Push.php';

use PHPUnit\Framework\TestCase;
use Shaozeming\Push\GeTuiService;
use Shaozeming\Push\PushManager;


class PushTest extends TestCase
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

//        $config =  [
//            'gt_appkey' => 'de8fbc44a4d7c90630d167ef',
//            'gt_mastersecret' => '23f8e0bc41eca2a11f831939',
//        ];
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
//            $deviceId = 'b2e5b64931f06f617e363b74c8057cf6';
            $deviceId = '160a3797c8310b57df9';
            $deviceId = '2e682657977c5c616481ae76088b033d';
            $title = '我是第一条数据';
            $content = '你好呀您负责的的工单已经追加元';

            $data = [
                'type' => 9,
                'title' => $title,
                'content' => $content,
            ];

//            $getuiResponse = $this->instance->push($deviceId, $data);
            $getuiResponse = $this->instance->pushToApp( $data);
            $res = json_encode($getuiResponse);
            echo '<br>';
            echo $res;
        } catch (\Exception $e) {
            echo "Error : 错误" . $e->getMessage();
        }
    }
}
