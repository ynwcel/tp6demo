<?php

declare (strict_types=1);

namespace app\common\service\admin;

use think\exception\DbException;
use think\facade\Session;
use think\facade\Cookie;
use think\facade\Request;
use think\facade\Db;

use app\common\service\BaseService;
use app\common\model\Admin\AdminAdmin as AdminAdminModel;
use app\common\validate\AdminAdmin as AdminAdminValidate;

/**
 * Notes：后台管理员服务
 * {2023/11/14}
 * Class AdminAdminService
 * @package app\common\service\admin
 */
class AdminAdminService extends BaseService
{
    /**
     * Notes：管理员添加
     * {2023/11/15}
     * @param $data
     * @return array
     */
    public static function goAdd($data)
    {
        //验证
        $validate = new AdminAdminValidate;
        if(!$validate->scene('add')->check($data))
            return ['msg'=>$validate->getError(),'code'=>201];
        try {
            $password =  set_password($data['password']);
            AdminAdminModel::create(array_merge($data, [
                'password' => $password,
            ]));
            return ['msg'=>'success','code'=>200];
        }catch (\Exception $e){
            return ['msg'=>'操作失败'.$e->getMessage(),'code'=>201];
        }
    }

    /**
     * Notes：管理员编辑
     * {2023/11/15}
     * @param $data
     * @param $id
     * @return array
     */
    public static function goEdit($data,$id)
    {
        $data['id'] = $id;
        //验证
        $validate = new AdminAdminValidate;
        if(!$validate->scene('edit')->check($data))
            return ['msg'=>$validate->getError(),'code'=>201];
        try {
            $model = AdminAdminModel::find($id);
            //是否需要修改密码
            if ($data['password']){
                $model->password = set_password($data['password']);
                $model->token = null;
            }
            $model->username = $data['username'];
            $model->nickname = $data['nickname'];
            $model->save();
            rm();
            return ['msg'=>'success','code'=>200];
        }catch (\Exception $e){
            return ['msg'=>'操作失败'.$e->getMessage(),'code'=>201];
        }
    }

    /**
     * Notes：状态
     * {2023/11/15}
     * @param $data
     * @param $id
     * @return array
     */
    public static function goStatus($data,$id)
    {
        $model =  AdminAdminModel::find($id);
        if ($model->isEmpty())  return ['msg'=>'数据不存在','code'=>201];
        try{
            $model->save([
                'status' => $data,
                'token' => null
            ]);
            rm();
            return ['msg'=>'success','code'=>200];
        }catch (\Exception $e){
            return ['msg'=>'操作失败'.$e->getMessage(),'code'=>201];
        }
    }

    /**
     * Notes：管理员删除
     * {2023/11/15}
     * @param $id
     * @return array
     */
    public static function goRemove($id)
    {
        $model = AdminAdminModel::find($id);
        if ($model->isEmpty()) return ['msg'=>'数据不存在','code'=>201];
        try{
            $model->delete();
            Db::name('admin_admin_role')->where('admin_id', $id)->delete();
            Db::name('admin_admin_permission')->where('admin_id', $id)->delete();
            rm();
            return ['msg'=>'success','code'=>200];
        }catch (\Exception $e){
            return ['msg'=>'操作失败'.$e->getMessage(),'code'=>201];
        }
    }

    /**
     * Notes：管理员批量删除
     * {2023/11/15}
     * @param $id
     * @return array
     */
    public static function goBatchRemove($ids)
    {
        if (!is_array($ids)) return ['msg'=>'数据不存在','code'=>201];
        try{
            AdminAdminModel::destroy($ids);
            Db::name('admin_admin_role')->whereIn('admin_id', $ids)->delete();
            Db::name('admin_admin_permission')->whereIn('admin_id', $ids)->delete();
            rm();
            return ['msg'=>'success','code'=>200];
        }catch (\Exception $e){
            return ['msg'=>'操作失败'.$e->getMessage(),'code'=>201];
        }
    }

    /**
     * Notes：用户分配角色
     * {2023/11/15}
     * @param $data
     * @param $id
     * @return array
     * @throws \think\exception\PDOException
     */
    public static function goRole($data,$id)
    {
        if($data){
            Db::startTrans();
            try{
                //清除原先的角色
                Db::name('admin_admin_role')->where('admin_id',$id)->delete();
                //添加新的角色
                foreach ($data as $v){
                    Db::name('admin_admin_role')->insert([
                        'admin_id' => $id,
                        'role_id' => $v,
                    ]);
                }
                Db::commit();
                rm();
                return ['msg'=>'success','code'=>200];
            }catch (\Exception $e){
                Db::rollback();
                return ['msg'=>'操作失败'.$e->getMessage(),'code'=>201];
            }
        } else {
            return ['msg'=>'参数错误','code'=>201];
        }
    }

