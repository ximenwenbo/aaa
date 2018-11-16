<?php

namespace app\admin\controller;

use app\admin\model\Category;
use think\Controller;
use think\Request;


class CategoryController extends Controller {

    /**
     * 列表展示
     * @return mixed
     */
    public function index(Request $request)
    {
        //展示模板
        return $this -> fetch();
    }

    /**
     * 获得用于给zTree树显示的分类数据信息
     */
    public function getcatinfo(Request $request){
        //返回一个“数组对象集”，可以用于"zTree树"显示使用
        return Category::field('cat_id,cat_pid,cat_name,cat_level')->select();
    }

    /**
     * 删除分类信息
     * @param Request $request
     * @return array
     */
    public function shanchu(Request $request)
    {
        //返回被删除记录条数
        $rst = Category::destroy($request->post('cat_id'));
        if($rst){
            return ['status'=>'success'];
        }
        return ['status'=>'failure'];
    }

    /**
     * 修改分类信息，(同时兼容做节点添加处理)
     * @param Request $request
     * @return array
     */
    public function xiugai(Request $request)
    {
        $data['cat_id']     = $request->post('cat_id');
        $data['cat_name']   = $request->post('cat_name');
        $data['cat_pid']    = $request->post('cat_pid');
        $data['cat_level']  = $request->post('cat_level');

        $category = new Category();
        //判断添加、修改逻辑
        if($data['cat_id']==0){
            //添加数据
            unset($data['cat_id']);
            $rst = $category ->allowField(true)-> save($data);//返回新添加记录条数
            if($rst){
                return ['status'=>'success','info'=>'添加分类成功','cat_id'=>$category->cat_id];
            }
            return ['status'=>'failure','info'=>'添加分类失败,请联系管理员'];
        }else{
            //修改数据
            $rst = $category->update($data);
            if($rst){
                return ['status'=>'success','info'=>'修改分类成功','cat_id'=>$data['cat_id']];
            }
            return ['status'=>'failure','info'=>'修改分类失败,请联系管理员'];
        }
    }
}


















