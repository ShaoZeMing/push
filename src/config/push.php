<?php
return [
    'driver' => env('SYSTEM_OS') ? env('SYSTEM_OS') : 'develop',
    'tag' => 'demo',
    'develop' => [
        'getui' => [
            'demo' => [
                'gt_appid' => '87klYMPe1o515SCcbx7Co5',
                'gt_appkey' => 'dd9XpsgHff89DJgUgvW6L8',
                'gt_appsecret' => 'aKMLyeXLCc8hFpjcuf8gW8',
                'gt_mastersecret' => 'zx85PndZVf8Q1M1Iv9dEy3',
                'gt_domainurl' => 'http://sdk.open.api.igexin.com/apiex.htm',
            ],
        ],
        'jiguang' => [
            'demo' => [
                'gt_appkey' => 'dd9XpsgHff89DJgUgvW6L8',
                'gt_mastersecret' => 'zx85PndZVf8Q1M1Iv9dEy3',
            ],
        ],


    ],


    'production' => [
        'getui' => [
            'demo' => [
                'gt_appid' => '87klYMPe1o515SCcbx7Co5',
                'gt_appkey' => 'dd9XpsgHff89DJgUgvW6L8',
                'gt_appsecret' => 'aKMLyeXLCc8hFpjcuf8gW8',
                'gt_mastersecret' => 'zx85PndZVf8Q1M1Iv9dEy3',
                'gt_domainurl' => 'http://sdk.open.api.igexin.com/apiex.htm',
            ],
        ],
        'jigaung' => [
            'demo' => [
                'gt_appkey' => 'dd9XpsgHff89DJgUgvW6L8',
                'gt_mastersecret' => 'zx85PndZVf8Q1M1Iv9dEy3',
            ],
        ],

    ],


    'push_flag' => TRUE,

    //信息地址
    'biz_type' => [
        'msg_worker_h5' => 1,
        'msg_worker_order_detail' => 2,
        'msg_worker_wallet' => 3,
        'msg_worker_order_place' => 4,
        'push_worker_order_place' => 5,
        'push_worker_order_detail' => 6,
        'push_worker_wallet' => 7,
        'push_worker_H5' => 8,
        'push_worker_logout' => 10,
        'msg_merchant_order_detail' => 9,
        'push_merchant_order_detail' => 11,
        'push_merchant_wallet' => 12,
        'push_merchant_H5' => 13,
        'push_merchant_logout' => 14,
        'msg_merchant_wallet' => 15,
        'msg_merchant_H5' => 16,
    ],
];
