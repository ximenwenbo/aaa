<?php

namespace app\admin\controller;


use app\admin\model\Permission;
use think\Controller;
use think\Request;
use think\Validate;


class PermissionController extends Controller {

    /**
     * 权限列表展示
     * @param Request $request
     * @return mixed
     */
    public function index(Request $request)
    {
        //$obj = Permission::find(103);
        //dump($obj -> toArray());
        //exit;

        //获得全部的权限传递给模板
        $ps_infos = Permission::select(); //返回“数组对象集”

        //把获得的权限数据组织为“父子”关系
        $infos = [];
        foreach($ps_infos as $v){
            $infos[] = $v->toArray();
        }
        //dump($infos); //纯粹的二维数组

        $infos_fuson = generateTree($infos);
        //dump($infos_fuson);//父子关系数据获取到了

        $this -> assign('ps_infos',$infos_fuson);

        return $this -> fetch();
    }

    /**
     * 添加权限
     * @param Request $request
     * @return mixed
     */
    public function tianjia(Request $request)
    {
        if($request -> isPost()){
            //dump($request->post());  //收集到4个可以直接添加进权限表的表单域信息，但是还差一个ps_level

            //数据校验
            $rules = [
                'ps_name' => 'require',
            ];
            $notices = [
                'ps_name.require' => '权限名称必填',
            ];
            $validate = new Validate($rules,$notices);
            $shuju = $request -> post();
            if($validate->batch()->check($shuju)){
                //现在需要计算ps_level(灵感来之父级权限ps_pid)
                //两种情况：
                if($shuju['ps_pid']==0){
                    //① 第1级别权限：ps_level = 0
                    $shuju['ps_level'] = '0';
                }else{
                    //② 子级权限：ps_level = 父level+1
                    $fu_level = Permission::where('ps_id',$shuju['ps_pid'])
                        ->value('ps_level');
                    $shuju['ps_level'] = (int)$fu_level+1;
                }

                $permission = new Permission();
                $rst = $permission->allowField(true)->save($shuju); //返回添加新记录条数
                if($rst){
                    return ['status'=>'success'];
                }else{
                    return ['status'=>'failure'];
                }
            }else{
                $errorinfo = implode(',',$validate -> getError());
                return ['status'=>'failure','errorinfo'=>$errorinfo];
            }
        }else{
            //把可供选取的父级权限获得出来
            $fu_infos = Permission::where('ps_level','<',2)->select();

            //制作权限为“上下级”关系
            $tmp = [];
            foreach($fu_infos as $v){
                $tmp[] = $v->toArray();
            }
            $infos = generateTree($tmp);

            $this -> assign('infos',$infos);

            return $this -> fetch();
        }
    }
}


















