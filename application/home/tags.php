<?php
/**
 * 通过“标签”名称把具体行为给定义好
 * 后期在控制器应用处 就可以通过调用“标签”而触发执行行为
 */
return [
    //标签名称=>[行为]
    //标签名称=>[行为,行为,行为]
//    'app_init'=> [
//        'app\\index\\behavior\\CheckAuth',
//        'app\\index\\behavior\\CheckLang'
//    ],
//    'app_end'=> [
//        'app\\admin\\behavior\\CronRun'
//    ]
    'denglu'=>['app\\home\\behavior\\CheckLogin'],
];

