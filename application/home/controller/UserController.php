<?php

namespace app\home\controller;

use app\home\model\User;
use think\Controller;
use think\Request;
use think\Validate;

use Flc\Dysms\Client;
use Flc\Dysms\Request\SendSms;

use loveteemo\qqconnect\QC;

class UserController extends Controller {

    /**
     * QQ登录窗口展示效果
     * @param Request $request
     * @return \think\response\Redirect
     */
    public function qqlogin()
    {
        $qc = new QC();
        return redirect($qc->qq_login());
    }

    /**
     * QQ登录后逻辑处理实现
     * @return \think\response\Redirect
     */
    public function qqcallback(){
        $Qc = new QC();
        $access_token =  $Qc->qq_callback();
        $openid = $Qc->get_openid();
        //dump($openid);//string(32) "692819B85461DAB8320064F4718D4465"
        $Qc = new QC($access_token,$openid);
        $qq_user_info = $Qc->get_user_info();

        //判断当前用户登录次数
        $userinfo = User::where('openid',$openid)->find();
        $user = new User();  //实例化会员模型对象
        if($userinfo===null){
            //当前QQ第一次登录系统
            //给sp_user表insert生成对应的会员记录信息
            $shuju['user_name'] = $qq_user_info['nickname'];
            $shuju['openid'] = $openid;
            $user->save($shuju);    //返回新添加记录条数
            $user_id    = $user->user_id;  //新会员user_id
            $user_name  = $user->user_name;  //新会员user_name
        }else{
            //当前QQ第n次重复登录系统
            //给sp_user表update更新nickname昵称
            $user -> where('openid',$openid)->update(['user_name'=>$qq_user_info['nickname']]);
            //获得更新后的qq会员信息
            $upuser = User::find($userinfo->user_id);
            $user_id    = $upuser->user_id;
            $user_name  = $upuser->user_name; //直接获取“更新后”的昵称信息
        }

        //持久化用户信息
        session('user_id',$user_id);
        session('user_name',$user_name);

        //判断是否有回调地址再跳转
        $back_url = session('back_url');
        if(!empty($back_url)){
            session('back_url',null);//清除回调地址

            echo <<<eof
            <script type="text/javascript">
            opener.window.location.href="$back_url";
            window.close();
            </script>
eof;
        }else{
            //通过定界符方式输出一大段的js内容信息
            //父窗口跳转到首页
            //本身子窗口关闭
                echo <<<eof
            <script type="text/javascript">
            opener.window.location.href='/';
            window.close();
            </script>
eof;
        }



        //获得登录QQ帐号信息
        //dump($qq_user_info);die;
        // ...
        // 用户逻辑
        //return redirect(url('Index/index'));
    }

    
    /**
     * 用户输入手机校验码
     * @param Request $request
     * @return mixed
     */
    public function checktel(Request $request)
    {
        if($request->isPost()){
            $tel_code = $request->post('tel_code');  //获取客户端用户输入的校验码
            //让$tel_code 与 之前session中存储的code做比较，同时判断验证码是否过期

            //判断在规定时间之内使用短信校验码
            if(time()-session('send_time')<=session('send_time_in')*60){
                //手机 验证码 和 用户输入的保持一致判断
                if(session('send_code')==$tel_code){
                    //持久化用户信息，登录系统
                    $userinfo = session('userinfo');
                    //记录本次登录系统的时间
                    $userinfo ->update(['user_id'=>$userinfo->user_id,'login_time'=>time()]);

                    session('user_id',$userinfo->user_id);
                    session('user_name',$userinfo->user_name);

                    //判断是否有回调地址再跳转
                    $back_url = session('back_url');
                    if(!empty($back_url)){
                        session('back_url',null);//清除回调地址
                        $this->redirect($back_url);
                    }

                    //首页跳转
                    $this -> redirect('/');
                }else{
                    $this -> assign('send_status','success');
                    $this -> assign('errorinfo','验证码输入错误');
                }
            }else{
                $this -> assign('send_status','success');
                $this -> assign('errorinfo','验证码已经过期');
            }
        }else{
            //给用户手机发送校验码短信
            $tel  = session('userinfo.user_tel');
            $code = rand(100000,999999);
            $time = 3;  //分钟
            //把“校验码和时间”通过session存储起来，便于后期获取比较校验
            session('send_code',$code);  //校验码
            session('send_time',time());  //发送短信时间
            session('send_time_in',$time);  //验证码规定使用时间

            //发送验证码短信
            $rst = send_sms($tel,$code,$time);

            if($rst->Message=='OK'){
                $this -> assign('send_status','success');
            }else{
                $this -> assign('send_status','failure');
            }
        }
        return $this -> fetch();
    }

    /**
     * 发送手机验证码短信(测试)
     */
    public function sms()
    {
        //配置密钥
        $config = [
            'accessKeyId'    => 'LTAIoNi869qUGqUr',
            'accessKeySecret' => 'SZFxPSmjymwGeXb2H0jrcSIN4sGC0x',
        ];

        $client  = new Client($config);
        $sendSms = new SendSms;
        $sendSms->setPhoneNumbers('13521319995');  //接收短信手机号码
        $sendSms->setSignName('万马奔腾');  //设置签名
        $sendSms->setTemplateCode('SMS_135802968');  //模板设置
        //设置模板变量
        $sendSms->setTemplateParam(['code' => rand(100000, 999999),'time'=>4]);
        //设置发送短息序号的
        $sendSms->setOutId('demo'.time());

        print_r($client->execute($sendSms)); //发送短信
    }

