<?php
namespace app\home\behavior;

use traits\controller\Jump;

class CheckLogin
{
    use Jump;

    public function run(&$params)
    {
        //$params接收Hook::listen(名称，参数) 第二个参数信息
        //判断用户没有登录系统就跳转到登录页去
        if(!session('?user_name')){
            //定义登录后的回调地址
            session('back_url',$params);
            //跳转
            $this -> redirect('user/login');
        }
    }
}


