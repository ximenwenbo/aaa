<?php
namespace app\admin\controller;

use app\admin\model\Manager;
use app\admin\model\Permission;
use think\Controller;



class IndexController extends Controller
{
    /**
     * 后台系统默认请求页面
     * @return mixed
     */
    public function index()
    {
        /*根据当前登录系统管理员的角色，获得对应的操作权限*/
        $mg_id   = session('mg_id');
        $mg_name = session('mg_name');

        if($mg_name === 'admin'){
            //1) 超级管理员admin 获得“全部”的权限并展示(根据级别分别获取)
            $ps_infoA = Permission::where('ps_level','0')->select();
            $ps_infoB = Permission::where('ps_level','1')->select();
        }else{
            //2) 普通管理员根据角色获得对应权限
            //sp_manager 和 sp_role 做联表查询，获得角色拥有权限的ids信息

            //获得要显示的权限的ids信息
            $ps_ids = Manager::alias('m')
                -> join('__ROLE__ r','m.role_id=r.role_id')
                -> where('m.mg_id',$mg_id)
                -> value('r.role_ps_ids');
            //dump($ps_ids);  //  "102,104,108"

            //根据$ps_ids获得要显示的权限数据(根据级别分别获取)
            $ps_infoA = Permission::where('ps_id','in',$ps_ids)
                ->where('ps_level','0')->select();
            $ps_infoB = Permission::where('ps_id','in',$ps_ids)
                ->where('ps_level','1')->select();
            //dump($ps_infoA);  //数组对象集 返回第1级别权限
            //dump($ps_infoB);//数组对象集 返回第2级别权限
            //exit;
        }
        //把获得的权限数据传递给模板
        $this -> assign('ps_infoA',$ps_infoA);
        $this -> assign('ps_infoB',$ps_infoB);
        return $this -> fetch();
    }

    /**
     * 默认页面右下角的独立路由
     * @return mixed
     */
    public function welcome()
    {
        return $this -> fetch();
    }
}


















