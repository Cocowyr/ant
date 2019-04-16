<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 流年 <liu21st@gmail.com>
// +----------------------------------------------------------------------

// 应用公共文件
// Cookie解密
function dCookie($string, $key = '')
{
    if (!$key) return '';
    $ckey_length = 4;

    $key  = md5($key);
    $keya = md5(substr($key, 0, 16));
    $keyb = md5(substr($key, 16, 16));
    $keyc = $ckey_length ? substr($string, 0, $ckey_length) : '';
/*  */
    $cryptkey   = $keya . md5($keya . $keyc);
    $key_length = strlen($cryptkey);

    $string        = base64_decode(substr($string, $ckey_length));
    $string_length = strlen($string);

    $result = '';
    $box    = range(0, 255);

    $rndkey = array();
    for ($i = 0; $i <= 255; $i++) {
        $rndkey[$i] = ord($cryptkey[$i % $key_length]);
    }

    for ($j = $i = 0; $i < 256; $i++) {
        $j       = ($j + $box[$i] + $rndkey[$i]) % 256;
        $tmp     = $box[$i];
        $box[$i] = $box[$j];
        $box[$j] = $tmp;
    }

    for ($a = $j = $i = 0; $i < $string_length; $i++) {
        $a       = ($a + 1) % 256;
        $j       = ($j + $box[$a]) % 256;
        $tmp     = $box[$a];
        $box[$a] = $box[$j];
        $box[$j] = $tmp;
        $result  .= chr(ord($string[$i]) ^ ($box[($box[$a] + $box[$j]) % 256]));
    }

    if ((substr($result, 0, 10) == 0 || substr($result, 0, 10) - time() > 0) && substr($result, 10, 16) == substr(md5(substr($result, 26) . $keyb), 0, 16)) {
        return substr($result, 26);
    } else {
        return '';
    }
}

// //权限检查
// function can($name)
// {
//     $user = session('user');
//     if($user['is_admin'] == 1)
//     {
//         return true;
//     }
//     else
//     {
//         $auth = new \Common\Common\CheyunAuth();
//         if($auth->check($name, $user['id']))
//         {
//             return true;
//         }
//         else
//         {
//             return false;
//         }
//     }
// }

//权限分组显示,模块ID对应相应的中文标题
function showModule($key)
{
    switch ($key) {
        case 1:
            return "商机管理";
            break;
        case 2:
            return "呼叫中心";
            break;
        case 3:
            return "分析工具";
            break;
        case 4:
            return "系统设置";
            break;
        case 5:
            return "个人设置";
            break;
        default:
            return "";
    }
}

//格式化手机号码
function maskCell($cell, $sign = '*')
{
    return substr($cell, 0, 3) . str_repeat($sign, 4) . substr($cell, -4);
}

//显示性别
function showGender($gender)
{
    switch ($gender) {
        case 1:
            return "男";
            break;
        case 2:
            return "女";
            break;
        default:
            return "保密";
    }
}

//显示经销商状态
function showDealerStatus($status, $begin = null, $end = null)
{
    if (strlen($begin) > 8 && strtotime($begin) > time()) {
        return "暂未开启";
    } else if (strlen($end) > 8 && strtotime($end) + 86400 < time()) {
        return "过期";
    } else {
        switch ($status) {
            case 0:
                return "申请中";
                break;
            case 1:
                return "正式使用";
                break;
            case 2:
                return "试用中";
                break;
            case 3:
                return "申请驳回";
                break;
            case 4:
                return "锁定状态";
                break;
            case 5:
                return "撤销";
                break;
            default:
                return "";
        }
    }
}


if (!function_exists('getallheaders')) {
    function getallheaders()
    {
        $headers = [];
        foreach ($_SERVER as $name => $value) {
            if (substr($name, 0, 5) == 'HTTP_') {
                $headers[str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', substr($name, 5)))))] = $value;
            }
        }
        return $headers;
    }
}
function curl_post($url, $curlPost)
{
    // $headers = array(
    //     'content-type:application/x-www-form-urlencoded'
    // );
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_HEADER, false);
    //curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);//不验证ssl证书
    curl_setopt($curl, CURLOPT_NOBODY, true);
    curl_setopt($curl, CURLOPT_POST, true);
    curl_setopt($curl, CURLOPT_POSTFIELDS, $curlPost);
    $return_str = curl_exec($curl);
    curl_close($curl);
    return $return_str;
}

