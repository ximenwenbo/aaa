<?php

namespace app\admin\controller;

use app\admin\model\GoodsPics;
use think\Controller;
use app\admin\model\Goods;
use think\Image;
use think\Request;
use think\Validate;

class GoodsController extends Controller {

    /**
     * 秒杀商品列表展示
     * @param Request $request
     */
    public function index_seckill(Request $request)
    {
        //获得秒杀商品(开始时间<当前时间<结束时间)
        $time = time();
        $seckillinfos = Goods::where('start_time','<',$time)
                ->where('end_time','>',$time)
                ->select();
        $this -> assign('seckillinfos',$seckillinfos);

        //展示模板
        return $this -> fetch();
    }

    /**
     * 商品列表
     */
    public function index()
    {
        //获得全部被展示的商品记录
        //$infos = Goods::order('goods_id desc')->select();

        //实现数据分页功能(每页6条记录)
        $infos = Goods::order('goods_id desc')->paginate(6);
        //dump($infos);
        $this -> assign('infos',$infos);

        //获得页码列表
        $pagelist = $infos -> render();
        $this -> assign('pagelist',$pagelist);

        return $this -> fetch();
    }

    /**
     * 添加商品
     * 当前方法承接两种请求：get/post
     * get:展示添加商品的form表单页面
     * post：收集客户端传递过来的信息，存储入库
     * @param $request:是依赖注入对象，请求对象
     * @return mixed
     *
     */
    public function tianjia(Request $request)
    {
        //通过mongodb记录操作日志
        $dt['msg'] = "添加商品信息，id=199";
        $dt['mg_name'] = session('mg_name');
        $dt['time'] = time();
        $dt['ip'] = $request->ip();

        //\think\Db::connect('db_mongodb') //选取操作的数据库媒介
        //\think\Db::connect('db_mongodb')->name('数据表')  //选择操作的数据表
        \think\Db::connect('db_mongodb')->name('log')->insert($dt);


        //判断当前请求类型
        if($request->isPost()){

            //对form表单收集的数据实现校验
            //① 制作验证规则
            $rules = [
                //'表单域name的名称'=>规则,
                //被校验信息 必填，并且sp_goods表中goods_name字段没有出现过被验证的内容信息,长度不小于6个字符
                'goods_name'    => 'require|unique:goods|min:6',
                //被校验信息 必填，并且sp_goods表中goods_abc字段没有出现过被验证的内容信息
                //'goods_name'    => 'require|unique:goods,goods_abc',
                //价格：必填，并且格式为 99999.99  或 0.99
                //'goods_price'   => 'require|regex:[1-9]\d{4}\.\d{2}|0\.\d{2}',  //"竖线|"产生歧义
                'goods_price'   => ['require','regex'=>'^([1-9]\d{0,4}\.\d{2}|0\.\d{2})$'],
                //数量：数字 并且在10到240之间
                'goods_number'  => 'number|between:10,240',
                //必填、数字、大于50
                'goods_weight'  => 'require|number|egt:50',
                //类型必选
                'type_id'   => 'require',
                //分类必选
                'cat_id'    => 'require',
                //秒杀价格限制
                'goods_price_seckill'  => ['regex'=>'^([1-9]\d{0,4}\.\d{2}|0\.\d{2})$'],
                //秒杀数量限制
                'goods_number_seckill'  => 'between:10,240',
            ];

            //② 制作验证的错误提示信息
            $notices = [
                //表单域name的名称.规则 => 具体错误提示,
                'goods_name.require'    =>  '商品名称必须填写',
                'goods_name.unique'     =>  '商品名称已经被占用',
                'goods_name.min'        =>  '商品名称长度不能小于6个字符',
                'goods_price.require'   =>  '商品价格必须填写',
                'goods_price.regex'     =>  '商品价格格式为：99999.99 或0.99',
                'goods_number.number'   =>  '商品数量必须是数字信息',
                'goods_number.between'  =>  '商品数量在10-240之间的数字',
                'goods_weight.require'  =>  '商品重量必须填写',
                'goods_weight.number'   =>  '商品重量的内容是数字格式',
                'goods_weight.egt'      =>  '商品重量值大于50',
                'type_id.require'       =>  '类型必选',
                'cat_id.require'        =>  '分类必选',
                'goods_price_seckill.regex'        =>  '商品秒杀价格格式为：99999.99 或0.99',
                'goods_number_seckill.between'     =>  '商品秒杀数量在10-240之间的数字',
            ];

            //③ 实例化Validate类对象
            $validate = new Validate($rules,$notices);

            //表单收集的数据
            $infos = $request -> post();
            //dump($infos); // ['goods_name'=>xx,'goods_price'=>xx...]数组

            //④ 开始校验（batch: 批量校验、批量提示错误）
            if($validate ->batch()-> check($infos)){
                //验证成功
                $goods = new Goods();

                //把收集到的“属性信息”做序列化操作
                $infos['goods_attrs'] = serialize($infos['shuxing']);

                //根据当前用户选取的分类，获得其第1/2/3级分类信息
                $catinfos = get_cat_parent_ids($infos['cat_id']);
                //把获得到的各个级别分类id信息分别填充到商品表的不同字段中去
                switch(count($catinfos)){
                    case 1:
                        //选取1级别分类
                        $infos['cat_one_id'] = $catinfos[0];
                        break;
                    case 2:
                        //选取2级别分类
                        $infos['cat_one_id'] = $catinfos[0];
                        $infos['cat_two_id'] = $catinfos[1];
                        break;
                    case 3:
                        //选取3级别分类
                        $infos['cat_one_id'] = $catinfos[0];
                        $infos['cat_two_id'] = $catinfos[1];
                        $infos['cat_three_id'] = $catinfos[2];
                        break;
                }

                /***商品logo图片处理01***/
                //商品logo图片处理，把其从uploads/goodstmp目录挪到uploads/goods目录中去
                //通过程序创建"年月日"的子级目录
                $path = "./uploads/goods/".date('Ymd');
                //判断目录不存在
                if(!file_exists($path)){
                    mkdir($path,0777,true);
                }
                //设置图片的"终极"存储目录路径名
                //./uploads/goodstmp/20180724/0c4c6b67dd2b03a3e106334e83373ac8.jpg [临时的]
                //./uploads/goods/20180724/0c4c6b67dd2b03a3e106334e83373ac8.jpg [终极的]
                $finalPathName = str_replace('goodstmp','goods',$infos['goods_logo']);
                //把图片从“临时”位置挪到“终极”存储位置
                rename($infos['goods_logo'],$finalPathName);
                $infos['goods_logo'] = $finalPathName;  //终极路径名要存储到数据库中去
                /***商品logo图片处理02***/

                //strtotime(格式化)：把格式化时间变为时间戳格式
                $infos['start_time'] = !empty($infos['start_time'])?strtotime($infos['start_time']):null;
                $infos['end_time']   = !empty($infos['end_time'])?strtotime($infos['end_time']):null;

                $rst = $goods ->allowField(true)-> save($infos);  //返回添加的记录条数
                if($rst){
                    //商品添加成功获得其主键goods_id信息，并维护相册，通过公共函数实现
                    pics_deal($goods->goods_id);

                    //把新添加商品同时存储到list链表中(goods_new)
                    //该链表始终保留最新添加的"4个"商品
                    $redis = get_redis_obj();
                    $key = config('special_goods.new');
                    $redis -> lpush($key, $goods->goods_id);
                    $redis -> ltrim($key,0,3); //保留最新的4个元素

                    return ['status'=>'success'];
                }else{
                    return ['status'=>'failure'];
                }
            }else{
                //验证失败
                //获得验证的错误信息
                $errorinfo = $validate->getError();
                //dump($errorinfo);  //一维数组方式返回全部校验错误信息 [goods_name=>xx,goods_price=>yy]
                $errorinfo = implode(',',$errorinfo); //把错误信息由Array变为,号分隔的字符串String
                                                            //xx,yy,zz 等错误提示
                //传递错误信息给客户端显示
                return ['status'=>'failure','errorinfo'=>$errorinfo];
            }
        }else{
            //get请求
            return $this -> fetch();
        }
    }

