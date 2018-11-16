<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006~2018 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------

use think\Route;

/*home前台*/
Route::rule('/','home/index/index','GET');  //首页路由注册
//前台-路由分组设置
Route::group('home',function(){
    //前台-sphinx检索数据
    Route::get('goods/indexs','home/goods/indexs');

    //前台-秒杀首页面
    Route::get('seckill/index','home/seckill/index');
    //前台-秒杀商品实施
    Route::get('seckill/qiang','home/seckill/qiang');

    //前台-购物车添加商品
    Route::post('shop/tianjia','home/shop/tianjia');
    //前台-购物车添加商品后的提示效果
    Route::get('shop/tianjia_show','home/shop/tianjia_show');
    //前台-购物车商品列表展示
    Route::get('shop/showgoods','home/shop/showgoods');
    //前台-购物车删除商品
    Route::post('shop/del','home/shop/del');
    //前台-购物车修改商品数量
    Route::post('shop/xiugainum','home/shop/xiugainum');
    //前台-购物车结算页面
    Route::get('shop/jiesuan','home/shop/jiesuan');
    //前台-购物车生成订单
    Route::post('shop/makeorder','home/shop/makeorder');
    //前台-购物车生成订单-支付
    Route::post('shop/payorder','home/shop/payorder');

    //前台-支付宝完成支付向商家发起get请求
    Route::get('shop/return_url','home/shop/return_url');
    //前台-支付宝完成支付向商家发起post请求
    Route::post('shop/notify_url','home/shop/notify_url');

    //前台-会员登录
    Route::any('user/login','home/user/login',['method'=>'get|post']);
    //前台-会员退出
    Route::get('user/logout','home/user/logout');
    //前台-会员注册
    Route::any('user/register','home/user/register',['method'=>'get|post']);
    //前台-激活会员帐号
    Route::get('user/active','home/user/active');
    //前台-测试发送短信
    Route::get('user/sms','home/user/sms');
    //前台-输入手机校验码请求路由
    Route::any('user/checktel','home/user/checktel',['method'=>'get|post']);
    //前台-QQ登录窗口展示
    Route::get('user/qqlogin','home/user/qqlogin');
    //前台-QQ登录后逻辑处理实现
    Route::get('user/qqcallback','home/user/qqcallback');

    Route::rule('goods/index','home/goods/index','GET');
    Route::rule('goods/detail','home/goods/detail','GET');
});



/*admin后台*/
//后台-分组设置
Route::group('admin',function(){
    //后台-管理员登录系统
    Route::any('manager/login','admin/manager/login',['method'=>'get|post']);
    //后台-管理员退出系统
    Route::get('manager/logout','admin/manager/logout');

    //子级分组
    Route::group('',function(){
        Route::rule('index/index','admin/index/index','GET');
        Route::rule('index/welcome','admin/index/welcome','GET');
    },['after_behavior'=>'\app\admin\behavior\CheckLogin']);

    Route::group('',function(){
        //后台-订单列表
        Route::get('order/index','admin/order/index');
        //后台-订单详情展示页面
        Route::get('order/detail','admin/order/detail');


        //后台-商品属性列表
        Route::rule('attribute/index','admin/attribute/index','GET');
        //后台-添加属性
        Route::any('attribute/tianjia','admin/attribute/tianjia', ['method'=>'get|post']);
        //后台-类型获取对应的属性列表
        Route::rule('attribute/showattribute','admin/attribute/showattribute','GET');

        //后台-商品类型列表
        Route::rule('type/index','admin/type/index','GET');

        //后台-添加商品类型
        Route::any('type/tianjia','admin/type/tianjia', ['method'=>'get|post']);

        //后台-商品列表
        Route::rule('goods/index','admin/goods/index','GET');
        //后台-添加商品
        Route::any('goods/tianjia','admin/goods/tianjia', ['method'=>'get|post']);
        //后台-修改商品
        Route::any('goods/xiugai','admin/goods/xiugai', ['method'=>'get|post']);
        //后台-删除商品
        Route::post('goods/shanchu','admin/goods/shanchu');

        //后台-商品logo图片上传
        Route::post('goods/logo_up','admin/goods/logo_up');
        //后台-商品pics相册图片上传
        Route::post('goods/pics_up','admin/goods/pics_up');
        //后台-商品pics相册删除
        Route::post('goods/pics_del','admin/goods/pics_del');

        //后台-商品做促销状态切换
        Route::post('goods/setpromotion','admin/goods/setpromotion');
        //后台-商品热卖数量更新
        Route::post('goods/setsalenum','admin/goods/setsalenum');

        //后台-秒杀商品列表展示
        Route::get('goods/index_seckill','admin/goods/index_seckill');

        //后台-添加商品表单中-类型获取对应的属性信息
        Route::rule('attribute/showattribute2','admin/attribute/showattribute2','GET');

        //后台-修改商品表单中-类型获取对应的属性信息
        Route::rule('attribute/showattribute3','admin/attribute/showattribute3','GET');

        //后台-角色列表
        Route::get('role/index','admin/role/index');
        //后台-修改角色
        Route::any('role/xiugai','admin/role/xiugai',['method'=>'get|post']);
        //后台-权限列表
        Route::get('permission/index','admin/permission/index');
        //后台-添加权限
        Route::any('permission/tianjia','admin/permission/tianjia',['method'=>'get|post']);

        //后台-分类列表“页面”
        Route::get('category/index','admin/category/index');
        //后台-获得分类列表数据
        Route::get('category/getcatinfo','admin/category/getcatinfo');
        //后台-删除分类
        Route::post('category/shanchu','admin/category/shanchu');
        //后台-修改分类
        Route::post('category/xiugai','admin/category/xiugai');


    },['after_behavior'=>['\app\admin\behavior\CheckLogin','\app\admin\behavior\CheckPermission']]);
});

























