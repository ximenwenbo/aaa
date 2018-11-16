<?php

namespace app\home\controller;

use think\Controller;
use think\Request;


class SeckillController extends Controller {

    /**
     * 会员实施抢购商品
     * @param Request $request
     */
    public function qiang(Request $request)
    {
        //抢购商品实施
        //用户必须登录的处理
        $params = url('/home/seckill/qiang',['goods_id'=>$request->param('goods_id')]);
        \think\Hook::listen('denglu',$params);

        //获得被抢购商品id
        $goods_id = $request->param('goods_id');

        //redis中当前被抢购商品库存减少操作
        $redis = get_redis_obj(10);
        //decr()可以使得key做减一操作 类似 --$i
        $num = $redis->decr(config('seckill.num').$goods_id);

        //判断剩余库存是否允许抢购
        if($num>=0){
            //允许
            //获得抢购会员id
            $user_id = session('user_id');
            //把成功抢购当前商品的会员id存储好
            $redis -> lpush(config('seckill.byuser').$goods_id,$user_id);
            echo "恭喜成功秒杀到".$goods_id."的商品";
        }else{
            //超售，禁止抢购，恢复抢购数量
            $redis->incr(config('seckill.num').$goods_id);
            echo "抱歉，商品已经被秒杀完毕";
        }
        exit;
    }
    
    /**
     * 秒杀首页面展示
     * @return mixed
     */
    public function index(){
        //用户必须登录的处理
        $params = '/home/seckill/index';
        \think\Hook::listen('denglu',$params);

        //获得redis中秒杀商品列表信息
        $redis = get_redis_obj(10);

        //获得商品goods_id集合
        $goods_ids = $redis -> smembers(config('seckill.ids'));
        //根据$goods_ids获得商品的基本信息
        $goodsinfos = []; //声明一个空数组变量
        foreach($goods_ids as $k => $id){
            //获得每个秒杀商品信息后直接反序列化存储给goodsinfos变量
            $goodsinfos[$k] = unserialize($redis -> get(config('seckill.info').$id));

            //获得当前商品可以被秒杀购买的数量
            $goodsinfos[$k]['second_num'] = $redis->get(config('seckill.num').$id);

        }
        //dump($goodsinfos); //二维数组的秒杀商品信息
        $this -> assign('goodsinfos',$goodsinfos);

        return $this -> fetch();
    }
}













