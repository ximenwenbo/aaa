<?php

namespace app\home\controller;

use app\home\model\Attribute;
use app\home\model\Category;
use app\home\model\Goods;
use app\home\model\GoodsPics;
use think\Controller;
use think\Request;

class GoodsController extends Controller{

    /**
     * 商品列表(根据“分类”展示)
     * 根据"分类"获得对应的商品列表信息
     * 把“当前选中分类”及内部所有“递归子级分类”对应的全部商品列表信息给获得到
     */
    public function index(Request $request,Category $category)
    {
        //获得选取分类级别
        $level = $category->cat_level;

        //根据级别限定查询条件
        if($level=='0'){
            $cdt = "cat_one_id";
        }elseif($level=='1'){
            $cdt = "cat_two_id";
        }else{
            $cdt = "cat_three_id";
        }
        $goodsinfos = Goods::where("$cdt",$category->cat_id)->select();
        $this -> assign('goodsinfos',$goodsinfos);
        return $this -> fetch();
    }


    /**
     * 商品列表(根据“关键字”检索展示)
     */
    public function indexs(Request $request,Category $category)
    {
        /************************通过sphinx检索数据1**************************/
        $ids = []; //声明一个存储被搜索商品的id变量
        $key_name = $request -> get('search_key');
        if($key_name){
            $cl = new \SphinxClient ();
            $cl->SetServer ( '192.168.139.200', 9312);
            $cl->SetArrayResult ( true );
            $cl->SetMatchMode ( SPH_MATCH_ANY ); //设置"完整关键字"匹配(与mysql的like相似)
            $cl -> setlimits(0,20); //显示前20条

            $index_name = "goods";  //索引名称
            $res = $cl->Query ( $key_name, $index_name ); //开始检索
            if($res['total']>0) {
                //制作应用查看检索到信息
                //获得检索到的主键id值
                $ids = [];  //存储id的变量
                foreach ($res['matches'] as $v) {
                    $ids[] = $v['id'];  //集合所有的id
                }
                $goodsinfos = Goods::field('goods_id,goods_name,goods_price,goods_logo');
                $goodsinfos = $goodsinfos -> where('goods_id','in',$ids);
                $goodsinfos = $goodsinfos    ->select();
                //给展示的搜索到的商品设置语法高亮
                //if判断有做关键字检索 并且 有检索到数据 才做"高亮"语法
                $tmpinfos = []; //声明一个空数组变量，用于接收制作好的语法高亮数据
                foreach($goodsinfos as $k => $v){
                    //把数据的各个部分变为"纯String类型"，因为sphinx语法高亮需要
                    $arr_v['goods_id']      = (string)($v->goods_id);
                    $arr_v['goods_name']    = (string)($v->goods_name);
                    $arr_v['goods_price']   = (string)($v->goods_price);
                    $arr_v['goods_logo']    = (string)($v->goods_logo);

                    //buildExcerpts()方法返回一个"一维索引数组"结果
                    //注意：传递给buildExcerpts()方法第一个参数是一维数组(被处理数据)，
                    //要求内部各个组成部分的值必须是String字符串类型
                    $light_v = $cl -> buildExcerpts($arr_v,$index_name,$key_name,[
                        'before_match'=>'<span style="background-color:yellow">',
                        'after_match'=>'</span>'
                    ]);

                    //把生成好的$light_v的一维索引数组再变回“关联”数组
                    $tmpinfos[$k]['goods_id']       = $light_v[0];
                    $tmpinfos[$k]['goods_name']     = $light_v[1];
                    $tmpinfos[$k]['goods_price']    = $light_v[2];
                    $tmpinfos[$k]['goods_logo']     = $light_v[3];
                }
                $this -> assign('goodsinfos',$tmpinfos);
                return $this -> fetch('index');
            }
        }
        //没有检索到数据的情形
        $this -> assign('goodsinfos',[]);
        return $this -> fetch('index');
        /************************通过sphinx检索数据2**************************/
    }

    /**
     * 商品详情
     * 根据商品goods_id获得相关信息展示
     */
    public function detail(Request $request,Goods $goods)
    {
        //① 传递商品依赖注入对象到模板中
        $this -> assign('goods',$goods);

        //② 获得当前被查看商品的"相册"图片信息
        $picsinfos = GoodsPics::where('goods_id',$goods->goods_id)->select();
        $this -> assign('picsinfos',$picsinfos);

        //③ (唯一/单选)属性信息整合
        //获得当前商品"类型"对应的"属性"信息情况
        $attrinfos = Attribute::where('type_id',$goods->type_id)->select();
        //dump($attrinfos);

        //获得当前商品拥有“属性值”的信息
        $attrvals = unserialize($goods -> goods_attrs);
        //dump($attrvals);

        //把属性信息 与 属性值 融合到一起
        foreach($attrinfos as $k => $v){
            //$k:从0开始自增的序号信息
            foreach($attrvals as $kk => $vv){
                //$kk:属性的attr_id信息
                //让属性 和 属性值 先对应上
                if($v->attr_id == $kk){
                    $attrinfos[$k]['values'] = $vv;
                }
            }
        }
        //dump($attrinfos);

        //把融合好的信息传递给模板
        $this -> assign('attrinfos',$attrinfos);

        return $this -> fetch();
    }
}


















