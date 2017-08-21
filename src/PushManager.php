<?php
namespace Shaozeming\Push;

/**
 *  PushBase.php
 *
 * @author gengzhiguo@xiongmaojinfu.com
 * $Id: OrderService.php 2017-03-21 下午4:57 $
 */
class PushManager
{


    /**
     * 后台取消费用处理商家资金解冻和流水
     *
     * @author gengzhiguo@xiongmaojinfu.com
     *
     * @param        $order
     * @param        $fee
     * @param string $bizComment
     *
     */

    protected $drivers = [];

    public function driver($driver)
    {
        $method = 'create' . $this->studly($driver) . 'Driver';

        if (method_exists($this, $method)) {
            return $this->$method();
        }
        throw new \InvalidArgumentException("Driver [$driver] not supported.");
    }


    public function studly($value)
    {
        $value = ucwords(str_replace([
            '-',
            '_',
        ], ' ', $value));

        return str_replace(' ', '', $value);
    }

    public function createGeTuiDriver()
    {

        return new GeTuiService();
    }


    public function createJiGuangDriver()
    {

        return new JiGuangService();
    }


}
