<?php

namespace app\home\model;

use think\Model;
//命名空间做trait引入
use traits\model\SoftDelete;

class Goods extends Model {
    //使用SoftDelete 的trait，使得本身类可以使用SoftDelete内部的成员
    use SoftDelete;
    //use hello,world;  //使用多个trait情形


    //控制器方法依赖注入执行的地方
    public static function invoke(\think\Request $request)
    {
        $id = $request->param('goods_id');
        return self::find($id);
    }

}




