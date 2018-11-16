<?php
//【windows】php操作redis
$redis = new Redis();

//连接redis
$redis -> connect('192.168.139.200',6379);

//连接密码
$redis -> auth('football');

//选取6号数据库
$redis -> select(6);

//获得key的信息
var_dump($redis -> get('addr'));
echo "<hr />";
var_dump($redis -> mget(['weather','addr','week']));


