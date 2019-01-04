<?php
namespace app\index\controller;

use think\Db;
use think\Request;
use app\index\model\Sms;
use app\index\model\User;
use app\index\model\Order;
use alipay\alipaynotify;
use think\Cookie; 
class Api extends \think\Controller
{

    public function jack()
    {
        // 获取当前请求的所有变量（经过过滤）
        // dump(input('')); 
        // exit();
 
        $alipay_config['partner']      = '208888888888888';

        //收款支付宝账号，一般情况下收款账号就是签约账号
        $alipay_config['seller_email']  = '250285636@qq.com';

        //安全检验码，以数字和字母组成的32位字符
        $alipay_config['key']           = 'ynccccccccccccccccccccccj7h';


        //↑↑↑↑↑↑↑↑↑↑请在这里配置您的基本信息↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑


        //签名方式 不需修改
        $alipay_config['sign_type']    = strtoupper('MD5');

        //字符编码格式 目前支持 gbk 或 utf-8
        $alipay_config['input_charset']= strtolower('utf-8');

        //ca证书路径地址，用于curl中ssl校验
        //请保证cacert.pem文件在当前文件夹目录中
        $alipay_config['cacert']    = getcwd().'\\cacert.pem';

        //访问模式,根据自己的服务器是否支持ssl访问，若支持请选择https；若不支持请选择http
        $alipay_config['transport']    = 'http';
        // $b = new return_url();



        // $b = new alipaynotify($alipay_config);
        // $foo = new \first\second\Foo();
        //计算得出通知验证结果
        $alipayNotify = new AlipayNotify($alipay_config);
        $verify_result = $alipayNotify->verifyReturn();

        $result = '';

        if($verify_result) {//验证成功
    /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    //请在这里加上商户的业务逻辑程序代码
    
    //——请根据您的业务逻辑来编写程序（以下代码仅作参考）——
    //获取支付宝的通知返回参数，可参考技术文档中页面跳转同步通知参数列表
    
    //商户订单号

    $out_trade_no = $_GET['out_trade_no'];

    //支付宝交易号

    $trade_no = $_GET['trade_no'];

    // echo "您的订单号是：" .$trade_no;



    //交易状态
    $trade_status = $_GET['trade_status'];


    if($_GET['trade_status'] == 'TRADE_FINISHED' || $_GET['trade_status'] == 'TRADE_SUCCESS') {
        //判断该笔订单是否在商户网站中已经做过处理
            //如果没有做过处理，根据订单号（out_trade_no）在商户网站的订单系统中查到该笔订单的详细，并执行商户的业务程序
            //如果有做过处理，不执行商户的业务程序
        $result = "支付成功";

         // 登记订单信息

        $phone               = Cookie::get('phone');
        $body                = input('param.body');
        $subject             = input('param.subject');
        $total_fee           = input('param.WIDtotal_fee');
        $buyer_id            = input('param.buyer_id');
        $buyer_email         = input('param.buyer_email');
        $total_fee           = input('param.total_fee');
        $out_trade_no        = input('param.out_trade_no');
            
            // dump($out_trade_no);        
                    $order = Order::create([
                        'phone'                   =>  $phone,
                        'body'                      =>  $body,
                        'subject'                  =>  $subject,
                        'total_fee'               =>  $total_fee,
                        'buyer_id'               =>  $buyer_id,
                        'buyer_email'        =>  $buyer_email,
                        'out_trade_no'      =>  $out_trade_no,
                    ]);

                    
                     // 如果是vip用户，设置vip字段
                    if ($body==105) {

                        // dump($body);
                        //             die();
                        User::where('phone', $phone)
                                    ->update(['rand' => 105]);

                                                             
                    }  
                    
                   
                    
                    

                    //重定向到用户购买的商品结果页面
                    $this->redirect('index/view', ['id' => $body]);

    }
    else {
      echo "trade_status=".$_GET['trade_status'];
    }
        
    // echo "购买成功了...<br />";

    

         

    //——请根据您的业务逻辑来编写程序（以上代码仅作参考）——
    
    /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
}
else {
    //验证失败
    //如要调试，请看alipay_notify.php页面的verifyReturn函数
    // echo "付款失败";
    $result = "支付失败";


    




}

// 模板变量赋值
        $this->assign('result',$result);
// 渲染模板输出
        return $this->fetch();
    }

