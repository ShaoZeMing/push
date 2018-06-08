# push
一个集成推送服务包..,基于laravel5.* 框架开发

- 封装了个推服务
- 封装了极光服务


## 使用说明

### 安装


- 方法 1：

    执行命令

    ```
    composer require shaozeming/push 
    ```
    直接运行composer自动安装代码。

- 方法 2：

    在项目根目录的下composer.json文件中添加代码 "shaozeming/push": "dev-master"

    ```
         "require": {
                "shaozeming/push": "dev-master",
            },
    ```
    添加在 require 中。然后执行命令：composer update。


### 使用说明

- 1、添加服务

在 config/app.php 的 providers数组中注册服务：

```
        Shaozeming\Push\PushServiceProvider::class,

```

- 2 复制config配置文件

复制vendor/shaozeming/push/config/push.php文件   ->   config/push.php进行配置

```
<?php
return [
    'driver' => env('SYSTEM_OS') ? env('SYSTEM_OS') : 'develop',
    'push_service' => 'ge_tui',   //使用个推服务
//    'push_service' => 'ji_guang', //使用极光服务

    //开发环境
    'develop' => [

        'getui' => [
                'gt_appid' => '87klYMPe1o515SCcbx7Co5',
                'gt_appkey' => 'dd9XpsgHff89DJgUgvW6L8',
                'gt_appsecret' => 'aKMLyeXLCc8hFpjcuf8gW8',
                'gt_mastersecret' => 'zx85PndZVf8Q1M1Iv9dEy3',
                'gt_domainurl' => 'http://sdk.open.api.igexin.com/apiex.htm',
        ],
        'jiguang' => [
                'gt_appkey' => 'dd9XpsgHff89DJgUgvW6L8',
                'gt_mastersecret' => 'zx85PndZVf8Q1M1Iv9dEy3',
        ],
    ],

    //生产环境
    'production' => [
       //个推配置
        'getui' => [
                'gt_appid' => '87klYMPe1o515SCcbx7Co5',
                'gt_appkey' => 'dd9XpsgHff89DJgUgvW6L8',
                'gt_appsecret' => 'aKMLyeXLCc8hFpjcuf8gW8',
                'gt_mastersecret' => 'zx85PndZVf8Q1M1Iv9dEy3',
                'gt_domainurl' => 'http://sdk.open.api.igexin.com/apiex.htm',
        ],
        //极光配置
        'jigaung' => [
                'gt_appkey' => 'dd9XpsgHff89DJgUgvW6L8',
                'gt_mastersecret' => 'zx85PndZVf8Q1M1Iv9dEy3',
        ],

    ]
];

```


### 代码示例演示


```
<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Log;
use Shaozeming\Push\PushManager;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */


    public function push()
    {
        echo "发送push 中";
        try {
            Log::info('testPush', [__METHOD__]);
            $deviceId = 'b2e5b64931f06f617e363b74c8057cf6';
            多个push对象device_id 用数组传入
//            $deviceId = [
//                'ea34a4715b08b1b8d77aabf36c977cba',
//                'ea34a4715b08b1b8d77aabf36c977cba',
//            ];
            $data = [
                'url' => 'http://test.4d4k.com/push',
                'type' => '点击查看\(^o^)/~',
                'title' => '23232323fdf',
                'content' => $content,
                'id' => '3a92y3GR1neZ',
                'merchant_name' => '米粒科技',
                'big_cat' => '电视机',
                'full_address' => '北京市海淀区五道口清华大学',
            ];

            $getuiResponse = app('PushManager')->driver()->push($deviceId, $data,true);
            $res = json_encode($getuiResponse);
            echo $res;
            Log::info($res, [__METHOD__]);
        } catch (\Exception $e) {
            echo "Error : 错误" . $e->getMessage();
        }

    }
}

```

### 方法介绍

#### 1.方法 public function push($deviceId, array $data, $isNotice = true, $function = 'json_encode'){}

- 针对单个用户或多个用户进行推送使用

| 参数 | 类型 | 默认值 | 说明 |
| ---- | ------ | ---- | ----|
| $deviceId | string或array | 无 | push用户的设备ID，多 |
| $data | array | 无 | push使用的参数 |
| $isNotice | bool | true | 是否通知栏通知。如果为false,只透传，不通知，且透传消息有效时间为只有100秒 |
| $function | function | json_encode | 数据编码转换函数，默认是json_encode,** 目前暂时只支持个推编码函数自定义**|


#### 2.方法 public function pushToApp(array $data,$isNotice=true, $function = 'json_encode'){}

- 针对整个App进行推送使用

| 参数 | 类型 | 默认值 | 说明 |
| ---- | ------ | ---- | ----|
| $data | array | 无 | push使用的参数 |
| $isNotice | bool | true | 是否通知栏通知。如果为false,只透传，不通知，且透传消息有效时间为只有100秒 |
| $function | function | json_encode | 数据编码转换函数，默认是json_encode,** 目前暂时只支持个推编码函数自定义**|




