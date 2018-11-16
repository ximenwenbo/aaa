<?php
namespace app\home\controller;

use app\home\model\Attribute;
use app\home\model\Consignee;
use app\home\model\Order;
use app\home\model\OrderGoods;
use cart\SessionCart;
use cart\DbCart;
use think\Controller;
use think\Request;
use app\home\model\Goods;

/**
 * 购物车操控主体，调ff用工具类实现商品在购物车的 增、删、改、查 操作
 * Class ShopController
 * @package app\home\controller
 */
class ShopController extends Controller
{
    //声明存储购物车类对象成员属性
    private $cart = null;

    /**
     * 构造方法
     * ShopController constructor.
     */
    public function __construct()
    {
        //为了避免功能丢失，先调用下父类的构造方法
        parent::__construct();

        //判断会员是否登录系统，并获得购物车对象
        if(session('?user_name')){
            $this -> cart = new DbCart();
        }else{
            $this -> cart = new SessionCart();
        }
    }

    /**
     * 给购物车添加商品
     */
    public function tianjia(Request $request,Goods $goods)
    {
        //dump($request -> post());

        //把被添加商品信息制作为购物车需要的样子
        $info['cgoods_id']          = $goods-> goods_id;
        $info['cgoods_name']        = $goods-> goods_name;
        $info['cgoods_price']       = $goods-> goods_price;
        $info['cgoods_number']      = 1;
        $info['cgoods_price_sum']   = $goods-> goods_price;
        $info['cgoods_attrs']       = $request-> post('attr_info/a');
        $info['cgoods_logo']        = $goods-> goods_logo;
        $uid = md5(serialize($info));  //用以区分相同商品选取了不同的属性
        $info['cgoods_attr_uid']    = $uid;

        //dump($info);
        //把制作好的商品信息添加到购物车中
        $this->cart -> add($info);
        //echo "success";

        //显示添加成功页面效果
        //① 跳转(redirect())到对应的页面
        //② 直接fetch()模板渲染，不推荐，因为页面会使用F5进行刷新，造成相同商品重复添加
        ///home/shop/tianjia_show/goods_id/xxx
        $this -> redirect('tianjia_show',['goods_id'=>$goods->goods_id]);
    }

    /**
     * 添加商品到购物车后的提示页面效果
     * @param Request $request
     * @param Goods $goods
     */
    public function tianjia_show(Request $request,Goods $goods)
    {
        //传递被添加商品的模型对象到模板中，以便展示使用
        $this -> assign('goods',$goods);
        return $this -> fetch();
    }


    /**
     * 购物车商品列表展示
     * @param Request $request
     */
    public function showgoods(Request $request)
    {
        //获得购物车商品信息(二维数组结构)
        $cartinfos = $this->cart->getCartInfo();
        $this -> assign('cartinfos',$cartinfos);
//dump($cartinfos);
        //获得购物车商品"总数量和总价格"
        $numberprice = $this->cart->getNumberPrice();  //['cnumber'=>xx,'cprice'=>xx]
        $this -> assign('numberprice',$numberprice);

        //把 全部属性的id 和 名称 对应的值获得到
        //$attrinfo = [1=>'cpu',2=>'颜色',3=>'操作系统',4=>'内存'......]
        //$attrinfo = [1=>'cpu',2=>'颜色',3=>'操作系统',4=>'内存'];
        //column(key1,key2):把数据表字段key2和key1获得出来，并变为一个一维数组,元素下标就是key2,值就是key1
        $attrinfo = Attribute::column('attr_name','attr_id');
        $this -> assign('attrinfo',$attrinfo);

        return $this -> fetch();
    }

    /**
     * 购物车删除商品
     * @param Request $request
     */
    public function del(Request $request)
    {
        //获得被删除商品唯一uid信息
        $uid = $request -> post('uid');
        $this->cart->del($uid);

        //把更新后购物车商品总数量、总价格重新获得出来
        $numberprice = $this->cart->getNumberPrice();

        return [
            'cnumber'=>$numberprice['cnumber'],
            'cprice'=>$numberprice['cprice']];
    }

    /**
     * 实现购物车商品数量修改操作
     * @param Request $request
     */
    public function xiugaiNum(Request $request)
    {
        //接收修改后的数量 和 商品uid
        $num = $request -> post('num');
        $uid = $request -> post('uid');

        //购物车修改商品数量
        $infos = $this->cart->modifynum($uid,$num);

        return $infos; //['cnumber'=>xx,'cprice'=>xx,'cgoods_price_sum'=>xx]
    }

