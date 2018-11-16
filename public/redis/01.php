<?php
//【windows】php操作redis
$redis = new Redis();

//连接redis
$redis -> connect('192.168.139.200',6379);

//连接密码
$redis -> auth('football');

//选取6号数据库
$redis -> select(6);

//设置相关key
$redis -> set('weather','rain and cloud');
$redis -> set('addr','顺义');
$redis -> set('week','Tuesday');