function curl_post_www($url, $curlPost)
{
    // $headers = array(
    //     'content-type:application/x-www-form-urlencoded'
    // );
    $data = '';
    if (!empty($curlPost) && is_array($curlPost)) {
        foreach ($curlPost as $k => $v) {
            $data .= $k . '=' . $v . '&';
        }
    }
    $data = trim($data, '&');
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_HEADER, false);
    //curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);//不验证ssl证书
    curl_setopt($curl, CURLOPT_NOBODY, true);
    curl_setopt($curl, CURLOPT_POST, true);
    curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
    $return_str = curl_exec($curl);
    curl_close($curl);
    return $return_str;
}

function curl_get($url, $curl_data = '')
{
    if (!empty($curl_data) && is_array($curl_data)) {
        $url .= '?';
        foreach ($curl_data as $k => $v) {
            $url .= $k . '=' . $v . '&';
        }
    }
    $url = rtrim($url, '&');
    //初始化
    $curl = curl_init();
    //设置抓取的url
    curl_setopt($curl, CURLOPT_URL, $url);
    //设置头文件的信息作为数据流输出
    curl_setopt($curl, CURLOPT_HEADER, false);
    //设置获取的信息以文件流的形式返回，而不是直接输出。
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);//不验证ssl证书
    //超时退出
    curl_setopt($curl, CURLOPT_TIMEOUT, 15);
    curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 15);
    //执行命令
    $data = curl_exec($curl);
    //关闭URL请求
    curl_close($curl);
    //显示获得的数据
    return $data;
}

/**
 * @param $data string
 * @param $dirPath string 文件路径
 * @param $type bool 日期 true Y-m-d false Y-m
 */
function conLog($data, $dirPath = '', $type = false)
{
    if (empty($dirPath)) $dirPath = getcwd() . "/www_log/defaultLog";
    if (!is_dir($dirPath)) mkdir($dirPath, 0777, true);
    $log = '';
    $log .= date('Y-m-d H:i:s') . "\t";
    $log .= $data . PHP_EOL;
    if ($type) $filename = date("Y-m-d") . '.log'; else $filename = date("Y-m") . '.log';
    $filePath = $dirPath . '/' . $filename;
    file_put_contents($filePath, $log, FILE_APPEND);
}

/**
 * 获取UUID
 * @return string
 */
function create_uuid()
{
    $str = md5(uniqid(mt_rand(), true));
    return strtoupper($str);
}

function return_code($code, $msg, $data = null)
{
    if ($data === null) {
        return json(['code' => $code, 'msg' => $msg]);
    } else {
        return json(['code' => $code, 'msg' => $msg, 'data' => $data]);
    }

}

function error_html()
{
    return redirect('http://www.mayinvwang.com');
}

function empty_return()
{
    return json(['code' => 404, 'msg' => '未找到此页面']);
}

function out3($data, $type = 1)
{
    if ($type !== 1) {
        echo '<pre>';
        print_r($data);
        exit;
    }
    var_export($data);
    exit;
}


function get_ip()
{
    //判断服务器是否允许$_SERVER
    if (isset($_SERVER)) {
        if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $realIp = $_SERVER['HTTP_X_FORWARDED_FOR'];
        } elseif (isset($_SERVER['HTTP_CLIENT_IP'])) {
            $realIp = $_SERVER['HTTP_CLIENT_IP'];
        } else {
            $realIp = $_SERVER['REMOTE_ADDR'];
        }
    } else {
        //不允许就使用getenv获取
        if (getenv("HTTP_X_FORWARDED_FOR")) {
            $realIp = getenv("HTTP_X_FORWARDED_FOR");
        } elseif (getenv("HTTP_CLIENT_IP")) {
            $realIp = getenv("HTTP_CLIENT_IP");
        } else {
            $realIp = getenv("REMOTE_ADDR");
        }
    }
    return $realIp;
}

/**
 * 修改数组的key
 * @param array $arr
 * @param string $key
 * @return array|bool
 */
function changeArrayKey($arr, $key)
{
    if (!is_array($arr) or empty($arr)) return false;
    $data = [];
    foreach ($arr as $k => $v) {
        $data[$v[$key]] = $v;
    }
    if (empty($data)) return false;
    return $data;
}

