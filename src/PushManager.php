<?php
namespace Shaozeming\Push;

/**
 *  PushBase.php
 *
 * @author szm19920426@gmail.com
 * $Id: OrderService.php 2017-03-21 下午4:57 $
 */
class PushManager
{



    protected $drivers = [];



    /**
     * 获取对应的服务模型对象
     *
     * @author szm19920426@gmail.com
     *
     * @param    string    $driver
     * @param    array    $config
     *
     */
    public function driver($driver,$config=[])
    {
        $method = 'create' . $this->studly($driver) . 'Driver';

        if (method_exists($this, $method)) {
            return $this->$method($config);
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



    /**
     * 获取GeTui服务模型对象
     *
     * @author szm19920426@gmail.com
     *
     * @param    array    $config
     *
     */
    public function createGeTuiDriver($config)
    {

        return new GeTuiService($config);
    }



    /**
     * 获取极光推送服务模型对象
     *
     * @author szm19920426@gmail.com
     *
     * @param    array    $config
     *
     */
    public function createJiGuangDriver($config)
    {

        return new JiGuangService($config);
    }


}