    /**
     * 修改商品
     * 当前方法承接两种请求：get/post
     * get:展示form表单页面
     * post：收集客户端传递过来的信息，存储入库
     * @return mixed
     * @param $goods_id:代表接收请求地址中pathinfo的goods_id参数信息
     *                  pathinfo中如果没有goods_id,也可以从form表单中获取
     * http://网址/admin/goods/xiugai/goods_id/xxx
     */
    //public function xiugai(Request $request,$goods_id)
    public function xiugai(Request $request,Goods $goods)
    {
        //通过mongodb记录操作日志
        $dt['msg'] = "修改商品信息，名称被改为xxx,id=203";
        $dt['mg_name'] = session('mg_name');
        $dt['time'] = time();
        $dt['ip'] = $request->ip();

        //\think\Db::connect('db_mongodb') //选取操作的数据库媒介
        //\think\Db::connect('db_mongodb')->name('数据表')  //选择操作的数据表
        \think\Db::connect('db_mongodb')->name('log')->insert($dt);

        dump(\think\Db::connect('db_mongodb')->name('log'));

        if($request -> isPost()){

            //对form表单收集的数据实现校验
            //① 制作验证规则
            $rules = [
                //'表单域name的名称'=>规则,
                //被校验信息 必填，并且sp_goods表中goods_name字段没有出现过被验证的内容信息,长度不小于6个字符
                'goods_name'    => 'require|unique:goods|min:6',
                //被校验信息 必填，并且sp_goods表中goods_abc字段没有出现过被验证的内容信息
                //'goods_name'    => 'require|unique:goods,goods_abc',
                //价格：必填，并且格式为 99999.99  或 0.99
                //'goods_price'   => 'require|regex:[1-9]\d{4}\.\d{2}|0\.\d{2}',  //"竖线|"产生歧义
                'goods_price'   => ['require','regex'=>'^([1-9]\d{0,4}\.\d{2}|0\.\d{2})$'],
                //数量：数字 并且在10到240之间
                'goods_number'  => 'number|between:10,240',
                //必填、数字、大于50
                'goods_weight'  => 'require|number|egt:50',
                //类型必选
                'type_id'   => 'require',
                //分类必选
                'cat_id'    => 'require',
                //秒杀价格限制
                'goods_price_seckill'  => ['regex'=>'^([1-9]\d{0,4}\.\d{2}|0\.\d{2})$'],
                //秒杀数量限制
                'goods_number_seckill'  => 'between:10,240',
            ];

            //② 制作验证的错误提示信息
            $notices = [
                //表单域name的名称.规则 => 具体错误提示,
                'goods_name.require'    =>  '商品名称必须填写',
                'goods_name.unique'     =>  '商品名称已经被占用',
                'goods_name.min'        =>  '商品名称长度不能小于6个字符',
                'goods_price.require'   =>  '商品价格必须填写',
                'goods_price.regex'     =>  '商品价格格式为：99999.99 或0.99',
                'goods_number.number'   =>  '商品数量必须是数字信息',
                'goods_number.between'  =>  '商品数量在10-240之间的数字',
                'goods_weight.require'  =>  '商品重量必须填写',
                'goods_weight.number'   =>  '商品重量的内容是数字格式',
                'goods_weight.egt'      =>  '商品重量值大于50',
                'type_id.require'       =>  '类型必选',
                'cat_id.require'        =>  '分类必选',
                'goods_price_seckill.regex'        =>  '商品秒杀价格格式为：99999.99 或0.99',
                'goods_number_seckill.between'     =>  '商品秒杀数量在10-240之间的数字',
            ];

            //③ 实例化Validate类对象
            $validate = new Validate($rules,$notices);

            //把正确的、合法的form表单信息筛选出来
            $infos = $request -> only('goods_id,goods_name,goods_number,goods_price,goods_weight,goods_introduce,type_id,cat_id,goods_logo,start_time,end_time,goods_price_seckill,goods_number_seckill');

            //④ 开始校验（batch: 批量校验、批量提示错误）
            if($validate ->batch()-> check($infos)){
                $infos['goods_attrs'] = serialize($request->post('shuxing/a'));//序列化属性信息

                //根据当前用户选取的分类，获得其第1/2/3级分类信息
                $catinfos = get_cat_parent_ids($infos['cat_id']);
                //把获得到的各个级别分类id信息分别填充到商品表的不同字段中去
                switch(count($catinfos)){
                    case 1:
                        //选取1级别分类
                        $infos['cat_one_id']    = $catinfos[0];
                        $infos['cat_two_id']    = 0;
                        $infos['cat_three_id']  = 0;
                        break;
                    case 2:
                        //选取2级别分类
                        $infos['cat_one_id']    = $catinfos[0];
                        $infos['cat_two_id']    = $catinfos[1];
                        $infos['cat_three_id']  = 0;
                        break;
                    case 3:
                        //选取3级别分类
                        $infos['cat_one_id']    = $catinfos[0];
                        $infos['cat_two_id']    = $catinfos[1];
                        $infos['cat_three_id']  = $catinfos[2];
                        break;
                }

                /***商品logo图片修改维护01***/
                if(strpos($infos['goods_logo'],'goodstmp')!==false){
                    //A. 修改商品的logo图片为其他
                    //① 判断有上传新logo图片才维护
                    //② 删除当前商品对应的旧图片(删除物理图片)
                    if(!empty($goods->goods_logo) && file_exists($goods->goods_logo)){
                        unlink($goods->goods_logo);
                    }

                    //③ 创建"年月日"的文件目录
                    $path = './uploads/goods/'.date('Ymd');
                    if(!file_exists($path)){
                        mkdir($path,0777,true);
                    }
                    //制作图片终极路径名
                    $finalPathName = str_replace('goodstmp','goods',$infos['goods_logo']);
                    //图片从临时位置 挪到终极位置
                    rename($infos['goods_logo'],$finalPathName);
                    //设置 终极图片路径名 存储到数据库中
                    $infos['goods_logo'] = $finalPathName;

                }elseif(empty($infos['goods_logo']) && !empty($goods->goods_logo)){
                    //B. 清除商品原有的旧图片
                    if(file_exists($goods->goods_logo)){
                        unlink($goods->goods_logo);
                    }
                }else{
                    //C. 保持原有logo图片不变(不要修改)
                    unset($infos['goods_logo']);
                }
                /***商品logo图片修改维护02***/

                //strtotime(格式化)：把格式化时间变为时间戳格式
                $infos['start_time'] = !empty($infos['start_time'])?strtotime($infos['start_time']):null;
                $infos['end_time']   = !empty($infos['end_time'])?strtotime($infos['end_time']):null;

                $rst = $goods -> update($infos);  //返回修改记录条数
                if($rst){
                    //被修改商品 与 新上传相册联系
                    pics_deal($goods->goods_id);

                    return ['status'=>'success'];
                }else{
                    return ['status'=>'failure'];
                }
            }else{
                //验证失败
                //获得验证的错误信息
                $errorinfo = $validate->getError();
                //dump($errorinfo);  //一维数组方式返回全部校验错误信息 [goods_name=>xx,goods_price=>yy]
                $errorinfo = implode(',',$errorinfo); //把错误信息由Array变为,号分隔的字符串String
                //xx,yy,zz 等错误提示
                //传递错误信息给客户端显示
                return ['status'=>'failure','errorinfo'=>$errorinfo];
            }
        }else{

            //获得当前被修改商品拥有的相册图片
            $picsinfos = GoodsPics::where('goods_id',$goods->goods_id)->select();
            //处理$picsinfos变为如下信息结构
            //[{path: 'http://wx3.sinaimg.cn/mw690/68a0d5e5gy1fcsbq3vrwgj20qo0zkgo5.jpg'},
            //{path: 'http://wx2.sinaimg.cn/mw690/68a0d5e5gy1fcsbqgeyjwj21i01i07wh.jpg'},
            //{path: 'http://wx2.sinaimg.cn/mw690/68a0d5e5gy1fcsbq8g01sj22c03404qs.jpg'}],
            $pics = [];
            //判断商品存在相册图片
            if(!empty($picsinfos)){
                foreach($picsinfos as $v){
                    //substr去除图片路径名前边的.点，以便浏览器显示
                    $pics[] = ['path'=>substr($v->pics_mid,1)];
                }
                $exists_pics = json_encode($pics);
                //dump($exists_pics);  //String:[{},{},{}..]
                //传递 $exists_pics 到模板中
                $this -> assign('exists_pics',$exists_pics);
            }

            //传递依赖注入对象(即当前被修改商品的模型对象)给模板，
            $this -> assign('info',$goods);

            return $this -> fetch();
        }
    }

