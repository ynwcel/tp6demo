<?php

declare (strict_types=1);

namespace app\common\enum\email;

use app\common\enum\EnumBasics;

/**
 * 枚举类：邮件模板类型
 * Class Email
 * @package app\common\enum\coupon
 */
class Email extends EnumBasics
{
    const SUBSCRIBE = 1;
    const SUBSCRIBE_FOOTER = 101;
    const REGISTER = 2;
    const REGISTERNEW = 201;
    const REGISTERCHECKOUT = 202;

    const ORDER = 3;//下单后订单信息
    const ORDERREGISTRE = 4;//下单后订单信息(未注册用户，同时包含注册信息)
    const NOTPAYFIRST = 5;//未支付订单提醒付款，第一次
    const NOTPAYSECOND = 6;//未支付订单提醒付款，第二次

    const CARTFIRST = 81;//购物车提醒，第1次
    const CARTSECOND = 82;//购物车提醒，第2次
    const CARTTHIRD = 83;//购物车提醒，第3次
    const CARTFOUR = 84;//购物车提醒，第4次
    const CARTFIVE = 85;//购物车提醒，第5次

    const WISHLISTFIRST = 91;//wishlist提醒，第1次
    const WISHLISTSECOND = 92;//wishlist提醒，第2次
    const WISHLISTTHIRD = 93;//wishlist提醒，第3次
    const WISHLISTFOUR = 94;//wishlist提醒，第4次


    /**
     * 获取枚举类型值
     * @return array
     */
    public static function data()
    {
        return [
            self::SUBSCRIBE => [
                'name' => '订阅邮箱',
                'value' => self::SUBSCRIBE
            ],
            self::SUBSCRIBE_FOOTER => [
                'name' => '底部订阅邮箱',
                'value' => self::SUBSCRIBE_FOOTER
            ],
            self::REGISTER => [
                'name' => '用户注册',
                'value' => self::REGISTER
            ],
            self::REGISTERNEW => [
                'name' => '新客订阅',//first  pair 4.95
                'value' => self::REGISTERNEW
            ],
            self::REGISTERCHECKOUT => [
                'name' => '支付页注册',//checkout 注册
                'value' => self::REGISTERCHECKOUT
            ],
            self::ORDER => [
                'name' => '下单',
                'value' => self::ORDER
            ],
            self::ORDERREGISTRE => [
                'name' => '下单未注册用户',
                'value' => self::ORDERREGISTRE
            ],
            self::NOTPAYFIRST => [
                'name' => '未支付订单第一次',
                'value' => self::NOTPAYFIRST
            ],
            self::NOTPAYSECOND => [
                'name' => '未支付订单第二次',
                'value' => self::NOTPAYSECOND
            ],
            self::CARTFIRST => [
                'name' => '购物车提醒第一次',
                'value' => self::CARTFIRST
            ],
            self::CARTSECOND => [
                'name' => '购物车第二次',
                'value' => self::CARTSECOND
            ],
            self::CARTTHIRD => [
                'name' => '购物车第三次',
                'value' => self::CARTTHIRD
            ],
            self::CARTFOUR => [
                'name' => '购物车第4次',
                'value' => self::CARTFOUR
            ],
            self::CARTFIVE => [
                'name' => '购物车第5次',
                'value' => self::CARTFIVE
            ],
            self::WISHLISTFIRST => [
                'name' => 'wishlist第一次',
                'value' => self::WISHLISTFIRST
            ],
            self::WISHLISTSECOND => [
                'name' => 'wishlist第二次',
                'value' => self::WISHLISTSECOND
            ],
            self::WISHLISTTHIRD => [
                'name' => 'wishlist第三次',
                'value' => self::WISHLISTTHIRD
            ],
            self::WISHLISTFOUR => [
                'name' => 'wishlist第四次',
                'value' => self::WISHLISTFOUR
            ],
        ];
    }
}