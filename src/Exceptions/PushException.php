<?php

namespace Shaozeming\Push\Exceptions;
/**
 *  SmsException.php
 *
 * @author szm19920426@gmail.com
 * $Id: SmsException.php 2017-08-16 上午11:05 $
 */
class PushException extends \Exception
{
    protected static $errorMsgs = [
        '101' => '缺少用户device_Id',
        '102' => 'title or content is null',
        '103' => 't',
        '104' => '系统忙（因平台侧原因，暂时无法处理提交的短信）',
        '105' => '敏感消息（消息内容包含敏感词）',
        '106' => '消息长度错误（>536或<=0）',
        '107' => '包含错误的手机号码',
        '108' => '手机号码个数错误（群发>50000或<=0）',
        '109' => '无发送额度（该用户可用短信数已使用完）',
        '110' => '不在发送时间内(验证码通知7*24小时发送) ',
        '111' => '超出该账户当月发送额度限制',
        '112' => '无此产品，用户没有订购该产品',
        '113' => 'extno格式错误（非数字或者长度不对）',
        '115' => '自动审核驳回',
        '116' => '签名不合法，未带签名（用户必须带签名的前提下）',
        '117' => 'IP地址认证错误,请求调用的IP地址不是系统登记的IP地址',
        '118' => '用户没有相应的发送权限',
        '119' => '用户已过期',
        '120' => '内容不在白名单中',
    ];
    public function __construct($code)
    {
        parent::__construct(self::$errorMsgs[$code], $code);
    }
}
 