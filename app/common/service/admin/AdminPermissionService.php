<?php

declare (strict_types=1);

namespace app\common\service\admin;

use think\facade\Session;
use think\facade\Request;
use think\facade\Db;

use app\common\service\BaseService;
use app\common\model\Admin\AdminPermission as AdminPermissionM;
use app\common\validate\AdminPermission as AdminPermissionV;

/**
 * Notes：后台权限管理类
 * {2023/11/14}
 * Class AdminPermissionService
 * @package app\common\service\admin
 */
class AdminPermissionService extends BaseService
{
    /**
     * Notes：添加权限
     * {2023/11/15}
     * @param $data
     * @return array
     */
    public static function goAdd($data)
    {
        $validate = new AdminPermissionV;
        if (!$validate->check($data)){
            return ['msg'=>$validate->getError(),'code'=>201];
        }

        try {
            AdminPermissionM::create($data);
            rm();
            Session::clear();
            return ['msg'=>'success','code'=>200];
        } catch (\Exception $e){
            return ['msg'=>'操作失败'.$e->getMessage(),'code'=>201];
        }
    }

    /**
     * Notes：修改权限
     * {2023/11/15}
     * @param $data
     * @param $id
     * @return array
     */
    public static function goEdit($data,$id)
    {
        $data['id'] = $id;
        $validate = new AdminPermissionV;
        if (!$validate->check($data)){
            return ['msg'=>$validate->getError(),'code'=>201];
        }
        try {
            AdminPermissionM::update($data);
            rm();
            Session::clear();
            return ['msg'=>'success','code'=>200];
        }catch (\Exception $e){
            return ['msg'=>'操作失败'.$e->getMessage(),'code'=>201];
        }
    }

    /**
     * Notes：修改状态
     * {2023/11/15}
     * @param $data
     * @param $id
     * @return array
     */
    public static function goStatus($data,$id)
    {
        $model =  AdminPermissionM::find($id);
        if ($model->isEmpty()) {
            return ['msg'=>'数据不存在','code'=>201];
        }
        try{
            $model->save([
                'status' => $data,
            ]);
            rm();
            return ['msg'=>'success','code'=>200];
        }catch (\Exception $e){
            return ['msg'=>'操作失败'.$e->getMessage(),'code'=>201];
        }
    }

    /**
     * Notes：权限删除
     * {2023/11/15}
     * @param $id
     * @return array
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @throws \think\exception\PDOException
     */
    public static function goRemove($id)
    {
        $model = AdminPermissionM::with('child')->find($id);
        if(Request::param('type')){
            $arr = Db::name('admin_permission')->where('pid',$id)->field('id,pid')->select();
            foreach($arr as $k=>$v){
                Db::name('admin_permission')->where('pid',$v['id'])->delete();
                Db::name('admin_role_permission')->where('permission_id',$v['id'])->delete();
                Db::name('admin_admin_permission')->where('permission_id',$v['id'])->delete();
            }
        }else{
            if (isset($model->child) && !$model->child->isEmpty()){
                return ['msg'=>'存在子权限，确认删除后不可恢复','code'=>201];
            }
        }
        $model->delete();
        Db::name('admin_role_permission')->where('permission_id', $id)->delete();
        Db::name('admin_admin_permission')->where('permission_id', $id)->delete();
        rm();
        Session::clear();
        return ['msg'=>'success','code'=>200];
    }

    /**
     * Notes：创建菜单
     * {2023/11/15}
     * @param $data
     */
    public static function goMenu($data)
    {
        $path = '/'.$data['left'].'.'.$data['right'].'/';
        $data = [
            'pid' => $data['menu'],
            'title' => $data['ename'],
            'href' => $path.'index',
        ];
        $menu = AdminPermissionM::create(array_merge($data, [
            'icon'=>'layui-icon layui-icon-fire'
        ]));
        $crud = [
            'add' => "新增",
            'edit' => "修改",
            'remove' => "删除",
            'batchRemove' => "批量删除",
            'recycle' => "回收站"
        ];
        $data['pid'] = $menu['id'];
        foreach ($crud as $k=>$v) {
            $data['title'] = $v.$menu['title'];
            $data['href'] = $path.$k;
            AdminPermissionM::create($data);
        }
        return ['msg'=>'success','code'=>200];

    }


}
