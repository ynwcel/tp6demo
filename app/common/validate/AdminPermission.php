<?php
declare (strict_types = 1);

namespace app\common\validate;

use think\Validate;

class AdminPermission extends Validate
{
    protected $rule = [
        'title|名称' => 'require',
        'type|类型' => 'require',
        'sort|排序' => 'require|between:1,99',
    ];

    protected $message = [];
}
