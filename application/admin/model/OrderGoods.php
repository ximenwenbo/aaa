<?php

namespace app\admin\model;

use think\Model;


class OrderGoods extends Model {


    /**
     * 建立与 goods的1vs1关系
     * @return \think\model\relation\HasOne
     */
    public function goods()
    {
        return $this -> hasOne('Goods','goods_id','goods_id');
    }
}






















