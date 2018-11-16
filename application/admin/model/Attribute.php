<?php

namespace app\admin\model;

use think\Model;
//命名空间做trait引入
use traits\model\SoftDelete;

class Attribute extends Model {
    //使用SoftDelete 的trait，使得本身类可以使用SoftDelete内部的成员
    use SoftDelete;
    //use hello,world;  //使用多个trait情形


    //控制器方法依赖注入执行的地方
    public static function invoke(\think\Request $request)
    {
        $id = $request->param('attr_id');
        return self::find($id);
    }

    /**
     * 建立与Type的“1对1”关系
     * 方法名称即：关系名称
     */
    public function type(){
        //return $this -> hasOne(关系模型名称Type，关系模型联系字段type_id，本模型关系联系字段type_id);
        return $this -> hasOne('Type','type_id','type_id');
    }
}






