function getRangeArry($index)
{
    switch ($index) {
        case 0:
            //不到店原因 1价格贵 2去其他4S店 3去其他修理店 4未到保养时间 5会考虑 6客户主动取消 7路太远 8联系不上 9车已转卖 10异地用车 11其他
            return [
                1  => '价格贵',
                2  => '去其他4S店',
                3  => '去其他修理店',
                4  => '未到保养时间',
                5  => '会考虑',
                6  => '客户主动取消',
                7  => '路太远',
                8  => '联系不上',
                9  => '车已转卖',
                10 => '异地用车',
                11 => '其他',
            ];
        case 1:
            //不到店车龄 1 1-2年 2 2-3年 3 3-5年 4 5-10年 5 10年以上
            return [
                1 => '1-2年',
                2 => '2-3年',
                3 => '3-5年',
                4 => '5-10年',
                5 => '10年以上',
            ];
        case 2:
            //不到店车系 1探歌 2捷达 3宝来 4速腾 5迈腾 6高尔夫
            return [
                1 => '探歌',
                2 => '捷达',
                3 => '宝来',
                4 => '速腾',
                5 => '迈腾',
                6 => '高尔夫',
                7 => '其他',
            ];
        case 3:
            //跟进中，预约时间选择的时间类型
            return [
                1  => 'time_800',
                2  => 'time_830',
                3  => 'time_900',
                4  => 'time_930',
                5  => 'time_1000',
                6  => 'time_1030',
                7  => 'time_1100',
                8  => 'time_1130',
                9  => 'time_1200',
                10 => 'time_1230',
                11 => 'time_1300',
                12 => 'time_1330',
                13 => 'time_1400',
                14 => 'time_1430',
                15 => 'time_1500',
                16 => 'time_1530',
                17 => 'time_1600',
                18 => 'time_1630',
                19 => 'time_1700',
                20 => 'time_1730',
                21 => 'time_1800',
            ];
        case 4:
            //跟进中，预约时间选择的时间类型
            return [
                1  => '8:00',
                2  => '8:30',
                3  => '9:00',
                4  => '9:30',
                5  => '10:00',
                6  => '10:30',
                7  => '11:00',
                8  => '11:30',
                9  => '12:00',
                10 => '12:30',
                11 => '13:00',
                12 => '13:30',
                13 => '14:00',
                14 => '14:30',
                15 => '15:00',
                16 => '15:30',
                17 => '16:00',
                18 => '16:30',
                19 => '17:00',
                20 => '17:30',
                21 => '18:00',
            ];
        case 5:
        default:
            return "";
    }
}


/**
 * 将查询数据中Null改为''空字符串
 * @param $rows array 数据库查询出来的集合
 * @return mixed 返回转换后的集合
 */
function convertNull($rows)
{
    //预约状态 1预约到店 2不到店 3待招揽 4已到店
    $recruitState = [
        1 => '预约到店',
        2 => '不到店',
        3 => '待招揽',
        4 => '已到店',
    ];
    //客户意愿 1预约到店 2不到店
    $customerDesire = [
        1 => '预约到店',
        2 => '不到店',
    ];

    //跟进方式 1电话  2短信 3微信 4现场到店 5其他
    $followMode = [
        1 => '电话',
        2 => '短信',
        3 => '微信',
        4 => '现场到店',
        5 => '其他',
    ];
    //预约状态 1待确认预约 2已确认预约
    $reserveState = [
        1 => '待确认预约',
        2 => '已确认预约',
    ];
    //不到店原因 1价格贵 2去其他4S店 3去其他修理店 4未到保养时间 5会考虑 6客户主动取消 7路太远 8联系不上 9车已转卖 10异地用车 11其他
    $noReason = getRangeArry(0);
    for ($i = 0; $i < count($rows); $i++) {
        $arry = $rows[$i];
        foreach ($arry as $key => $val) {

            if ($key == 'chRecruitState') {
                if ($val) {
                    $rows[$i][$key] = $recruitState[$val];
                    if ($val == 1) {
                        $rows[$i][$key] = $reserveState[$rows[$i]['reserveState']];
                    }
                }
            }
            if ($key == 'chCustomerDesire') {
                if ($val)
                    $rows[$i][$key] = $customerDesire[$val];
            }
            if ($key == 'chFollowMode') {
                if ($val)
                    $rows[$i][$key] = $followMode[$val];
            }
            if ($key == 'chReserveState') {
                if ($val)
                    $rows[$i][$key] = $reserveState[$val];
            }
            if ($key == 'chNoReason') {
                if ($val)
                    $rows[$i][$key] = $noReason[$val];
            }
            if ($key == 'maintainExpireDate') {
                if ($val)
                    $rows[$i][$key] = date('Y-m-d', strtotime($val));
            }
            if ($key == 'model_name' && $val) {
                $rows[$i]['va6MC'] = $val;
            }
            if ($key == 'va6MC' && !$val) {
                continue;
            }
            if (!$val) {
                $rows[$i][$key] = "";
            }

        }
    }
    return $rows;
}