    public function sms()
    {

        // $tom  = '18210787405'; 
        header("content-type:text/html; charset=utf-8"); 
        $tom  = input('s');
        $rand = rand(1000,9999);
        $cha  = 'http://api.chanyoo.cn/utf8/interface/send_sms.aspx?username=guomengtao1&password=998877&content=验证码：'.$rand.'【高血压】&receiver='.$tom ; 

        // $cha = 'http://www.baidu.com';


        $fp = file_get_contents($cha); 

        // echo $fp;
        // dump($fp);

        // die();

        
 
        //转xml为数组形式
        $xml = simplexml_load_string($fp);
        $data = json_decode(json_encode($xml),TRUE);

        dump($data);

        // die();
        // echo $tom;
        
        echo $data['message'];

        echo $data['result'];

        // die();
         
        if ($data['message']='短信提交成功') {

        // if ($data['result']>=0) {
            # code...
            echo "(";

            // 模型的 静态方法 
            // 存入短信发送日志表
               $user        = Sms::create([
                'phone'   =>  $tom,
                'rand'       =>  $rand
            ]);
            // 创建会员信息
            User::create([
                'phone'   =>  $tom
            ]);
            echo "::::";
        }
        // return $this->fetch();

        // 短信入库完成
    }
    public function alipay()
    {
         
        return $this->fetch();
    }
    public function alipayReturnUrl()
    {
        import('alipay/tom.php'); 
        require("alipay/tom.php");
        require("alipay/tom.php");
        require("alipay/lib/alipay_notify.class.php");



        exit();


        require("alipay/alipay.config.php");
        require("alipay/lib/alipay_core.function.php");
        require("alipay/lib/alipay_md5.function.php");
        require("alipay/lib/alipay_notify.class.php");


        // 计算得出通知验证结果
    $alipayNotify = new AlipayNotify($alipay_config);
    $verify_result = $alipayNotify->verifyReturn();
    if($verify_result) {//验证成功
    /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    //请在这里加上商户的业务逻辑程序代码
    
    //——请根据您的业务逻辑来编写程序（以下代码仅作参考）——
    //获取支付宝的通知返回参数，可参考技术文档中页面跳转同步通知参数列表

    //商户订单号

    $out_trade_no = $_GET['out_trade_no'];

    //支付宝交易号

    $trade_no = $_GET['trade_no'];

    echo "您的订单号是：" .$trade_no;



    //交易状态
    $trade_status = $_GET['trade_status'];


    if($_GET['trade_status'] == 'TRADE_FINISHED' || $_GET['trade_status'] == 'TRADE_SUCCESS') {
        //判断该笔订单是否在商户网站中已经做过处理
            //如果没有做过处理，根据订单号（out_trade_no）在商户网站的订单系统中查到该笔订单的详细，并执行商户的业务程序
            //如果有做过处理，不执行商户的业务程序
    }
    else {
      echo "trade_status=".$_GET['trade_status'];
    }
        
    echo "购买成功了...<br />";

    //——请根据您的业务逻辑来编写程序（以上代码仅作参考）——
    
    /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    }
    else {
        //验证失败
        //如要调试，请看alipay_notify.php页面的verifyReturn函数
        echo "付款失败";
    }

     
      
 
        // return $this->fetch();
    }
    public function demo(){

            dump("演示一下 api跨域访问");

            $url = "http://open.gaoxueya.com/tp5/public/index.php/index/bbs/add";
            // $url = file_get_contents($url);

            // echo $url ;

            // exit();

            $data=array(
            "title" => "用机器人来发帖了,我来采集你的内容了",
            "content" => "加个验证码吧，不然被攻击了"
            );

            // 1. 初始化
            $ch = curl_init(); 

            // 2. 设置选项，包括URL

            // 指定请求的URL；
            curl_setopt($ch, CURLOPT_URL, $url); 
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 60); 
            curl_setopt($ch, CURLOPT_POSTFIELDS , http_build_query($data));
            // 返回字符串
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 

            // 3. 执行并获取HTML文档内容
            $output = curl_exec($ch); 

            // 4. 释放curl句柄
            curl_close($ch);      
         

            //echo $output;

       

    }
    public function weibo(){
    

        

// 微博登录官方开发步骤
// http://open.weibo.com/wiki/Connect/login

// 微博登录官方获取信息接口
// http://open.weibo.com/wiki/2/users/show

// 老师的临时演示地址：
// http://open.gaoxueya.com/tp5/public/index/api/weibo?code=6555d1d93a8b9630c1fc2302bdc9e34f 

        $code  = input('code');

        //echo $code;

         $tom =   "https://api.weibo.com/oauth2/access_token?client_id=2078783153&client_secret=249bc84acffd4d59335021f1e4865707&grant_type=authorization_code&redirect_uri=http://open.gaoxueya.com/tp5/public/index/api/weibo&code=" .$code ;
         
         

          // post获取开始 获取重要的唯一ID钥匙-访问令牌
         header("Content-Type:text/html;charset=utf-8");
         $url = $tom;
         //echo $url.'<br />';

         $curl = curl_init();
         curl_setopt($curl, CURLOPT_URL, $url);
         curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
         curl_setopt($curl, CURLOPT_POST, TRUE); 
         curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
         curl_setopt($curl, CURLOPT_USERPWD, "username:password");
         $data = curl_exec($curl);
         curl_close($curl);

         $result = json_decode($data, true);

         //echo '<pre>';
         //print_r($result);
         //echo '</pre>';
         
         // exit();
         

        $access_token = $result['access_token'];
        $uid = $result['uid'];
        //echo $access_token;

        // 继续获得用户信息 - 开始 方法一
        $tom = "https://api.weibo.com/2/users/show.json?access_token=". $access_token ."&uid=" . $uid;

        // 使用file方法
        // $domain = 'Rinuo.com'; 
        // $cha = 'http://panda.www.net.cn/cgi-bin/check.cgi?area_domain='.$domain ; 
        $data = file_get_contents($tom,'rb'); 
        //dump($data);

        $data = json_decode($data, true);
 
        //$xml = simplexml_load_string($fp);
        //$data = json_decode(json_encode($xml),TRUE);

        //dump($data);



        //exit();

        
        // 模板变量赋值
        $this->assign('data',$data);

        // 渲染模板输出
        return $this->fetch();
    }
    public function domain(){
       


        $domain = 'Rinuo.com'; 
        $cha = 'http://panda.www.net.cn/cgi-bin/check.cgi?area_domain='.$domain ; 
        $fp = file_get_contents($cha,'rb'); 
        //dump($fp);

        
 

        $xml = simplexml_load_string($fp);
        $data = json_decode(json_encode($xml),TRUE);

        //dump($data);
        //exit();
        // 模板变量赋值
        $this->assign('data',$data);

        // 渲染模板输出
        return $this->fetch();

    }

}
 