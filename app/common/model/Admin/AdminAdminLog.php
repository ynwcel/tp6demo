<?php
declare (strict_types = 1);

namespace app\common\model\Admin;

use think\facade\Request;
use think\facade\Session;
use think\Model;

/**
 * Notes：后台管理记录
 * {2023/11/14}
 * Class AdminAdminLog
 * @package app\common\model
 */
class AdminAdminLog extends Model
{
    /**
     * Notes：
     * {2023/11/14}
     * @return \think\model\relation\BelongsTo
     */
    public function log()
    {
        return $this->belongsTo('AdminAdmin','uid','id');
    }

    /**
     * Notes：管理员日志
     * {2023/11/14}
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public static function record()
    {
        $desc = Request::except(['s','_pjax'])??'';
        if(isset($desc['page'])&&isset($desc['limit']))return;
        foreach ($desc as $k => $v) {
            if(stripos($k, 'fresh') !== false) return;
            if (is_string($v) && strlen($v) > 255 || stripos($k, 'password') !== false)  {
                unset($desc[$k]);
            }
        }
        $info = [
            'uid' => Session::get('admin.id'),
            'url' => Request::url(),
            'desc' => json_encode($desc),
            'ip' => Request::ip(),
            'user_agent' => Request::server('HTTP_USER_AGENT')
        ];

        $res = self::where('uid',$info['uid'])->order('id', 'desc')->find();
        if (isset($res['url'])!==$info['url']) {
            self::create($info);
        }
    }

}