    /**
     * 会员登录系统 [get/post]
     */
    public function login(Request $request)
    {
        if($request -> isPost()){
            //接收并校验 用户名和密码 信息
            $name = $request->post('user_name');
            $pwd  = md5($request->post('user_pwd'));

            //object or null
            $userinfo = User::where(['user_name'=>$name,'user_pwd'=>$pwd])->find();
            if($userinfo===null){
                //用户名、密码错误
                //回传form表单过来的信息到客户端
                $this -> assign('shuju',['user_name'=>$name,'user_pwd'=>$request->post('user_pwd')]);
                //把错误信息传递给客户端
                $this -> assign('errorinfo','用户名或密码错误');
            }
            elseif($userinfo->is_active=='否')
            {
                //邮件没有激活
                //回传form表单过来的信息到客户端
                $this -> assign('shuju',['user_name'=>$name,'user_pwd'=>$request->post('user_pwd')]);
                //把错误信息传递给客户端
                $this -> assign('errorinfo','帐号没有进行激活');
            }else{
                //超过3天没有登录系统，就要跳转到输入"手机校验码"页面去
                if((time()-$userinfo->login_time)>3600*24*3){
                    //给手机校验码页面传递需要的信息(手机号、user_id、user_name)，具体通过session传递
                    session('userinfo',$userinfo);

                    $this -> redirect('checktel');
                }else{
                    //持久化用户信息，登录系统
                    //记录本次登录系统的时间
                    $userinfo ->update(['user_id'=>$userinfo->user_id,'login_time'=>time()]);
                    session('user_id',$userinfo->user_id);
                    session('user_name',$userinfo->user_name);

                    //判断是否有回调地址再跳转
                    $back_url = session('back_url');
                    if(!empty($back_url)){
                        session('back_url',null);//清除回调地址
                        $this->redirect($back_url);
                    }

                    //首页跳转
                    $this -> redirect('/');
                }
            }
        }
        //获取展示视图view模板文件
        return $this -> fetch();
    }


    /**
     * 会员退出系统
     * @param Request $request
     */
    public function logout(Request $request)
    {
        session(null);//清空session
        $this -> redirect('user/login');//跳转到登录页面
    }

    /**
     * 会员注册
     */
    public function register(Request $request)
    {
        if($request -> isPost()){

            //对接收到的客户端form表单信息实现校验
            $rules = [
                'user_name' => 'require',
                'user_email' => 'require|email',
                'user_pwd'  => 'require|length:6,10',
                'user_pwd2'  => 'require|confirm:user_pwd',
                'user_tel'  => ['require','regex'=>'/^1[358769]\d{9}$/'],
            ];
            $notices = [
                'user_name.require'=>'帐号必填',
                'user_email.require'=>'邮件必填',
                'user_email.email'=>'邮件格式不正确',
                'user_pwd.require'=>'密码必填',
                'user_pwd.length'=>'密码内容长度要求6-10位之间',
                'user_pwd2.require'=>'确认密码必填',
                'user_pwd2.confirm'=>'两次密码输入不一样',
                'user_tel.require'=>'手机号码必填',
                'user_tel.regex'=>'手机号码格式不正确',
            ];

            $shuju = $request -> post();  //接收form表单信息

            $validate = new Validate($rules,$notices); //实例化校验对象
            if($validate->batch()->check($shuju)){ //校验数据
                //校验成功
                //存储入库
                $user = new User();
                $shuju['user_pwd'] = md5($shuju['user_pwd']);//给密码设置加密

                //生成一个唯一13位的校验信息码(同步给 数据库 和 邮件)，用于激活校验判断
                $shuju['verify_code'] = uniqid();

                $num = $user -> allowField(true)->save($shuju); //返回添加记录条数
                if($num){
                    //注册成功:
                    // ① 发送激活邮件
                    $to = $shuju['user_email'];
                    $title = "激活会员帐号";
                    $cont  = "<p>请点击以下超链接，激活《品优购网站》账号</p>";
                    $url = "http://www.php68.com/home/user/active/user_id/".$user->user_id."/checkcode/".$shuju['verify_code'];
                    $cont .= "<a href='$url' target='_blank'>$url</a>";
                    send_mail($to,$title,$cont); //发送邮件

                    // ② 页面跳转到登录页
                    return $this -> fetch('reg_success');
                    exit;
                }else{
                    //注册失败
                    echo "注册失败，请联系管理员";
                    exit();
                }
            }else{
                //校验失败
                $errorinfos = $validate->getError();
                //① 把错误信息传递给模板，通过“红色”高亮显示
                $this -> assign('errorinfos',$errorinfos);
                //② 把收集到的form表单信息再次传递给模板显示(避免ok的信息重复设置)
                $this -> assign('shuju',$shuju);
            }
        }
        return $this -> fetch();
    }


    /**
     * 邮件激活新会员帐号
     * @param Request $request
     * @return mixed
     */
    public function active(Request $request)
    {
        //激活新会员
        //获得会员id  和 激活码信息
        $user_id    = $request -> param('user_id');
        $checkcode  = $request -> param('checkcode');

        $userinfo = User::find($user_id); //获得被激活会员信息
        //判断邮箱激活码 与 数据库存储的 是否一致
        if($userinfo->verify_code == $checkcode){
            //让激活
            $userinfo -> update(['user_id'=>$userinfo->user_id,'is_active'=>'是','verify_code'=>'']);
            $this -> assign('status','success');
        }else{
            //非法激活
            $this -> assign('status','failure');
        }
        return $this -> fetch();
    }
}












