<?php
namespace app\index\controller;
use app\BaseController;

class Index
{
//    protected $middleware = ['DeviceDetection'];

    public function index()
    {
        return sprintf('您好！这是一个[index]示例应用:<h1><pre>%s</pre></h1>', __METHOD__);
    }
}
