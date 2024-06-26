<?php
namespace app\api\controller;
use app\BaseController;

class Index extends BaseController{
    public function index()
    {
        return $this->json(200);
        return sprintf('您好！这是一个[api]示例应用:<h1><pre>%s</pre></h1>', __METHOD__);
    }
}
