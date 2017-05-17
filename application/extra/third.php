<?php
// 第三方登录配置
return [
    // 微信
    // 申请请到https://open.weixin.qq.com
    'wechat' => [
        'app_id'     => '',
        'app_secret' => '',
        'callback'   => '',
        'scope'      => 'snsapi_userinfo',
    ],
    // QQ
    // 申请请到https://connect.qq.com
    'qq'     => [
        'app_id'     => '123546',
        'app_secret' => 'asd456ad',
        'scope'      => 'get_user_info',
        'callback'   => '',
    ],
];
