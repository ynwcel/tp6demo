<?php
// +----------------------------------------------------------------------
// | 系统配置项(自定义)
// +----------------------------------------------------------------------
return [
    //后台配置
    'admin' => [
        'admin_cache_key' => 'tp6',//后台缓存key
        'login_captcha' => 0,//是否后台登录验证码
    ],
    //网站配置
    'website' => [
        'keywords' => '',
        'title' => '',

    ],
    //jwt_salt
    'jwt_config' => [
        'jwt_key'    =>  '111',
        'jwt_leeway' =>  60
    ],
    // paypal 账户配置
    'paypal' => [


    ],
    'paypal_sandbox' => [


    ],
    'oceanpay' => [],
    'oceanpay_sandbox' => [],
    'klarna'   => [],
    'klarna_sandbox' => [],
    'fbconversionapi' => [
        'access_token' => env('fbconversionapi.access_token'),
        'api_version' => env('fbconversionapi.api_version'),
        'fixel_id' => env('fbconversionapi.fixel_id'),
        'is_test' => env('fbconversionapi.is_test'),
        'test_event_code' => env('fbconversionapi.test_event_code'),
    ],
];