    /**
     * 删除商品
     * @param $goods_id: 代表pathinfo参数 或 form表单参数
     */
//    public function shanchu(Request $request,$goods_id)
//    {
//        $obj = new Goods();
//        $goods = $obj -> find($goods_id);  //返回被删除商品对应的模型对象
//        $rst = $goods -> delete();//返回删除条数
    public function shanchu(Request $request,Goods $goods){
        $rst = $goods -> delete();

        if($rst){
            return ['status'=>'success'];
        }else{
            return ['status'=>'failure'];
        }
    }


    /**
     * 给商品做logo图片上传处理 [post]
     * @param Request $request
     */
    public function logo_up(Request $request)
    {
        //接收客户端传递过来的附件，并存储到服务器上
        //$request调用file()方法就可以获得被上传附件
        //以"think\File"类对象形式返回
        $file = $request -> file('mylogo');
        //dump($file);  //think\File类对象

        $path = "./uploads/goodstmp/";  //图片存储目录

        //图片上传,move()方法执行成功会返回think\File类对象
        //       上传失败会返回false信息
        //think\File 内部通过算法会给每个上传图片定义一个唯一名字
        $result = $file -> move($path);
        if($result){
            //获得上传好的图片信息
            //获得上传好图片路径名信息
            $filename = $result->getSaveName(); //20160820\42a79759f284b767dfcb2a0197904287.jpg

            $pathfilename = $path.$filename; //拼装图片完整路径名
            $pathfilename = str_replace('\\','/',$pathfilename);//"\"替换为"/"

            return ['status'=>'success','pathfilename'=>$pathfilename];
        }else{
            //上传图片失败
            $errorinfo = $file -> getError();
            return ['status'=>'failure','errorinfo'=>$errorinfo];
        }
    }

