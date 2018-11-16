<?php

namespace app\admin\controller;

use app\admin\model\Attribute;
use app\admin\model\Type;
use think\Controller;
use think\Request;
use think\Validate;

class TypeController extends Controller {

    /**
     * 类型列表展示
     * @return mixed
     */
    public function index()
    {
        //关系数据获取示例
        //2) “1对多”关系数据获取
        //$typeinfos = Type::with('attribute')->select('2,3');
//        $typeinfos = Type::select('2,3');
//        foreach($typeinfos as $typeinfo){
//            foreach($typeinfo->attribute as $attr){
//                dump($attr->attr_name); //获得关系数据的信息
//            }
//        }




        //1) “1对1”关系数据获取，通过“属性获取类型”信息
        //$attrinfo = Attribute::find(3);
        //dump($attrinfo);  //单个model模型对象  没有体现关系

        //A.1个属性 获得 一个关系类型 信息
//        $attrinfo = Attribute::with('type')->find(3);
//        //dump($attrinfo);
//        //获得关系的详细信息
//        //dump($attrinfo->type->type_name);  //手机

        //B. 多个属性 获得 对应的多个关系类型  信息
//        $attrinfo = Attribute::with('type')->select('4,5');
//        //dump($attrinfo);  //数组对象集
//        foreach($attrinfo as $v){
//            dump($v->type->type_name);  //手机  电脑
//        }

        //C. 通过属性“直接”获得关系数据
//        $attrinfo = Attribute::find(6);
//        //dump($attrinfo);  //单个model模型对象  没有体现关系
//        dump($attrinfo->type->type_name);  //电脑


        //获取用于展示的类型列表数据信息
        $infos = Type::select();
        $this -> assign('infos',$infos);

        //展示模板
        return $this -> fetch();
    }

    /**
     * 添加类型
     * @param Request $request
     * @return mixed
     */
    public function tianjia(Request $request)
    {
        if($request -> isPost()){
            //1) 数据校验
            $rules = [
                'type_name' => 'require|unique:type',
            ];
            $notices = [
                'type_name.require' => '类型名称必填',
                'type_name.unique' => '类型名称被占用',
            ];
            $validate = new Validate($rules,$notices);

            $shuju = $request -> post();//收集的表单信息
            if($validate -> batch()->check($shuju)){
                //2) 数据入库
                $type = new Type();
                $rst = $type ->allowField(true)-> save($shuju);
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
}