    /**
     * 结算展示页面
     * @param Request $request
     * @return mixed
     */
    public function jiesuan(Request $request)
    {
        //执行home\behavior\CheckLogin的行为，判断如果没有登录系统就去登录
        $params = '/home/shop/jiesuan';
        \think\Hook::listen('denglu',$params);
        //(注意，上述listen()方法必须通过 变量 传递第2个参数)

        //把Session购物车 商品 转存到 Mysql数据库购物车 中去(之后清空Session购物车信息)
        $session_cart = new SessionCart();
        $db_cart = new DbCart();
        $session_cartinfos = $session_cart->getCartInfo();
        //判断session购物车有商品信息
        if(!empty($session_cartinfos)){
            //把数据转存给Mysql数据库购物车
            foreach($session_cartinfos as $v){
                $db_cart -> add($v);
            }
            //清除session购物车信息
            $session_cart -> delall();
        }

        //获得当前会员本身关联的“收货人”信息
        $cgninfos = Consignee::where('user_id',session('user_id'))->select();
        $this -> assign('cgninfos',$cgninfos);

        //获得购物车商品清单
        $cartinfos = $db_cart->getCartInfo();
        //获得购物车商品总数量和总价格
        $numberprice = $this->cart->getNumberPrice();
        $this -> assign('cartinfos',$cartinfos);
        $this -> assign('numberprice',$numberprice);

        return $this -> fetch();
    }

    public function abcd()
    {
        $params = '/home/shop/abcd';
        \think\Hook::listen('denglu',$params);
    }
    public function efgh()
    {
        $params = '/home/shop/efgh';
        \think\Hook::listen('denglu',$params);
    }

    /**
     * 提交并生成订单
     * @param Request $request
     */
    public function makeorder(Request $request,Consignee $consignee)
    {
        //要求会员登录系统
        $params = '/home/shop/jiesuan';
        \think\Hook::listen('denglu',$params);

        $numberprice = $this->cart->getNumberPrice();//购物车商品总数量、总价格
        $cartinfos = $this->cart->getCartInfo();//购物车商品清单信息

        if(!empty($cartinfos)){
            //dump($request->post());  //收货人、支付方式
            //组织收货人信息
            $cgninfo['user_id']     = $consignee->user_id;
            $cgninfo['cgn_name']    = $consignee->cgn_name;
            $cgninfo['cgn_address'] = $consignee->cgn_address;
            $cgninfo['cgn_tel']     = $consignee->cgn_tel;
            $cgninfo['cgn_code']    = $consignee->cgn_code;
            //1) 生成订单信息
            $info0['user_id']       = session('user_id');
            $info0['order_number'] = 'itcast-'.date('Ymd').'php68-'.date('His').rand(10000,99999);
            $info0['order_price'] = $numberprice['cprice'];
            $info0['order_pay'] = $request->post('order_pay');
            $info0['cgn_address'] = serialize($cgninfo);
            $order = new Order();
            $order -> save($info0);

            //2) 订单关联商品信息
            $shuju = [];
            $i = 0;
            foreach($cartinfos as $v){
                $shuju[$i]['order_id'] = $order->order_id;
                $shuju[$i]['goods_id'] = $v['cgoods_id'];
                $shuju[$i]['goods_price'] = $v['cgoods_price'];
                $shuju[$i]['goods_number'] = $v['cgoods_number'];
                $shuju[$i]['goods_price_sum'] = $v['cgoods_price_sum'];
                //商品的单选属性
                $shuju[$i]['goods_attrs'] = serialize($v['cgoods_attrs']);
                $i++;
            }
            //$shuju是一个二维数组[0=>[order_id=>xx,goods_id=>xx,goods_price=>xx..],1=>[order_id=>xx..],2=>[]]
            $ordergoods = new OrderGoods();
            $ordergoods -> saveAll($shuju);

            //3) 清除购物车数据
            $this -> cart -> delall();

            //echo "订单已经形成好，请去支付。。。。";
            /*********************************支付宝支付1************************************/
            //绘制支付form表单
            $ordernum = $info0['order_number'];//订单号码
            $total = $numberprice['cprice'];//支付总金额
            $dizhi = url('payorder');  //支付提交地址
            echo <<<eof
            <form method="post" action="$dizhi">
                <input type="hidden" name="WIDout_trade_no" value="$ordernum" />
                <input type="hidden" name="WIDsubject" value="$ordernum" />
                <input type="hidden" name="WIDtotal_amount" value="$total" />
                <input type="hidden" name="WIDbody" value="$ordernum" />
            </form>
            <script type="text/javascript">
                document.getElementsByTagName('form')[0].submit();
            </script>
eof;
            /*********************************支付宝支付2************************************/
        }else{
            exit('请给购物车添加商品');
        }
    }

