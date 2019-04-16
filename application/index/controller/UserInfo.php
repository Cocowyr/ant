<?php 
namespace app\index\controller;
use think\Controller;
use think\Db;
use think\Request;
//use think\Validate;

/**
 * 
 */
class Userinfo  extends Controller
{
	

	public function register()
	{
		$Request = new Request();
		$phone    = $Request->post('phone');
	    $pwd = $Request->post('password');
        $rePwd = $Request->post('rePassword');
		$res = Db::name('user')->where('phone',$phone)->select();
        if ($res) {
            return return_code(205,'此号码已注册过');
        }
      	$data = [
            'phone'  => $phone,
            'password' => $pwd,
        ];

        $validate = new \app\index\validate\User;

        if (!$validate->check($data)) {
            //dump($validate->getError());
            return return_code(201, $validate->getError());
        }
        if ($pwd !== $rePwd) {
        	return return_code(202, '密码输入不一致');
        }
		$token     = create_uuid();
        $password = strtoupper(md5($pwd . $token));
        $insertUser    = [
            'password' => $password,
            'code'     => $token,
            'phone'    => $phone,
            'token'    => create_uuid(),
            'last_login_ip' => get_ip(),
            'last_login_time' => date('Y-m-d H:i:s',time())
        ];
        //$result = Db::table('think_user')->insert($insertUser);
        $result = Db::name('user')->insert($insertUser);
      	if ($result) {
      		return return_code(200, '注册成功');
      	}else{
      		return return_code(204, '注册失败');
      	}
	}
	public function login(){
		$Request = new Request();
	
		$phone    = $Request->post('phone');
        $password = $Request->post('password');
        if (empty($phone) or empty($password)) return return_code(201, '缺少数据');
        if (strlen($phone) !== 11) return return_code(210, '请输入正确手机号');
        try {
            $user = Db::name('user')->where('phone', $phone)->find();
        } catch (Exception $e) {
            return return_code(500, '系统错误');
        }
        if (empty($user)) return return_code(212, '手机号未注册');
        //验证密码是否正确
        $code        = $user['code'];
        $newPassword = strtoupper(md5($password . $code));
        if ($user['password'] != $newPassword) return return_code(211, '密码错误,请重新输入');

        if ($user['status'] == 0) return return_code(300, '此账号已被冻结');
       
        $is_login = Auth::login($user);

        if ($is_login) return return_code(200, '登录成功');
        return return_code(400, '登录失败');
	}

}







 ?>