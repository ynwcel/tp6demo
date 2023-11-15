<?php
declare (strict_types=1);

namespace app\common\service;

use think\console\command\optimize\Config;

/**
 * Notes：fb 时间提交
 * {2023/11/13}
 * Class FbConversionsApiService
 * @package app\common\service
 */
class FbConversionsApiService extends BaseService
{
    /**
     * Notes：发送数据
     * {2023/11/13}
     * @param $data
     */
    public static function sendData($data)
    {
        $ret = [];
        $ret['access_token'] = config('system.fbconversionapi.access_token');
        if (config('system.fbconversionapi.is_test')){
            $ret['test_event_code'] = config('system.fbconversionapi.test_event_code');
        }

        $eventData = self::getEventData($data);

        //本地不发送数据
        if ($eventData['user_data']['client_ip_address']){
            $ret['data'] = json_encode($eventData);

            $url = "https://graph.facebook.com/".config('system.fbconversionapi.api_version').'/'.config('system.fbconversionapi.fixel_id').'/events';
            http_request_post($url,$ret,5,[],['basic'=>true]);
        }


    }

    /**
     * Notes：获取数据
     * {2023/11/13}
     * @param $data
     * @return array
     */
    protected static function getEventData($data)
    {
        $ret = [];

        $ret['event_id'] = isset($data['event_id']) ? $data['event_id'] : self::getEventId();
        $ret['event_name'] = isset($data['event_name']) ? $data['event_name'] : 'Custom Event';
        $ret['event_time'] = time();
        $ret['event_source_url'] = isset($data['event_source_url']) ? $data['event_source_url'] :self::getRequestUrl();
        $ret['action_source'] = isset($data['action_source']) ? $data['action_source'] : 'website';
        $ret['user_data'] = self::getUserData($data);

        $custom_data = self::getCustomData($data);
        if (!empty($custom_data)){
            $ret['custom_data'] = $custom_data;
        }
        return $ret;
    }

    /**
     * Notes：用户数据
     * {2023/11/13}
     * @param $data
     * @return array
     */
    protected static function getUserData($data)
    {
        $ret = [];
        $ret['client_ip_address'] = self::getIpAddress();
        $ret['client_user_agent'] = self::getHttpUserAgent();
        $ret['fbp'] = !empty(cookie('_fbp')) ? cookie('_fbp') : null;
        $ret['fbc'] = !empty(cookie('_fbc')) ? cookie('_fbc') : null;
        if (isset($data['email'])){
            $ret['em'] = hash("sha256", $data['email']);
        }
        if (isset($data['phone'])) {
            $ret['ph'] = hash("sha256", $data['phone']);
        }
        return $ret;
    }

    /**
     * Notes：自定义数据
     * {2023/11/13}
     * @param $data
     * @return array
     */
    protected static function getCustomData($data)
    {
        $ret = [];
        if (isset($data['currency'])){
            $ret['currency'] = $data['currency'];
        }
        if (isset($data['value'])){
            $ret['value'] = $data['value'];
        }
        if (isset($data['content_tpye'])){
            $ret['content_tpye'] = $data['content_tpye'];
        }
        if (isset($data['content_ids'])){
            $ret['content_ids'] = $data['content_ids'];
        }
        if (isset($data['content_name'])){
            $ret['content_name'] = $data['content_name'];
        }
        if (isset($data['content_category'])){
            $ret['content_category'] = $data['content_category'];
        }
        if (isset($data['search_string'])){
            $ret['search_string'] = $data['search_string'];
        }
        if (isset($data['num_items'])){
            $ret['num_items'] = $data['num_items'];
        }
        if (isset($data['contents'])){
            $ret['contents'] = $data['contents'];
        }
        if (isset($data['order_id'])){
            $ret['order_id'] = $data['order_id'];
        }

        return $ret;
    }

    /**
     * Notes：
     * {2023/11/13}
     * @return string
     */
    protected static function getRequestUrl()
    {
        $url = "http://";
        if (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') {
            $url = "https://";
        }

        if (!empty($_SERVER['HTTP_HOST'])) {
            $url .= $_SERVER['HTTP_HOST'];
        }

        if (!empty($_SERVER['REQUEST_URI'])) {
            $url .= $_SERVER['REQUEST_URI'];
        }
        return $url;
    }


    /**
     * Notes：获取eventid
     * {2023/11/13}
     * @return string
     */
    protected static function getEventId()
    {
        $data = openssl_random_pseudo_bytes(16);
        $data[6] = chr(ord($data[6]) & 0x0f | 0x40); // set version to 0100
        $data[8] = chr(ord($data[8]) & 0x3f | 0x80); // set bits 6-7 to 10
        return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
    }

    protected static function getHttpUserAgent()
    {
        $user_agent = null;
        if (!empty($_SERVER['HTTP_USER_AGENT'])) {
            $user_agent = $_SERVER['HTTP_USER_AGENT'];
        }
        return $user_agent;
    }

    protected static function getIpAddress()
    {
        $HEADERS_TO_SCAN = [
            'HTTP_CLIENT_IP',
            'HTTP_X_FORWARDED_FOR',
            'HTTP_X_FORWARDED',
            'HTTP_X_CLUSTER_CLIENT_IP',
            'HTTP_FORWARDED_FOR',
            'HTTP_FORWARDED',
            'REMOTE_ADDR'
        ];
        foreach ($HEADERS_TO_SCAN as $header) {
            if (array_key_exists($header, $_SERVER)) {
                $ipList = explode(',', $_SERVER[$header]);
                foreach ($ipList as $ip) {
                    $trimmedIp = trim($ip);
                    if (self::isValidIpAddress($trimmedIp)) {
                        return $trimmedIp;
                    }
                }
            }
        }
        return null;
    }

    protected static function isValidIpAddress($ipAddress)
    {
        return filter_var(
            $ipAddress,
            FILTER_VALIDATE_IP,
            FILTER_FLAG_IPV4
            | FILTER_FLAG_IPV6
            | FILTER_FLAG_NO_PRIV_RANGE
            | FILTER_FLAG_NO_RES_RANGE
        );
    }

}