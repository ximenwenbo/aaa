<?php

namespace app\home\model;

use think\Model;
//命名空间做trait引入
use traits\model\SoftDelete;

class Type extends Model {
    //使用SoftDelete 的trait，使得本身类可以使用SoftDelete内部的成员
    use SoftDelete;
    //use hello,world;  //使用多个trait情形


    //控制器方法依赖注入执行的地方
    public static function invoke(\think\Request $request)
    {
        $id = $request->param('type_id');
        return self::find($id);
    }

    /**
     * 建立与属性Attribute的“1对多”的关系
     */
    public function attribute()
    {
        //return $this -> hasMany(关系模型名称Attribute，关系模型联系字段type_id，本模型关系联系字段type_id);
        return $this -> hasMany('Attribute','type_id','type_id');
    }
}




