<?php
/**
 * Created by PhpStorm.
 * User: ssh
 * Date: 2018/7/24
 * Time: 10:46
 */

namespace app\home\behavior;

class Mytest
{
    /**
     * 行为默认执行的方法
     * @param $params
     */
    public function run(&$params)
    {
        // 行为逻辑
        echo "123456";
    }
}