    /**
     * 给商品做pics相册图片上传处理 [post]
     * @param Request $request
     */
    public function pics_up(Request $request)
    {
        //接收客户端传递过来的附件，并存储到服务器上
        //$request调用file()方法就可以获得被上传附件
        //以"think\File"类对象形式返回
        $file = $request -> file('mypics');
        //dump($file);  //think\File类对象

        $path = "./uploads/picstmp/";  //图片存储目录

        //图片上传,move()方法执行成功会返回think\File类对象
        //       上传失败会返回false信息
        //think\File 内部通过算法会给每个上传图片定义一个唯一名字
        $result = $file -> move($path);
        if($result){
            //获得上传好的图片信息
            //获得上传好图片路径名信息
            $filename = $result->getSaveName(); //20160820\42a79759f284b767dfcb2a0197904287.jpg

            $pathfilename = $path.$filename; //拼装图片完整路径名
            $pathfilename = str_replace('\\','/',$pathfilename);//"\"替换为"/"


            //根据上传好的原图($pathfilename)制作两种规格的缩略图
            //echo $pathfilename;
            // 原图         ./uploads/picstmp/20180808/00814c81a931a8a977beccdfe071b967.jpg
            // 缩略中图：    ./uploads/picstmp/20180808/mid_00814c81a931a8a977beccdfe071b967.jpg
            // 缩略大图：    ./uploads/picstmp/20180808/big_00814c81a931a8a977beccdfe071b967.jpg
            //① 大图800*800
            $im = Image::open($pathfilename);//打开原图
            $im -> thumb(800,800,6);//固定尺寸缩略图制作,原图大小最好大于800*800
            $bigpathname = $path.date('Ymd').'/big_'.$result->getFilename();
            $im -> save($bigpathname);

            //② 中图400*400
            $im2 = Image::open($pathfilename);
            $im2 -> thumb(400,400,6);
            $midpathname = str_replace('big_','mid_',$bigpathname);
            $im2 -> save($midpathname);

            //返回大图、中图路径名给客户端hhuploadify处理
            return ['status'=>'success','bigpathname'=>$bigpathname,'midpathname'=>$midpathname];
        }else{
            //上传图片失败
            $errorinfo = $file -> getError();
            return ['status'=>'failure','errorinfo'=>$errorinfo];
        }
    }

