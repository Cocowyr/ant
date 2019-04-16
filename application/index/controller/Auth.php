<?php
/**
 * Created by PhpStorm.
 * User: wujiajun
 * Date: 2018/11/14
 * Time: 14:53
 */

namespace app\index\controller;

use think\facade\Request;
use think\facade\Config;
use think\facade\Session;
use think\Loader;
use think\Db;
use think\Exception;


class Auth
{
    const  PATH = __DIR__;
    public $log = true;
    protected $request;
    protected $param;
    protected $module;
    protected $controller;
    protected $action;
    protected static $session_prefix;
    public $noNeedCheckRules = [];           //不需要检查的路由规则

    public function __construct()
    {
        $this->request        = Request::instance();
        $this->param          = $this->request->param();
        $this->module         = $this->request->module();
        $this->controller     = $this->request->controller();
        $this->action         = $this->request->action();
        self::$session_prefix = Config::get('muguache_session.session_prefix');

    }

    /**
     * 用户登入
     * @access private static
     * @param  array $userData 用户信息
     * @param  string $store_token 店铺token
     * @return bool
     */
    public static function login($userData)
    {
        if (empty($userData)) {
            return false;
        }
        try {
            Db::name('user')->where('id', $userData['id'])->update([
                'last_login_ip'   => get_ip(),
                'last_login_time' => date('Y-m-d H:i:s',time())
            ]);
        } catch (Exception $e) {
            return false;
        }

        return true;
    }

    /**
     * 检测用户是否登录
     * @return mixed
     */
    public static function is_login()
    {
        $user = self::sessionGet('userData');
        if (empty($user)) {
            return false;
        } else {
            return self::sessionGet('userDataSign') == self::data_auth_sign($user) ? $user : false;
        }
    }

    /**
     * 注销
     * @access private static
     * @return bool
     */
    public static function logout()
    {
        $session_prefix = self::$session_prefix;
        Session::delete($session_prefix . 'userData');
        Session::delete($session_prefix . 'userDataSign');
        return true;
    }


   
    /**
     * 重新索引
     * @param $data
     * @return array
     */
    protected static function reindex($data)
    {
        if (empty($data)) return [];
        $data = array_values($data);
        foreach ($data as $k => $v) {
            if (isset($v['children'])) {
                $data[$k]['children'] = self::reindex($v['children']);
            }
        }
        return $data;
    }


    /**
     * @param $menu array 菜单数组
     * @param $partnerMenu array 菜单父类
     * @return array
     */
    protected static function childrenMenu($menu, $partnerMenu)
    {
        $temp = [];
        for ($i = 0; $i < 4; $i++) {//最多四级分类
            foreach ($menu as $key => $val) {
                if (isset($partnerMenu[$val['pid']])) {
                    $temp[$val['id']] = $val['pid'];
                    $partnerMenu[$val['pid']]['children'][$val['id']] = $val;
                    unset($menu[$key]);
                } else if (isset($temp[$val['pid']])) {
                    //$partnerMenu[$val['pid']]['children'] = $val;
                    //查找主键
                    $pid = $temp[$val['pid']];//当前的父级
                    // $ppid                                                         = $temp[$pid];
                    $partnerMenu[$pid]['children'][$val['pid']]['children'][$val['id']] = $val;
                    unset($menu[$key]);
                }
            }
        }
        return $partnerMenu;
    }



    /**
     * 检查权限
     * @access protected
     * @param  string $url 路由
     * @return bool
     */
    protected function authCheck($url)
    {
        //检测路由是否有权限使用
        $url     = strtolower($url);
        $menuArr = Session::get(self::$session_prefix . 'authCheckDataCache');
        if (empty($menuArr)) return false;
        //return self::getAuthAll();
        if (in_array($url, $menuArr)) return true;
        return false;
    }

    /**
     * 缓存权限菜单
     * @param $data
     * @return bool |array
     */
    protected static function authCheckDataCache($data)
    {
        if (empty($data)) return false;
        $menuArr = [];
        foreach ($data as $k => $v) {
            $menuArr[]         = strtolower($v['app']) . '/' . strtolower($v['model']) . '/' . strtolower($v['action']);
            $data[$k]['route'] = ($v['app']) . '/' . ($v['model']) . '/' . ($v['action']);

        }
        Session::set(self::$session_prefix . 'authCheckDataCache', $menuArr);
        return $data;
    }


    /**
     * 读取session
     * @access private static
     * @param  string $path 被认证的数据
     * @return mixed
     */
    private static function sessionGet($path = '')
    {
        $session_prefix = self::$session_prefix;
        $user           = Session::get($session_prefix . $path);
        return $user;
    }


    /**
     * 数据签名认证
     * @access private static
     * @param  array $data 被认证的数据
     * @return string       签名
     */
    private static function data_auth_sign($data)
    {
        $code = http_build_query($data); //url编码并生成query字符串
        $sign = sha1($code); //生成签名
        return $sign;
    }
}