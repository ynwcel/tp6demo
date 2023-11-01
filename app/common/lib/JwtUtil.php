<?php
namespace app\common\lib;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class JwtUtil
{

    /**
     * 生成验签
     * @param $uid 用户id
     * @return mixed
     */
    public static function signToken($uid)
    {
        //自定义的一个随机字串用户于加密中常用的 盐  salt
        $key  = config('system.jwt_config.jwt_key');
        $token= [
            "iss"=>$key,        //签发者 可以为空
            "aud"=>'',          //面象的用户，可以为空
            "iat"=>time(),      //签发时间
            "nbf"=>time(),      //在什么时候jwt开始生效
            "exp"=> time()+30,  //token 过期时间
            "data"=>[           //记录的uid的信息
                'uid'=>$uid,
            ]
        ];

        $jwt = JWT::encode($token, $key, "HS256");  //生成了 token

        return $jwt;
    }

    /**
     * 验证token
     *
     * @Author WTTT
     * @DateTime 2022-10-26
     * @param [type] $token 需要验证的token
     * @return array|int[]
     */
    public static function checkToken($token)
    {
        $key = config('system.jwt_config.jwt_key');
        $res['status'] = false;

        try {
            //当前时间减去60，把时间留点余地
            JWT::$leeway    = config('system.jwt_config.jwt_leeway');
            $decoded        = JWT::decode($token, new Key($key, 'HS256')); //HS256方式，这里要和签发的时候对应
            $arr            = (array)$decoded;
            $res['status']  = 200;
            $res['data']    =(array)$arr['data'];
            return $res;
        } catch(\Firebase\JWT\SignatureInvalidException $e) { 
            //签名不正确
            $res['info']    = "签名不正确";
            return $res;
        } catch(\Firebase\JWT\BeforeValidException $e) { 
            // 签名在某个时间点之后才能用
            $res['info']    = "token失效";
            return $res;

        } catch(\Firebase\JWT\ExpiredException $e) { 
            // token过期
            $res['info']    = "token过期";
            return $res;
        } catch(\Exception $e) { 
            //其他错误
            $res['info']    = "未知错误";
            return $res;
        }
    }
}