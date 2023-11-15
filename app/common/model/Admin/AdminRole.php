<?php
declare (strict_types=1);

namespace app\common\model\Admin;

use think\Model;
use think\model\concern\SoftDelete;

/**
 * Notes：后台管理员角色
 * {2023/11/14}
 * Class AdminRole
 * @package app\common\model\Admin
 */
class AdminRole extends Model
{
    use SoftDelete;

    /**
     * Notes：角色所有的权限
     * {2023/11/14}
     * @return \think\model\relation\BelongsToMany
     */
    public function permissions()
    {
        return $this->belongsToMany('AdminPermission','admin_role_permission','permission_id','role_id');
    }

    /**
     * Notes：获取用户直接权限
     * {2023/11/14}
     * @param $id
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public static function getPermission($id)
    {
        $role = self::with('permissions')->find($id);
        $permissions = AdminPermission::order('sort','asc')->select();
        foreach ($permissions as $permission){
            foreach ($role->permissions as $v){
                if ($permission->id == $v['id']){
                    $permission->own = true;
                }
            }
        }
        $permissions = get_tree($permissions->toArray());
        return ['role'=>$role,'permissions'=>$permissions];
    }

    /**
     * Notes：列表信息
     * {2023/11/14}
     * @return array
     * @throws \think\db\exception\DbException
     * @throws \think\exception\DbException
     */
    public static function getList()
    {
        $limit = input('get.limit');
        $list = self::order('id','desc')->paginate($limit);
        return ['code'=>0,'data'=>$list->items(),'extend'=>['count' => $list->total(), 'limit' => $limit]];
    }
}