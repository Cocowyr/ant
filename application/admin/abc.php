<?php
namespace app\index\controller;
use think\Db;
class Index
{
    public function index()
    {
    	// $res = Db::name('think_data')->find();
    	// return json($res);
        

        return '<style type="text/css">*{ padding: 0; margin: 0; } div{ padding: 4px 48px;} a{color:#2E5CD5;cursor: pointer;text-decoration: none} a:hover{text-decoration:underline; } body{ background: #fff; font-family: "Century Gothic","Microsoft yahei"; color: #333;font-size:18px;} h1{ font-size: 100px; font-weight: normal; margin-bottom: 12px; } p{ line-height: 1.6em; font-size: 42px }</style><div style="padding: 24px 48px;"> <h1>:) </h1><p> ThinkPHP V5.1<br/><span style="font-size:30px">12载初心不改（2006-2018） - 你值得信赖的PHP框架</span></p></div><script type="text/javascript" src="https://tajs.qq.com/stats?sId=64890268" charset="UTF-8"></script><script type="text/javascript" src="https://e.topthink.com/Public/static/client.js"></script><think id="eab4b9f840753f8e7"></think>';
       
    }

    public function hello($name = 'ThinkPHP5')
    {
        return 'hello,' . $name;
    }
    public function insert()
    {
    	// 插入记录
       
        // 原生
		//$result = Db::execute('insert into think_data (id, name ,status) values (5, "thinkphp",1)');
		//dump($result);
		//表名
		//Db::table('think_data')->insert(['name' => 'xxx', 'status' => 1]);
		//name 去掉表前最
		//Db::name('data')->insert(['name' => '王雅蓉', 'status' => 2]);
		$data = [['name' => 'aaa'],['name' => 'bbb'],['name' => 'ccc'],['name' => 'ddd']] ;
		$db = db('data');
		//$res = $db->insertGetId(['name' => 'test']);
		//$res1 = DB::name('data')->insertAll($data);
		$res1 = $db->insertAll($data);
		dump($res1); 
 		
    }
    public function update()
    {
    	//原生
    	$res = Db::execute('update think_data set name = "ooo" where id = 8');
    	//dump($res);
    	echo "<h1> 更新</h1>";

    }
}
