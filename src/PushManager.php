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

        $config =  [
        'gt_appid' => '87klYMPe1o515SCcbx7Co5',
        'gt_appkey' => 'dd9XpsgHff89DJgUgvW6L8',
        'gt_appsecret' => 'aKMLyeXLCc8hFpjcuf8gW8',
        'gt_mastersecret' => 'zx85PndZVf8Q1M1Iv9dEy3',
        'gt_domainurl' => 'http://sdk.open.api.igexin.com/apiex.htm',
    ];

        return new GeTuiService($config);
    }


    public function createJiGuangDriver()
    {

        return new JiGuangService();
    }


}