function arryIsNull($arry)
{
    foreach ($arry as $key => $val) {
        if (!$val) {
            $arry[$key] = null;
        }
    }
    return $arry;
}


/**
 * 将时间加1天
 * @param $date string 时间
 * @return false|string 返回的是Y-m-d格式
 */
function getDatePlusOne($date)
{
    return date("Y-m-d", strtotime("+1 day", strtotime($date)));
}

/**
 * 获取两日期天数之差，忽略时分秒
 * @param $Date_1 string  较大的日期
 * @param $Date_2 string  较小的日期
 * @return float 返回的相差天数
 */
function getTowDateDifferenceDay($Date_1, $Date_2)
{
    if (!$Date_1 || !$Date_2) {
        return null;
    }
    $d1       = strtotime($Date_1);
    $d2       = strtotime($Date_2);
    $timetamp = strtotime(date("Y-m-d", $d1)) - strtotime(date("Y-m-d", $d2));
    $Days     = round($timetamp / 86400);
    return abs($Days);
}

/**
 * 获取两日期月份之差，忽略天数时分秒
 * @param $Date_1 string  较大的日期
 * @param $Date_2 string  较小的日期
 * @return float 返回的相差天数
 */
function getTowDateDifferenceMonth($Date_1, $Date_2)
{
    if (!$Date_1 || !$Date_2) {
        return null;
    }
    $d1       = strtotime($Date_1);
    $d2       = strtotime($Date_2);
    $timetamp = strtotime(date("Y-m", $d1)) - strtotime(date("Y-m", $d2));
    $Days     = round($timetamp / 86400 / 30);
    return abs($Days);
}

/**
 * @param $a mixed 第一个数
 * @param $b mixed 第二个数
 * @return mixed 最小的数
 */
function getMinNum($a, $b)
{
    return $a > $b ? $b : $a;
}

function getYearDays($year)
{
    return date('z', mktime(23, 59, 59, 12, 31, $year)) + 1;
}

/*

*处理Excel导出

*@param $datas array 设置表格数据

*@param $titlename string 设置head

*@param $title string 设置表头

*/

function excelData($datas, $titlename, $filename)
{

    $str = "<html xmlns:o=\"urn:schemas-microsoft-com:office:office\"\r\nxmlns:x=\"urn:schemas-microsoft-com:office:excel\"\r\nxmlns=\"http://www.w3.org/TR/REC-html40\">\r\n<head>\r\n<meta http-equiv=Content-Type content=\"text/html; charset=utf-8\">\r\n</head>\r\n<body>";

    $str .= "<table border=1>" . $titlename;
    foreach ($datas as $key => $rt) {

        $str .= "<tr>";

        foreach ($rt as $k => $v) {

            $str .= "<td style='text-align: center;font-size:13px;'>{$v}</td>";

        }

        $str .= "</tr>\n";

    }

    $str .= "</table></body></html>";

    header("Content-Type: application/vnd.ms-excel; name='excel'");

    header("Content-type: application/octet-stream");

    header("Content-Disposition: attachment; filename=" . $filename);

    header("Cache-Control: must-revalidate, post-check=0, pre-check=0");

    header("Pragma: no-cache");

    header("Expires: 0");

    exit($str);
}

/**
 * @param $url string 访问的URL
 * @param string $post post数据(不填则为GET)
 * @param string $cookie 提交的$cookies
 * @param int $returnCookie 是否返回$cookies
 * @return mixed|string
 */
