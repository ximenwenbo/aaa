<?php
/**
 * Created by PhpStorm.
 * User: ssh
 * Date: 2018/7/24
 * Time: 11:08
 */

namespace app\admin\behavior;

use app\admin\model\Manager;

class CheckPermission
{
    public function run(&$params)
    {
        //限制用户只访问自己拥有的权限
        $mg_id = session('mg_id');
        $mg_name = session('mg_name');

        if($mg_name!=='admin'){  //普通管理员才做权限限制操作
            //获得管理员请求的“控制器”和“方法”
            $path = request()->pathinfo();  //string(24) "admin/goods/tianjia.html"
            $path = rtrim($path,'.html'); //string(24) "admin/goods/tianjia"

            $path_arr = explode('/',$path); //['admin','goods','tianjia']
            $nowCA = $path_arr[1].'-'.$path_arr[2];
            //dump($nowCA);  //string(13) "goods-tianjia"

            //获得当前管理员角色拥有的可访问 “控制器-操作方法” 的信息串获得出来

            $role_ps_ca = Manager::alias('m')
                -> join('__ROLE__ r','m.role_id=r.role_id')
                -> where('mg_id',$mg_id)
                -> value('r.role_ps_ca');
            //dump($role_ps_ca); //“Goods-index,Order-index,Order-xiugai”

            $nowCA      = strtolower($nowCA);  //变为小写内容
            $role_ps_ca = strtolower($role_ps_ca);

            //判断$role_ps_ca的大串中是否有出现$nowCA的小串
            if(strpos($role_ps_ca,$nowCA)===false){
                exit('禁止越权访问!');
            }
        }
    }
}

