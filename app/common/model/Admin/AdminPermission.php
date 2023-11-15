<?php
declare (strict_types = 1);

namespace app\common\model\Admin;

use think\Model;

class AdminPermission extends Model
{
    /**
     * Notes：列表信息
     * {2023/11/14}
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public static function getList()
    {
        $list = self::order('id','desc')->select();
        return ['code'=>0,'data'=>$list->toArray(),'extend'=>['count' => $list->count()]];
    }

    /**
     * Notes：获取一条信息
     * {2023/11/14}
     * @param $id
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public static function getFind($id)
    {
        return [
            'model' => self::find($id),
            'permissions' => get_tree((self::order('sort','asc'))->select()->toArray())
        ];
    }

    /**
     * Notes：子权限
     * {2023/11/14}
     * @return \think\model\relation\HasMany
     */
    public function child()
    {
        return $this->hasMany('AdminPermission','pid','id');
    }
}