function curl_request($url, $post = '', $cookie = '', $returnCookie = 0)
{
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_USERAGENT, 'Mozilla/5.0 (compatible; MSIE 10.0; Windows NT 6.1; Trident/6.0)');
    curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1);
    curl_setopt($curl, CURLOPT_AUTOREFERER, 1);
    curl_setopt($curl, CURLOPT_REFERER, "http://XXX");
    if ($post) {
        curl_setopt($curl, CURLOPT_POST, 1);
        curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($post));
    }
    if ($cookie) {
        curl_setopt($curl, CURLOPT_COOKIE, $cookie);
    }
    curl_setopt($curl, CURLOPT_HEADER, $returnCookie);
    curl_setopt($curl, CURLOPT_TIMEOUT, 10);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    $data = curl_exec($curl);
    if (curl_errno($curl)) {
        return curl_error($curl);
    }
    curl_close($curl);
    if ($returnCookie) {
        list($header, $body) = explode("\r\n\r\n", $data, 2);
        preg_match_all("/Set\-Cookie:([^;]*);/", $header, $matches);
        $info['cookie']  = substr($matches[1][0], 1);
        $info['content'] = $body;
        return $info;
    } else {
        return $data;
    }
}

/**
 * 退出登录 跳转到登录页
 * @param bool $bool
 * @param string $model
 * @return \think\response\Redirect
 */
function outLogin($bool = false, $model = 'index')
{
    if (!$bool) {
        return redirect(url('Login/index', '', true, true));
    } else {
        return redirect(url("{$model}/Login/index", '', true, true));
    }
}

/**
 * @param $startTime
 * @param $endTime
 * @param $field
 * @return array
 */
function whereTime($startTime, $endTime, $field)
{
    if (strlen($endTime) == 10) $endTime .= ' 23:59:59';
    if (strlen($startTime) == 10) $startTime .= ' 00:00:00';

    $data = [];
    if (!empty($startTime) and !empty($endTime)) {
        $data = [$field, 'between', [$startTime, $endTime]];
        return $data;
    }
    if (!empty($startTime)) {
        $data = [$field, '>', $startTime];
        return $data;
    }
    if (!empty($endTime)) {
        $data = [$field, '<', $endTime];
        return $data;
    }
    return $data;
}

/**
 * @param $str
 * @return bool
 */
function ifChinese($str)
{
    if (preg_match('/[，。：；【】、《》？！]/', $str) > 0) {
        return true;
    }
    if (preg_match('/[\x{4e00}-\x{9fa5}]/u', $str) > 0) {
        return true;
    }
    return false;
}

/**
 * 获取7天之前的日期
 * 如2019-04-20 - 2019-04-26
 * @param $date1
 * @return false|string
 */
function getLastWeekDate($date1)
{
    $date2 = date('Y-m-d', strtotime("$date1 -1 week"));
    $date3 = date('Y-m-d', strtotime("$date2 +1 days"));
    return $date3;
}

/**
 * 获取可临时访问的url
 * @param $url
 * @return mixed
 */

function getOssUrl($url)
{
    $uploadImg = \think\facade\App::controller('UploadImg');
    $url       = $uploadImg->getSignUrl($url);
    return $url;
}

/**
 * 数组中的值转为小写
 * @param $arr
 * @return array
 */
function arrayToStrLower($arr)
{
    if (empty($arr)) return [];
    $str = implode(',', $arr);
    $str = strtolower($str);
    $arr = explode(',', $str);
    return $arr;
}

function deepMenuCheck($route, $menuAll)
{
    $is_check = false;
    foreach ($menuAll as $k => $v) {
        if (!$is_check) {
            $res = deep_in_array($route, $v);
            if ($res) {
                $is_check               = true;
                $menuAll[$k]['is_show'] = 1;
            } else $menuAll[$k]['is_show'] = 0;
        } else $menuAll[$k]['is_show'] = 0;
    }
    return $menuAll;
}

/**
 *
 * @param $value
 * @param $array
 * @return bool
 */
function deep_in_array($value, $array)
{
    foreach ($array as $item) {
        if (!is_array($item)) {
            $item  = strtolower($item);
            $value = strtolower($value);
            if ($item === $value) {
                return true;
            } else {
                continue;
            }
        }
        if (is_array($item)) {
            if (deep_in_array($value, $item)) {
                return true;
            }
        }
    }
    return false;
}