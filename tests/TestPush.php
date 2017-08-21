<?php
/**
 *  TestSms.php
 *
 * @author gengzhiguo@xiongmaojinfu.com
 * $Id: TestSms.php 2017-08-17 上午10:08 $
 */

namespace Shaozeming\Push\Tests;

use PHPUnit\Framework\TestCase;
use Shaozeming\Push\Message;
use Shaozeming\Push\PushManager;

class TestPush extends TestCase
{
    protected $instance;

    public function setUp()
    {
        $this->instance = (new PushManager())->driver('ge_tui');
    }

    public function testSmsManager()
    {
        $this->assertInstanceOf(PushManager::class, $this->instance);
    }

    public function testMessage()
    {
        $message = new Message();
        $message->setContent('您的验证码是100987');
//        $this->assertEquals('这是一条love', $message->getContent());
        $message->setTitle('我是个推标题');
//        $this->assertEquals('18518480028', $message->getMobile());

        $this->instance->setMessage($message);
        $this->assertInstanceOf(Message::class, $this->instance->getMessage());
        $this->expectException(ShiYuanSmsException::class);
        $this->instance->pushOne();
    }
}
