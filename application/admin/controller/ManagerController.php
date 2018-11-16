<?php

namespace app\admin\controller;

use app\admin\model\Manager;
use think\Controller;
use think\Request;


class ManagerController extends Controller {

    /**
     * 登录系统
     * 两种请求类型：get/post
     */
    public function login(Request $request)
    {
        if($request -> isPost()){
            //判断验证码
            $code = $request -> post('verify_code');
            if(captcha_check($code)){
                //获得帐号信息
                $name = $request->post('mg_name');
                $pwd  = md5($request->post('mg_pwd'));

                //校验帐号信息
                $exists = Manager::where(['mg_name'=>$name,'mg_pwd'=>$pwd])->find(); //obj || null
                if($exists){
                    //持久化信息
                    session('mg_id',$exists->mg_id);
                    session('mg_name',$exists->mg_name);
                    //跳转到后台首页面
                    //$this -> redirect('[分组/][控制器/]操作方法');
                    $this -> redirect('index/index');
                }else{
                    //用户名密码不存在的错误提示，传递给模板显示
                    $this -> assign('errorinfo','用户名或密码错误');
                }
            }else{
                $this -> assign('errorinfo','验证码错误');
            }
        }
        //没有成功就展示登录的表单页面
        return $this -> fetch();
    }

    /*
     * 管理员退出系统
     */
    public function logout()
    {
        //清除session
        session(null);
        //跳转到登录页面
        $this -> redirect('login');
    }
}


















