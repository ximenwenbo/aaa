<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 流年 <liu21st@gmail.com>
// +----------------------------------------------------------------------

// 应用公共函数库文件
use app\admin\model\Category;


/**
 * 给手机发送校验码短信
 * @param $tel
 * @param $code
 * @param $time
 */
function send_sms($tel,$code,$time){
    //配置密钥
    $config = [
        'accessKeyId'    => 'LTAIoNi869qUGqUr',
        'accessKeySecret' => 'SZFxPSmjymwGeXb2H0jrcSIN4sGC0x',
    ];

    $client  = new \Flc\Dysms\Client($config);
    $sendSms = new \Flc\Dysms\Request\SendSms;
    $sendSms->setPhoneNumbers($tel);  //接收短信手机号码
    $sendSms->setSignName('万马奔腾');  //设置签名
    $sendSms->setTemplateCode('SMS_135802968');  //模板设置
    //设置模板变量
    $sendSms->setTemplateParam(['code' => $code,'time'=>$time]);
    //设置发送短息序号的
    $sendSms->setOutId('demo'.time());

    return $client->execute($sendSms); //发送短信，返回一个对象
}

/**
 * 发送邮件
 * @param $to：接收者
 * @param $title：邮件标题
 * @param $cont：邮件内容
 */
function send_mail($to,$title,$cont){
    $mailer = \mailer\tp5\Mailer::instance();
    //\vendor\yuan1994\tp-mailer\src\mailer\tp5\Mailer.php
    $mailer->to($to)
        ->subject($title)
        ->html($cont)
        ->send();
}

/**
 * 获取redis对象
 * @return Redis
 */
function get_redis_obj($db=9){
    $redis = new \Redis();  //通过“\”公共空间方式使用Redis类
    $redis -> connect('192.168.139.200',6379);
    $redis -> auth('football');
    $redis -> select($db);
    return $redis;
}


/*
 * 根据$cat_id获得对应的第1/2/3级分类的id信息
 * 返回的信息是[1级别，2级别，3级别]顺序的数组
 * 例如:
 * id：177[3级别]  169[2级别]  129[1级别]
 * $cat_id是1级别： [129]
 * $cat_id是2级别： [129,169]
 * $cat_id是3级别： [129,169,177]
 */
function get_cat_parent_ids($cat_id){
    $result = []; //用于后期接收所有"上级和本身"的id信息

    //获得$cat_id对应的记录信息
    $category = Category::find($cat_id);
    while($category){
        $result[] = $category->cat_id;

        //继续获得上级分类对象信息
        $category = Category::find($category->cat_pid);
    }
    //把$result的各个元素顺序做反方向调换
    return array_reverse($result);
}

