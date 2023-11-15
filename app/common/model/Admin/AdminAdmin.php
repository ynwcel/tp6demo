<?php
declare (strict_types=1);

namespace app\common\model\Admin;

use think\Model;
use think\model\concern\SoftDelete;
use think\facade\Session;

/**
 * Notes：后台管理员模型
 * {2023/11/14}
 * Class AdminAdmin
 * @package app\common\model\Admin
 */
class AdminAdmin extends Model
{
    use SoftDelete;

    /**
     * Notes：管理拥有的角色
     * {2023/11/14}
     * @return \think\model\relation\BelongsToMany
     */
    public function roles()
    {
        return $this->belongsToMany('AdminRole', 'admin_admin_role', 'role_id', 'admin_id');
    }

    /**
     * Notes：管理的直接权限
     * {2023/11/14}
     * @return \think\model\relation\BelongsToMany
     */
    public function directPermissions()
    {
        return $this->belongsToMany('AdminPermission', 'admin_admin_permission', 'permission_id', 'admin_id');
    }

    /**
     * Notes：获取管理拥有的角色
     * {2023/11/14}
     * @param $id
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public static function getRole($id)
    {
        $admin = self::with('roles')->where('id',$id)->find();
        $roles = AdminRole::select();
        foreach ($roles as $k=>$role){
            if (isset($admin->roles) && !$admin->roles->isEmpty()){
                foreach ($admin->roles as $v){
                    if ($role['id']==$v['id']){
                        $roles[$k]['own'] = true;
                    }
                }
            }
        }
        return ['admin'=>$admin,'roles'=>$roles];
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
        $admin = self::with('directPermissions')->find($id);
        $permissions = AdminPermission::order('sort','asc')->select();
        foreach ($permissions as $permission){
            foreach ($admin->direct_permissions as $v){
                if ($permission->id == $v['id']){
                    $permission->own = true;
                }
            }
        }
        $permissions = get_tree($permissions->toArray());
        return ['admin'=>$admin,'permissions'=>$permissions];
    }

    /**
     * Notes：用户的所有权限
     * {2023/11/14}
     * @param $id
     * @param $root
     * @return array
     */
    public static function permissions($id,$root)
    {
        $permissions = [];
        $admin = self::with(['roles.permissions', 'directPermissions'])->findOrEmpty($id)->toArray();
        //超级管理员缓存所有权限
        if ($admin['id'] == 1){
            $perms = AdminPermission::order('sort','asc')->select()->toArray();
            foreach ($perms as $p){
                if($p['status'] == 1){
                    $permissions[$p['id']] =  $p;
                    $permissions[$p['id']]['href'] = is_url($p['href'])??$root.$p['href'];
                }
            }
            if(env('APP_DEBUG')==true){
                $permissions[0] = [
                    "id" => -1,
                    "pid" => 0,
                    "title" => "自动生成",
                    "icon" => "layui-icon layui-icon-util",
                    "type" => 0,
                    "href" => "",
                ];
                $permissions[-1] = [
                    "id" => -2,
                    "pid" => -1,
                    "title" => "CRUD管理",
                    "icon" => "layui-icon layui-icon-console",
                    "type" => 1,
                    "openType" => "_iframe",
                    'href'=> $root."/crud/index",
                ];
            }
        } else {
            //处理角色权限
            if (isset($admin['roles']) && !empty($admin['roles'])) {
                foreach ($admin['roles'] as $r) {
                    if (isset($r['permissions']) && !empty($r['permissions'])) {
                        foreach ($r['permissions'] as $p) {
                            if($p['status'] == 1){
                                $permissions[$p['id']] =  $p;
                                $permissions[$p['id']]['href'] = is_url($p['href'])??$root.$p['href'];
                            }
                        }
                    }
                }
            }

            //处理直接权限
            if (isset($admin['directPermissions']) && !empty($admin['directPermissions'])) {
                foreach ($admin['directPermissions'] as $p) {
                    if($p['status'] == 1){
                        $permissions[$p['id']] =  $p;
                        $permissions[$p['id']]['href'] = is_url($p['href'])??$root.$p['href'];
                    }
                }
            }
            $key = array_column($permissions, 'sort');
            array_multisort($key,SORT_ASC,$permissions);
        }
        return $permissions;
    }

    /**
     * Notes：获取后台用户
     * {2023/11/14}
     * @return array|mixed|object|\T|\think\App
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public static function getAdminInfo()
    {
        $key = config('system.admin_cache_key').'admin_admin_info';
        if(cache($key)){
            $ret = cache($key);
        } else {
            $ret = [];
            $data = self::field('id,username')->select()->toArray();
            if(!empty($data)){
                $ret = array_column($data,'username','id');
                cache($key,$ret,86400*7);
            }
        }
        return $ret;
    }

    /**
     * Notes：获取列表
     * {2023/11/14}
     * @return array
     * @throws \think\db\exception\DbException
     * @throws \think\exception\DbException
     */
    public static function getList()
    {
        $where = [];
        $limit = input('get.limit');
        if ($search = input('get.username')) {
            $where[] = ['username', 'like', "%" . $search . "%"];
        }

        $adminId = session('admin.id');

        $list = self::order('id','desc')
            ->where('id','<>',$adminId)
            ->where('id','>','1')
            ->withoutField('password,token,delete_time')
            ->where($where)
            ->paginate($limit);
        return ['code'=>0,'data'=>$list->items(),'extend'=>['count' => $list->total(), 'limit' => $limit]];
    }



}