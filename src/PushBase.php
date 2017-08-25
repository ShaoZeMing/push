<?php
namespace Shaozeming\Push;

/**
 *  PushBase.php
 *
 * @author szm19920426@gmail.com
 * $Id: OrderService.php 2017-03-21 下午4:57 $
 */
class PushBase implements PushInterface
{
    const ALL = 0;
    const NOTICE = 1;
    const PENETRATE = 2;
    const H5 = 3;
    public $push_type = self::ALL;  //默认通知+透传
    public static $push_type_txt = [
        self::NOTICE => '点击弹窗下载',
        self::PENETRATE => '透传',
        self::ALL => '通知+透传',
        self::H5 => 'H5',
    ];

    public function push($deviceId, array $data)
    {

    }

    public function pushToApp(array $data){

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


}
