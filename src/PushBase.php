<?php
namespace Shaozeming\Push;

/**
 *  PushBase.php
 *
 * @author gengzhiguo@xiongmaojinfu.com
 * $Id: OrderService.php 2017-03-21 下午4:57 $
 */
class PushBase implements PushInterface
{

    public function pushOne(){

    }

    public function pushAll(){

    }

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
    public function cancelFixFee()
    {
    }


}
