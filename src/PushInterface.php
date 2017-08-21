<?php
namespace Shaozeming\Push;
/**
 *  PushInterface.php
 *
 * @author szm19920426@gmail.com
 * $Id: PushInterface.php 2017-08-16 上午10:56 $
 */
interface PushInterface
{
    public function pushOne(array $data);


    public function pushAll(array $data);
}
 