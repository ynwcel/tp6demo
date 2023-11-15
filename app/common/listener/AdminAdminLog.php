<?php
declare (strict_types = 1);

namespace app\common\listener;

/**
 * Notes：后台登录记录日志
 * {2023/11/15}
 * Class AdminAdminLog
 * @package app\common\listener
 */
class AdminAdminLog
{
    public function handle()
    {
        app('app\common\model\AdminAdminLog')->record();
    }
}