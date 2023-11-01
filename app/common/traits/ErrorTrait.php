<?php
namespace app\common\traits;

/**
 * 错误信息Trait类
 */
trait ErrorTrait
{
    /**
     * 错误信息
     * @var string
     */
    protected $error = '';

    /**
     * 设置错误信息
     * @param string $error
     * @return bool
     */
    protected function setError(string $error)
    {
        $this->error = $error ?: 'unknow error';
        return false;
    }

    /**
     * 获取错误信息
     * @return string
     */
    public function getError()
    {
        return $this->error;
    }

    /**
     * 是否存在错误信息
     * @return bool
     */
    public function hasError()
    {
        return !empty($this->error);
    }
}