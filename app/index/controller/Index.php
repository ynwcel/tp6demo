<?php
declare (strict_types = 1);

namespace app\index\controller;

use app\common\lib\JwtUtil;

/**
 * Class Index
 * @package app\index\controller
 */
class Index
{
//    protected $middleware = ['DeviceDetection'];

    public function index()
    {
        //return JwtUtil::signToken(1);
        
        $token = "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiIxMTEiLCJhdWQiOiIiLCJpYXQiOjE2NjY3NTQ4NDUsIm5iZiI6MTY2Njc1NDg0NSwiZXhwIjoxNjY2NzU0ODc1LCJkYXRhIjp7InVpZCI6MX19._hNklCYf3mG-VqF5d4cwJg5WGX6EEm_8fd4M9Wkcc4o";
        $ret = json_encode(JwtUtil::checkToken($token));
        return $ret;
    }


    public function test(){
        $token = "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiIxMTEiLCJhdWQiOiIiLCJpYXQiOjE2NjY3NTQ2ODcsIm5iZiI6MTY2Njc1NDY4NywiZXhwIjoxNjY2NzU0NzE3LCJkYXRhIjp7InVpZCI6MX19.1gQ3NRKD3vzFUPvL6YXG0gHutLUM3M0Qo8DosQ5P-t4";
        $ret = json_encode(JwtUtil::checkToken($token));
        return $ret;
    }

    public function hello()
    {
        return 'index';
    }
}
