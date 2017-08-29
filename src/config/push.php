<?php
return [
    'driver' => env('SYSTEM_OS') ? env('SYSTEM_OS') : 'develop',
    'push_service' => 'ge_tui',   //使用个推服务
//    'push_service' => 'ji_guang', //使用极光服务

   //开发环境
    'develop' => [
        //个推配置
        'getui' => [
                'gt_appid' => '87klYMPe1o515SCcbx7Co5',
                'gt_appkey' => 'dd9XpsgHff89DJgUgvW6L8',
                'gt_appsecret' => 'aKMLyeXLCc8hFpjcuf8gW8',
                'gt_mastersecret' => 'zx85PndZVf8Q1M1Iv9dEy3',
                'gt_domainurl' => 'http://sdk.open.api.igexin.com/apiex.htm',
        ],
        //极光配置
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
