<?php
namespace app\eadmin\controller;
use app\BaseController;
class Index
{
    public function index()
    {
        return sprintf('您好！这是一个[eadmin]示例应用:<h1><pre>%s</pre></h1>', __METHOD__);
    }
}
