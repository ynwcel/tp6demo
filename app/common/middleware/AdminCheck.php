<?php
declare (strict_types = 1);

namespace app\common\middleware;

use app\common\service\admin\AdminAdminService;
use app\common\model\Admin\AdminAdminLog;

/**
 * Notes：后台登录检测
 * {2023/11/14}
 * Class AdminCheck
 * @package app\common\middleware
 */
class AdminCheck
{
    public function handle($request, \Closure $next)
    {
        try {
            if(AdminAdminService::isLogin() == false){
                return redirect($request->root().'/login/index');
            }
            //登录日志记录
            AdminAdminLog::record();

        } catch (\Exception $e){

        }
        return $next($request);

    }

}