    /**
     * 支付form表单提交目的地
     * @param Request $request
     */
    public function payorder(Request $request)
    {
        //EXTEND_PATH 常量：项目根目录/php68/extend
        require_once EXTEND_PATH.'/alipay/config.php';
        require_once EXTEND_PATH.'/alipay/pagepay/service/AlipayTradeService.php';
        require_once EXTEND_PATH.'/alipay/pagepay/buildermodel/AlipayTradePagePayContentBuilder.php';

        //商户订单号，商户网站订单系统中唯一订单号，必填
        $out_trade_no = trim($_POST['WIDout_trade_no']);

        //订单名称，必填
        $subject = trim($_POST['WIDsubject']);

        //付款金额，必填
        $total_amount = trim($_POST['WIDtotal_amount']);

        //商品描述，可空
        $body = trim($_POST['WIDbody']);

        //构造参数
        $payRequestBuilder = new \AlipayTradePagePayContentBuilder();
        $payRequestBuilder->setBody($body);
        $payRequestBuilder->setSubject($subject);
        $payRequestBuilder->setTotalAmount($total_amount);
        $payRequestBuilder->setOutTradeNo($out_trade_no);

        $aop = new \AlipayTradeService($config);

        /**
         * pagePay 电脑网站支付请求
         * @param $builder 业务参数，使用buildmodel中的对象生成。
         * @param $return_url 同步跳转地址，公网可以访问
         * @param $notify_url 异步通知地址，公网可以访问
         * @return $response 支付宝返回的信息
         */
        $response = $aop->pagePay($payRequestBuilder,$config['return_url'],$config['notify_url']);

        //输出表单
        var_dump($response);
    }

    /**
     * 支付宝付款成功后商家展示效果
     * @param Request $request
     */
    public function return_url(Request $request)
    {
        //支付成功后，修改订单状态及记录相关信息
        require_once EXTEND_PATH.'/alipay/config.php';
        require_once EXTEND_PATH.'/alipay/pagepay/service/AlipayTradeService.php';

        $arr=$_GET;
        $alipaySevice = new \AlipayTradeService($config);
        $alipaySevice->writeLog(var_export($_GET,true));

        //对数据进行“安全校验”
        $result = $alipaySevice->check($arr);

        /* 实际验证过程建议商户添加以下校验。
        1、商户需要验证该通知数据中的out_trade_no是否为商户系统中创建的订单号，
        2、判断total_amount是否确实为该订单的实际金额（即商户订单创建时的金额），
        3、校验通知中的seller_id（或者seller_email) 是否为out_trade_no这笔单据的对应的操作方（有的时候，一个商户可能有多个seller_id/seller_email）
        4、验证app_id是否为该商户本身。
        */
        if($result) {//验证成功
            //商户订单号
            $out_trade_no = $_GET['out_trade_no'];
            //支付宝交易号
            $trade_no = $_GET['trade_no'];
            //支付金额
            $total_amount = $_GET['total_amount'];
            //交易状态
            //$trade_status = $_POST['trade_status'];
            //if($_POST['trade_status'] == 'TRADE_FINISHED' || $_POST['trade_status'] == 'TRADE_SUCCESS') {
            //}
            $order = new Order();
            //判断订单之前“未”付款
            $exists = Order::where('order_number',$out_trade_no)
                    ->where('order_status','1')
                    ->find();
            if($exists===null){
                //做后续业务逻辑实现
                $shuju['order_status'] = '1';
                $shuju['order_trade_no'] = $trade_no;
                $shuju['order_pay_money'] = $total_amount;
                $order -> where('order_number',$out_trade_no)->update($shuju);
            }else{
                echo "此订单之前已经付款";
                exit;
            }
            //——请根据您的业务逻辑来编写程序（以上代码仅作参考）——
            //echo "success";	//请不要修改或删除
        }else {
            //验证失败
            //echo "fail";
        }
        return $this -> fetch();
    }

    /**
     * 支付宝系统向商家发起支付后的post请求
     * 但是要求商家网站是"在线"的
     */
    public function notify_url()
    {

    }
}


