    /**
     * 删除单个的已存在相册图片
     * @param Request $request
     */
    public function pics_del(Request $request)
    {
        //获得被删除相册的中图路径名
        $midpath = $request->post('midpath');
        //删除数据库记录
        GoodsPics::where('pics_mid',$midpath)->delete();

        //删除物理图片
        //获得大图图片路径名
        $bigpath = str_replace('mid_','big_',$midpath);
        unlink($midpath);
        unlink($bigpath);
        return ['status'=>'success'];
    }

    /**
     * 给商品做“促销”状态切换
     * @param Request $request
     */
    public function setPromotion(Request $request)
    {
        $data['goods_id']       = $request->post('goods_id');
        $data['is_promotion']   = $request->post('is_pro');

        if(Goods::update($data)){
            //把进入促销状态的商品存储到redis的set集合中goods_pro的key中
            //从促销进入非促销状态，要把集合中对应的商品删除
            $redis = get_redis_obj();  //获得redis操作对象
            $key = config('special_goods.pro');//获取key名字
            if($data['is_promotion']==1){
                //进入“促销”状态
                //存储商品(goods_id)到集合
                $redis -> sadd($key,$data['goods_id']);
            }else{
                //进入“非促销”状态
                $redis -> srem($key,$data['goods_id']);
            }


            return ['status'=>'success'];
        }else{
            return ['status'=>'failure'];
        }
    }
    /**
     * 给商品做“热卖数量”更新
     * @param Request $request
     */
    public function setSaleNum(Request $request)
    {
        $data['goods_id']       = $request->post('goods_id'); //商品id
        $data['goods_salenum']  = $request->post('sale_num'); //更新后热卖数量

        if(Goods::update($data)){

            //使得当前更新数量的商品goods_id进入到redis的排序集合中
            $redis = get_redis_obj();
            $key = config('special_goods.hot');//获得key名称
            //$redis -> zadd(key名称,权,值);
            $redis -> zadd($key,$data['goods_salenum'],$data['goods_id']);
            //判断集合元素个数如果大于4个，就要删除多余的，保持销量最高的4个商品元素
            if($redis->zCard($key)>4){
                //删除权值(销量)最小的1个元素(goods_id)
                $redis -> zRemRangeByRank($key,0,0);
            }

            return ['status'=>'success'];
        }else{
            return ['status'=>'failure'];
        }
    }
}


























