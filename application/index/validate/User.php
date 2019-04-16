<?php 

namespace app\index\validate;

use think\Validate;

class User extends Validate
{
    protected $rule = [
     	'phone' => 'number|length:11',
        'password' => 'length:6,30',
    ];

     protected $message = [
        'phone.number' => '手机号必须是数字',
        'phone.max'    => '手机号错误',
        'phone.min'    => '手机号错误',
    ];

}
 ?>