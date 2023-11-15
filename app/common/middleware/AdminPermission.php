<?php

declare (strict_types=1);

namespace app\common\middleware;

use think\facade\Session;
use think\Response;

/**
 * Notes：y验证权限
 * {2023/11/15}
 * Class AdminPermission
 * @package app\common\middleware
 */
class AdminPermission
{
    use \app\common\traits\Base;

    /**
     * Notes：处理请求
     * {2023/11/15}
     * @param $request
     * @param \Closure $next
     * @return mixed|object
     */
    public function handle($request, \Closure $next)
    {
        //超级管理员不需要验证
        if (Session::get('admin.id') == 1) {
            return $next($request);
        }
        //验证权限
        $url = $request->root(). '/'.$request->controller(true).'/'.$request->action(true);
        $href = array_column(Session::get('admin.menu'), 'href');
        if (!in_array($url, $href)) {
            return $request->isAjax()? $this->json('权限不足',999):$this->error('权限不足','');
        }
        return $next($request);
    }
}