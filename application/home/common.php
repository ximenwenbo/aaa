<?php
/**
 * Created by PhpStorm.
 * User: ssh
 * Date: 2018/7/23
 * Time: 17:03
 */


/*利用memcache把获得的各个分类信息缓存起来，供获取使用*/
//获得1级别分类信息
function get_catinfo_one(){
    /*
    $m = new Memcached();
    $m -> addServer('192.168.139.200',11211);

    //通过memcache获得1级分类信息
    $one_infos = $m -> get('m_cat_one');
    if(!$one_infos){
        $one_infos = \app\home\model\Category::where('cat_level','0')->select();
        $m -> set('m_cat_one',$one_infos);
    }
    return $one_infos;
    */
    return \app\home\model\Category::where('cat_level','0')->select();

}
//获得2级别分类信息
function get_catinfo_two(){
    /*
    $m = new Memcached();
    $m -> addServer('192.168.139.200',11211);

    //通过memcache获得2级分类信息
    $two_infos = $m -> get('m_cat_two');
    if(!$two_infos){
        $two_infos = \app\home\model\Category::where('cat_level','1')->select();
        $m -> set('m_cat_two',$two_infos);
    }
    return $two_infos;
    */
    return \app\home\model\Category::where('cat_level','1')->select();
}
//获得3级别分类信息
function get_catinfo_three(){
    /*
    $m = new Memcached();
    $m -> addServer('192.168.139.200',11211);

    //通过memcache获得2级分类信息
    $three_infos = $m -> get('m_cat_three');
    if(!$three_infos){
        $three_infos = \app\home\model\Category::where('cat_level','2')->select();
        $m -> set('m_cat_three',$three_infos);
    }
    return $three_infos;
    */
    return \app\home\model\Category::where('cat_level','2')->select();
}





