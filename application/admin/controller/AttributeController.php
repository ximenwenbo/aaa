<?php

namespace app\admin\controller;

use app\admin\model\Attribute;
use app\admin\model\Goods;
use app\admin\model\Type;
use think\Controller;
use think\Request;
use think\Validate;

class AttributeController extends Controller {

    /**
     * 列表展示
     * @return mixed
     */
    public function index(Request $request)
    {
        $type_id = $request->param('type_id'); //获得被查看类型的type_id信息


        //获取用于展示的属性列表数据信息
        //$infos = Attribute::where('type_id',$type_id)->select();
        //与sp_type联表查询获得类型名称
//        $infos = Attribute::alias('a')
//                ->join('__TYPE__ t','a.type_id=t.type_id')
//                ->field('a.*,t.type_name')
//                ->where('a.type_id',$type_id)
//                ->select();

//        //通过“关系”使得 属性 获得对应的 类型 信息
//        $infos = Attribute::with('type')
//                ->where('type_id',$type_id)
//                ->select();
//
//        $this -> assign('infos',$infos);

        //展示模板
        return $this -> fetch();
    }

    /**
     * 添加
     * @param Request $request
     * @return mixed
     */
    public function tianjia(Request $request)
    {
        if($request -> isPost()){
            //1) 数据校验
            $rules = [
                'attr_name' => 'require',
                'type_id' => 'gt:0',
            ];
            $notices = [
                'attr_name.require' => '属性名称必填',
                'type_id.gt' => '所属类型必选',
            ];
            $validate = new Validate($rules,$notices);

            $shuju = $request -> post();//收集的表单信息
            if($validate -> batch()->check($shuju)){
                //2) 数据入库
                //可选值 信息中 "中文，" 替换为 "英文,"
                $shuju['attr_vals'] = str_replace('，',',',$shuju['attr_vals'] );

                $attr = new Attribute();
                $rst = $attr ->allowField(true)-> save($shuju);
                if($rst){
                    return ['status'=>'success'];
                }else{
                    return ['status'=>'failure','errorinfo'=>'请联系管理员'];
                }
            }else{
                //校验失败
                $errorinfo = $validate -> getError();//获得Array数组返回的校验错误信息
                $errorinfo = implode(',',$errorinfo);//Array变为String
                return ['status'=>'failure','errorinfo'=>$errorinfo];
            }
        }else{
            return $this -> fetch();
        }
    }

    /**
     * 根据“类型”获得对应的“属性列表”信息
     */
    public function showAttribute(Request $request)
    {
        //获得类型id信息
        $type_id = $request->get('type_id');
        //根据$type_id获得对应的属性信息
        return Attribute::with('type')->where('type_id',$type_id)->select();
    }
    /**
     * 添加商品表单中
     * 根据“类型”获得对应的“属性列表”信息
     */
    public function showAttribute2(Request $request)
    {
        //获得类型id信息
        $type_id = $request->get('type_id');
        //根据$type_id获得对应的属性信息
        return Attribute::where('type_id',$type_id)->select();
    }
    /**
     * 修改商品表单中
     * 根据“类型”获得对应的“属性列表”信息(最终在客户端制作属性表单域使用)
     * @param $goods: 被修改商品的依赖注入对象
     */
    public function showAttribute3(Request $request,Goods $goods)
    {
        //要获得两个格式的属性：空壳、实心
        //获得类型id信息、被修改商品goods_id信息
        $type_id = $request->get('type_id');

        //根据$type_id获得对应的属性信息
        $attrinfos = Attribute::where('type_id',$type_id)->select();

        //获得"被修改商品"所 拥有/设置 过的属性信息
        $attrvals = unserialize($goods -> goods_attrs);  //二维数组

        //dump($attrvals); //商品拥有的属性信息,二维数组
        //dump($attrinfos); //类型对应的属性基本信息，数组对象集

        //遍历$attrvals，使得信息 与 空壳属性信息 做"结合"
        if(is_array($attrvals)){  //非空判断
            foreach($attrvals as $k => $v){
                //$k：是具体的属性attr_id信息
                //$v: 代表商品设置的每个属性的array数组信息
                foreach($attrinfos as $kk => $vv){
                    //判断商品拥有的属性 与 当前获得的属性一致
                    if($vv->attr_id == $k){
                        $attrinfos[$kk]['values'] = $v;
                    }
                }
            }
        }
        //dump($attrinfos);
        return $attrinfos;
    }
}


















