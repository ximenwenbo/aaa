<?php

namespace app\admin\controller;

use app\admin\model\Attribute;
use app\admin\model\Order;
use app\admin\model\OrderGoods;
use think\Controller;
use think\Request;


class OrderController extends Controller {

    /**
     * 订单列表展示
     * @param Request $request
     */
    public function index(Request $request)
    {
        //获得mongodb存储的日志信息并输出
        $loginfos = \think\Db::connect('db_mongodb')->name('log')->select();
        dump($loginfos);

        //获得订单列表信息，传递给模板展示
        //制作分页数据
        $orderinfos = Order::with('user')
            ->order('order_id desc')
            ->paginate(6);
        //获得分页页码列表信息
        $pagelist = $orderinfos->render();
        $this -> assign('orderinfos',$orderinfos);
        $this -> assign('pagelist',$pagelist);

        return $this -> fetch();
    }

    /**
     * 查看订单详情
     * @param Request $request
     */
    public function detail(Request $request,Order $order)
    {
        //1) 传递$order model模型对象 到模板
        $this -> assign('order',$order);
        //2) 获取收获地址并传递给模板
        $cgninfo = unserialize($order->cgn_address);
        $this -> assign('cgninfo',$cgninfo);
        //3）支付方式
        $this -> assign('pay_method',['0'=>'支付宝','1'=>'微信','2'=>'银行卡']);
        //4) 获得订单关联的商品并传递给模板
        $goodsinfos = OrderGoods::where('order_id',$order->order_id)->select();
        $this -> assign('goodsinfos',$goodsinfos);

        //5) 处理订单关联商品的属性，给其做反序列化操作
        foreach($goodsinfos as $k => $v){
            $goodsinfos[$k]['goods_attrs'] = unserialize($v['goods_attrs']);
        }
        //把[属性id=>名称,id=>name,id=>name..] 的信息获得出来
        $attrinfos = Attribute::column('attr_name','attr_id');
        $this -> assign('attrinfos',$attrinfos);

        //通过具体模板展示订单详情效果
        return $this -> fetch();
    }
}



















