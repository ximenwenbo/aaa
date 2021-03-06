<?php

namespace cart;

/***
购物车类
实现对购物车里边商品的添加、删除操作

购物车商品信息对应的二维数组如下：
购物车商品信息对应的二维数组如下：
[
    商品属性唯一uid =>  [
        'cgoods_attr_uid'       =>'商品属性唯一uid',
        'cgoods_id'             =>  '商品id',
        'cgoods_name'           =>  '名称',
        'cgoods_price'          =>  '单价',
        'cgoods_number'         =>  '购买数量',
        'cgoods_price_sum'    =>  '小计价格',
        'cgoods_attrs'          =>  '附加属性',
        'cgoods_logo'           =>  '商品图片'
    ],
    商品属性唯一uid =>  [
        'cgoods_attr_uid'       =>'商品属性唯一uid',
        'cgoods_id'             =>  '商品id',
        'cgoods_name'           =>  '名称',
        'cgoods_price'          =>  '单价',
        'cgoods_number'         =>  '购买数量',
        'cgoods_price_sum'    =>  '小计价格',
        'cgoods_attrs'          =>  '附加属性',
        'cgoods_logo'           =>  '商品图片'
    ],
    ……
]
*/
use app\home\model\UserCart;

class DbCart{
    //购物车的一个属性，用于存放商品信息的
    private $cartInfo = array();
    private $user_id = "";      //会员的持久化id信息
    private $cart_obj = null;   //实例化接收sp_user_cart的 model模型对象

    function __construct(){
        $this -> user_id = session('user_id');
        $this -> cart_obj = new UserCart();  //实例化UserCart对象，获得数据model模型对象
        $this -> loadData();
    }

    /***
    取得购物车里边已经存放的商品信息
    该方法是该类里边第一个被执行的方法
    在类的构造函数里边调用
     */
    function loadData(){
        //判断当前会员是否存在购物车信息
        $obj = $this->cart_obj -> where('user_id',$this->user_id)->find();
        //$obj: object   null
        if($obj!==null){
            //数据库中存储的购物车信息都是"序列化"后的
            //获取购物车商品信息，"反序列化"，并赋予给cartInfo成员属性
            $this->cartInfo = unserialize($obj->cart_info);  //Array二维数组
        }
    }

    /***
    将商品添加到购物车里边
    @param $goods = [
     * 'cgoods_attr_uid'=>'商品属性唯一id',
     * 'cgoods_id'=>'商品id',
     * 'cgoods_name'=>'名称',
     * 'cgoods_price'=>'单价',
     * 'cgoods_number'=>'购买数量',
     * 'cgoods_price_sum'=>'价格小计',
     * 'cgoods_attrs'=>'商品属性信息',
     * 'cgoods_logo'=>'商品logo图片']
     */
    function add($goods){
        $cgoods_attr_uid = $goods['cgoods_attr_uid'];
        //对重复购买的商品要判断(还要判断当前的购物车是否为空，即是否是第一次添加商品)
        if(!empty($this->cartInfo) && array_key_exists($cgoods_attr_uid, $this->cartInfo)){
            //相同商品重复购买，数量增加
            $this->cartInfo[$cgoods_attr_uid]['cgoods_number'] +=1;
        } else {
            //把添加的商品存储给购物车
            $this->cartInfo[$cgoods_attr_uid] = $goods;
        }

        //单件商品的小计价格： 商品单价 * 数量
        $this->cartInfo[$cgoods_attr_uid]['cgoods_price_sum'] = $this->cartInfo[$cgoods_attr_uid]['cgoods_price'] * $this->cartInfo[$cgoods_attr_uid]['cgoods_number'];

        $this -> saveData();//将刷新的数据重新存入session
    }

    /***
    删除购物车里边指定的商品
    @param $goods_id 被删除商品的id信息
     */
    function del($cgoods_attr_uid){
        if(array_key_exists($cgoods_attr_uid, $this -> cartInfo)){
            unset($this -> cartInfo[$cgoods_attr_uid]);
        }
        $this -> saveData();//将刷新的数据重新存入session
    }

    /***
    清空购物车
     */
    function delall(){
        unset($this->cartInfo);
        $this -> saveData();//将刷新的数据重新存入session
    }

    /**
     * @param $uid：被修改商品本身的uid
     * @param $num：被修改商品要求购买的数量
     * 实现购物车单个商品数量的修改
     */
    public function modifynum($cgoods_attr_uid,$num)
    {
        //①修改数量
        $this->cartInfo[$cgoods_attr_uid]['cgoods_number'] = $num;
        //②修改商品的小计价格： 商品单价 * 数量
        $this->cartInfo[$cgoods_attr_uid]['cgoods_price_sum'] = $this->cartInfo[$cgoods_attr_uid]['cgoods_price'] * $this->cartInfo[$cgoods_attr_uid]['cgoods_number'];


        //把当前商品的小计价格、总商品数量、总商品价格一并返回
        $numberprice = $this->getNumberPrice();//获得商品总数量、总价格
        $arr['cnumber']             = $numberprice['cnumber'];
        $arr['cprice']              = $numberprice['cprice'];
        $arr['cgoods_price_sum']    = $this->cartInfo[$cgoods_attr_uid]['cgoods_price_sum'];

        //把修改的信息再存储到购物车中
        $this -> saveData();

        return $arr;
    }

    /***
     * 获得购物车的商品总数量和总价格
     */
    function getNumberPrice(){
        $cnumber = 0;//商品总数量
        $cprice = 0;//商品总价钱

        //获得商品的数量和价格
        foreach($this->cartInfo as $_k => $_v){
            $cnumber  += $_v['cgoods_number'];
            $cprice   += $_v['cgoods_price_sum'];
        }

        $arr['cnumber'] = $cnumber;
        $arr['cprice'] = $cprice;

        return $arr;
    }

    //返回购物车的商品信息，Array格式返回
    function getCartInfo(){
        return $this -> cartInfo;
    }

    /***
    将cartInfo数组的商品信息存入购物车
     */
    function saveData(){
        //把$this->cartInfo的二维数组的购物车信息“序列化”
        //并存储到user_cart数据表中

        if(!empty($this->cartInfo)){
            //判断当前会员在购物车中是否有购物记录
            //有：执行②   没有：执行①
            $exists = $this->cart_obj ->where('user_id',$this->user_id)->find();

            if($exists){
                //②添加后续第2、3、4、。。。等商品要执行update更新语句
                //信息需要序列化后再存储到数据库中
                $data['cart_info'] = serialize($this->cartInfo);
                $this->cart_obj ->where('user_id',$this->user_id)->update($data);
            }else{
                //①添加第1个商品执行save添加语句
                $data['user_id']   = $this->user_id;
                //信息需要序列化后再存储到数据库中
                $data['cart_info'] = serialize($this->cartInfo);
                $this->cart_obj ->save($data);
            }
        }else{
            //删除数据表购物车记录
            $this->cart_obj ->where('user_id',$this->user_id)->delete();
        }
    }
}





