<?php
/**
 * Created by PhpStorm.
 * User: ssh
 * Date: 2018/7/24
 * Time: 11:08
 */

namespace app\admin\behavior;
//use traits\controller\Jump;

class CheckLogin
{
//    use Jump;

    public function run(&$params)
    {
        //获得管理员 持久化信息
        $mg_id = session('mg_id');

        if(!$mg_id){//判断管理员 不处于登录状态 就跳转到登录页面
            //跳转到登录页面
            //$this -> redirect('admin/Manager/login'); //右下角 iframe跳转
            echo <<<eof
                <script type="text/javascript">
                    //top:会使得浏览器全部(非iframe)页面都跳转
                    window.top.location.href='/admin/manager/login';
                </script>
eof;
            exit;

        }
    }
}

