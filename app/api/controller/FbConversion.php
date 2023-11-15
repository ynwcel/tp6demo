<?php
declare (strict_types=1);

namespace app\api\controller;

use think\facade\Request;
use app\common\exception\BaseException;
use app\common\service\FbConversionsApiService;


class FbConversion extends ApiBase
{

    public function pageView()
    {
        $request = Request::param();

    }

}