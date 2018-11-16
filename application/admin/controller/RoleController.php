<?php

namespace app\admin\controller;


use app\admin\model\Permission;
use app\admin\model\Role;
use think\Controller;
use think\Request;
use think\Validate;


class RoleController extends Controller {
    /**
     * 角色展示
     * @param Request $request
     * @return mixed
     */
    public function index(Request $request)
    {
        //获得角色列表信息
        $infos = Role::select();
        $this -> assign('infos',$infos);

        return $this -> fetch();
    }

    /**
     * 修改角色[get/post]
     * @param Request $request[依赖注入对象]
     * @param Role $role:代表被修改的角色的模型对象[依赖注入对象]
     * @return mixed
     */
    public function xiugai(Request $request,Role $role)
    {
        if($request->isPost()){
            //dump($request->post());
            //对表单进行校验
            $rules = [
                'role_name' =>'require',
            ];
            $notices = [
                'role_name.require' => '角色名称必填',
            ];
            $fd = $request -> post(); //全部表单数据
            $validate = new Validate($rules,$notices);

            if($validate -> batch()->check($fd)){
                //把被修改的数据整合好
                $shuju['role_id']       = $request->post('role_id');
                $shuju['role_name']     = $request->post('role_name');
                //把收集的Array的权限信息变为String字符串格式
                $shuju['role_ps_ids']   = implode(',',$request->post('quanxian/a'));

                //dump($shuju); //['role_id'=>30,'role_ps_ids'=>'101,104,105']
                $ps_ca = Permission::where('ps_id','in',$shuju['role_ps_ids'])
                    ->where('ps_level','>',0)
                    ->column("concat(ps_c,'-',ps_a)");
                //dump($ps_ca); //['Goods-index','Brand-index']
                $shuju['role_ps_ca'] = implode(',',$ps_ca); // 'Goods-index','Brand-index'

                $rst = $role ->update($shuju);

                if($rst){
                    return ['status'=>'success'];
                }else{
                    return ['status'=>'failure'];
                }
            }else{
                //获得校验错误信息并返回给客户端
                $errorinfo = implode(',',$validate -> getError());
                return ['status'=>'failure','errorinfo'=>$errorinfo];
            }
        }else{
            //传递 依赖注入对象 到模板
            $this -> assign('role',$role);

            //把真实的各个级别权限获得出来，传递到板展示
            $ps_infoA = Permission::where('ps_level','0')->select();
            $ps_infoB = Permission::where('ps_level','1')->select();
            $ps_infoC = Permission::where('ps_level','2')->select();

            return $this -> fetch('',compact('ps_infoA','ps_infoB','ps_infoC'));
        }
    }
}


















