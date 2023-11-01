<?php
declare (strict_types=1);

namespace app\common\service;

use app\common\traits\ErrorTrait;
use think\facade\Request;

/**
 * Notes：系统服务基础类
 * {2023/11/1}
 * Class BaseService
 * @package app\common\service
 */
class BaseService
{
    use ErrorTrait;

    // 请求管理类
    /* @var $request \think\Request */
    protected $request;

    // 当前访问的商城ID
    protected $storeId;

    /**
     * 构造方法
     * BaseService constructor.
     */
    public function __construct()
    {
        // 请求管理类
        $this->request = Request::instance();
        // 获取当前操作的商城ID
        $this->getStoreId();
        // 执行子类的构造方法
        $this->initialize();
    }

    /**
     * 构造方法 (供继承的子类使用)
     */
    protected function initialize()
    {
    }

    /**
     * 获取当前操作的商城ID
     * @return int|null
     */
    protected function getStoreId()
    {
        if (empty($this->storeId) && in_array(app_name(), ['store', 'api'])) {
            $this->storeId = getStoreId();
        }
        return $this->storeId;
    }
}