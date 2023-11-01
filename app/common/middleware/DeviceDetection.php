<?php
namespace app\common\middleware;

use Closure;
use think\facade\Request;

/**
 * Notes：设备检测
 * {2023/11/1}
 * Class DeviceDetection
 * @package app\common\middleware
 */
class DeviceDetection
{
    public function handle($request, Closure $next)
    {
        // 根据 User Agent 检测设备类型
        if (Request::isMobile()) {
            // 如果是移动设备，重定向到移动应用
            app('app')->name('wap');
            exit();
        }
        return $next($request);

    }

}
