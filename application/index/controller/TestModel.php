<?php 
namespace app\index\controller;
use think\Controller;
use app\index\model\Data;
use think\Loader;
/**
 * 
 */
class TestModel extends Controller
{
	
	public function index()
	{
		// 使用模型的方法
		// 1.静态调用写法
	
		// 2.new的写法
		// $res = new Data;
		// $res = Data::get(51);

		// 3.可以加多个模型的写法
		// $res = Loader::Model("Data");
		// $res = $res::get(52);

		// 4.使用助手函数写法
		// $res = model("Data");
		// $res = $res::get(52);
		// 读取对象其中的数组值
		$res = $res->toArray();
		dump($res);
	}
}
 ?>

 grant all privileges on *.* to 'back'@'%' identified by "123";                                                         