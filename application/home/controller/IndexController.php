<?php
namespace app\home\controller;

use app\home\model\Goods;
use think\Controller;

class IndexController extends Controller
{
    public function index()
    {
        //通过redis获得各个特殊营销商品，并取得商品的相关信息传递给模板展示

        $redis = get_redis_obj();

        //1) 获得“热卖goods_hot”商品信息[sorted-set]
        //销售数量由高到低获得各个商品
        $hot_ids = $redis -> zRevRange(config('special_goods.hot'),0,-1);
        //dump($hot_ids);  //['val','val','val'...]
        //根据$hot_ids获得商品相关的名称、图片、价格等信息
        $hot_infos = Goods::where('goods_id','in',$hot_ids)
            ->field('goods_id,goods_name,goods_price,goods_logo')
            ->select();
        //dump($hot_infos); //数组对象集
        $this -> assign('hot_infos',$hot_infos);

        //2） 获得“促销goods_pro”商品信息[set]
        $pro_ids = $redis -> smembers(config('special_goods.pro'));
        //dump($pro_ids); //['val','val','val'...]
        //根据$pro_ids获得商品相关的名称、图片、价格等信息
        $pro_infos = Goods::where('goods_id','in',$pro_ids)
            ->field('goods_id,goods_name,goods_price,goods_logo')
            ->limit(4)
            ->select();
        $this -> assign('pro_infos',$pro_infos);

        //3) 获得“新品goods_new”商品信息[list]
        $new_ids = $redis -> lrange(config('special_goods.new'),0,-1);
        //dump($new_ids);//['val','val','val'...]
        //根据$new_ids获得商品相关的名称、图片、价格等信息
        $new_infos = Goods::where('goods_id','in',$new_ids)
            ->field('goods_id,goods_name,goods_price,goods_logo')
            ->select();
        $this -> assign('new_infos',$new_infos);

        return $this -> fetch();
    }

    public function abc()
    {
        echo "hello";
    }
}


















