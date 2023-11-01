<?php
declare (strict_types = 1);

namespace app\wap\controller;

class Index
{
    public function index()
    {
        return '您好！这是一个[wap]示例应用';
    }

    public function hello()
    {

        return 'waphello';
    }
}