    /**
     * Notes：管理员分配直接权限
     * {2023/11/15}
     * @param $data
     * @param $id
     * @return array
     * @throws \think\db\exception\DbException
     * @throws \think\exception\PDOException
     */
    public static function goPermission($data,$id)
    {
        if($data){
            Db::startTrans();
            try{
                //清除原有的直接权限
                Db::name('admin_admin_permission')->where('admin_id',$id)->delete();
                //填充新的直接权限
                foreach ($data as $v){
                    Db::name('admin_admin_permission')->insert([
                        'admin_id' => $id,
                        'permission_id' => $v,
                    ]);
                }
                Db::commit();
                return ['msg'=>'success','code'=>200];
            }catch (DbException $e){
                Db::rollback();
                return ['msg'=>'操作失败'.$e->getMessage(),'code'=>201];
            }
        } else {
            return ['msg'=>'参数错误','code'=>201];
        }
    }

    /**
     * Notes：获取列表
     * {2023/11/15}
     * @return array|string[]
     * @throws \think\db\exception\DbException
     * @throws \think\exception\DbException
     */
    public static function goRecycle()
    {
        if (Request::isPost()){
            $ids = Request::param('ids');
            if (!is_array($ids)) return ['msg'=>'参数错误','code'=>'201'];
            try{
                if(Request::param('type')){
                    $data = AdminAdminModel::onlyTrashed()->whereIn('id', $ids)->select();
                    foreach($data as $k){
                        $k->restore();
                    }
                }else{
                    AdminAdminModel::destroy($ids,true);
                }
            }catch (\Exception $e){
                return ['msg'=>'操作失败'.$e->getMessage(),'code'=>201];
            }
            return ['msg'=>'操作成功'];
        }
        //按用户名
        $where = [];
        $limit = input('get.limit');
        if ($search = input('get.username')) {
            $where[] = ['username', 'like', "%" . $search . "%"];
        }
        $list = AdminAdminModel::onlyTrashed()->order('id','desc')->withoutField('password,token')->where($where)->paginate($limit);
        return ['code'=>0,'data'=>$list->items(),'extend'=>['count' => $list->total(), 'limit' => $limit]];
    }

    /**
     * Notes：修改密码
     * {2023/11/15}
     * @return array
     * @throws \think\Exception
     * @throws \think\db\exception\DbException
     * @throws \think\exception\PDOException
     */
    public static function goPass()
    {
        $data = Request::post();
        $validate = new AdminAdminValidate;
        if(!$validate->scene('pass')->check($data)){
            return ['msg'=>$validate->getError(),'code'=>201];
        }
        AdminAdminModel::where('id',Session::get('admin.id'))
                    ->update(['password' => set_password(trim($data['password']))]);

        return self::logout();
    }

    /**
     * Notes：用户登录
     * {2023/11/15}
     * @param array $data
     * @return array|string[]
     */
    public static function login(array $data)
    {
        $validate = new AdminAdminValidate;
        if(!$validate->scene('login')->check($data)){
            return ['msg'=>$validate->getError(),'code'=>201];
        }

        //验证用户
        $where = [
            'username' => trim($data['username']),
            'password' => set_password(trim($data['password'])),
            'status' => 1
        ];
        $admin = AdminAdminModel::where($where)->find();
        if(!$admin) {
            return ['msg'=>'用户名密码错误','code'=>201];
        }
        $admin->token = rand_string().$admin->id.microtime(true);
        $admin->save();

        //是否记住密码
        $time = 3600;
        if (isset($data['remember'])) {
            $time = 30 * 86400;
        }

        //缓存登录信息
        $info = [
            'id' => $admin->id,
            'token' => $admin->token,
            'username' => $admin->username,
            'role_id' => Db::name('admin_admin_role')->where('admin_id','=',$admin->id)->value('role_id'),
            'menu' => AdminAdminModel::permissions($admin->id,Request::root())
        ];
        Session::set('admin', $info);
        Cookie::set('token',$admin->token, $time);
        // 触发登录成功事件
        event('AdminLog');
        return ['msg'=>'登录成功','code'=>200];
    }

    /**
     * Notes：判断是否登录
     * {2023/11/15}
     * @return bool|void
     * @throws DbException
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public static function isLogin()
    {
        if(Session::get('admin')) {
            return true;
        }
        if(Cookie::has('token')){
            $admin = AdminAdminModel::where(['token'=>Cookie::get('token'),'status'=>1])->find();
            if(!$admin) {
                return false;
            }

            Session::set('admin',[
                'id' => $admin->id,
                'token' => $admin->token,
                'menu' => AdminAdminModel::permissions($admin->id,Request::root())
            ]);
            return true;
        }
        return false;
    }

    /**
     * Notes：退出登录
     * {2023/11/15}
     * @return array
     */
    public static function logout()
    {
        Session::delete('admin');
        Cookie::delete('token');
        Cookie::delete('sign');
        return ['msg'=>'退出成功','code'=>200];
    }

}