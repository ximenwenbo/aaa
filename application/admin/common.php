<?php
/**
 * Created by PhpStorm.
 * User: ssh
 * Date: 2018/7/23
 * Time: 17:03
 */

/**
 * 给当前商品维护“新添加相册”信息
 * @param $goods_id
 */
function pics_deal($goods_id){
    //遍历、获得全部的大图、中图路径名信息
    $picture_bigs = request()->post('picture_big/a');
    $picture_mids = request()->post('picture_mid/a');

    $result = [];
//    $result = [
//        ['goods_id'=>xx,'pics_big'=>xx,'pics_mid'=>xx],
//        ['goods_id'=>xx,'pics_big'=>xx,'pics_mid'=>xx],
//        ['goods_id'=>xx,'pics_big'=>xx,'pics_mid'=>xx],
//    ];
    //通过foreach把上述$result给制作出来
    if(!empty($picture_bigs)){

        //把相册图片从“临时”位置  挪到“终极”存储目录去
        $path = "./uploads/pics/".date("Ymd");
        if(!file_exists($path)){
            mkdir($path,0777,true);
        }

        foreach($picture_bigs as $k => $v){
            //大图 终极存储路径名设置
            $finalBigName = str_replace('picstmp','pics',$v);
            //中图  终极存储路径名设置
            $finalMidName = str_replace('picstmp','pics',$picture_mids[$k]);

            //挪动图片
            rename($v,$finalBigName);
            rename($picture_mids[$k],$finalMidName);

            $result[$k]['goods_id'] = $goods_id;
            $result[$k]['pics_big'] = $finalBigName;
            $result[$k]['pics_mid'] = $finalMidName;
        }
        //判断有上传相册图片
        if(!empty($result)){
            $goodspics = new \app\admin\model\GoodsPics();
            //一次性写入多条记录信息
            $goodspics -> saveAll($result);//给数据表写入多条记录信息
        }
    }

}

/**
 * 获得全部“类型”信息
 */
function get_type_info(){
    return \app\admin\model\Type::field('type_id,type_name')->select();
}

/***************按照上下级关系获取权限信息***************/

/**
 * @param $data:被处理的数据，要求是二维Array数组格式
 * @return array
 */
function generateTree($data){
    $items = array();
    foreach($data as $v){
        $items[$v['ps_id']] = $v;
    }
    $tree = array();
    foreach($items as $k => $item){
        if(isset($items[$item['ps_pid']])){
            $items[$item['ps_pid']]['son'][] = &$items[$k];
        }else{
            $tree[] = &$items[$k];
        }
    }
    return getTreeData($tree);
}


function getTreeData($tree,$level=0){
    static $arr = array();
    foreach($tree as $t){
        $tmp = $t;
        unset($tmp['son']);
        //$tmp['level'] = $level;
        $arr[] = $tmp;
        if(isset($t['son'])){
            getTreeData($t['son'],$level+1);
        }
    }
    return $arr;
}

/***************按照上下级关系获取权限信息***************/
