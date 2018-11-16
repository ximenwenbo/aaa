<?php
/**
 * Created by PhpStorm.
 * User: ssh
 * Date: 2018/7/24
 * Time: 15:15
 */

return [
    'auth'=>[
        'qqconnect' => [
            //设置 创建应用 的id和key
            'appid' => '101492459',
            'appkey' => '4e3b65c635ce6de1ee2055cc0a6d847e',
            //qq登录回调地址
            'callback' => 'http://www.php68.com/home/user/qqcallback',
            'scope' => 'get_user_info',
            'errorReport' => true
        ],
    ],
];

