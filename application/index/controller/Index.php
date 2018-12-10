<?php
namespace app\index\controller;

use think\Db;
class Index extends \think\Controller
{   
    
    public function index()
    {
    	// $res = Db::name('think_data')->find();
    	// return json($res);
        return '<style type="text/css">*{ padding: 0; margin: 0; } div{ padding: 4px 48px;} a{color:#2E5CD5;cursor: pointer;text-decoration: none} a:hover{text-decoration:underline; } body{ background: #fff; font-family: "Century Gothic","Microsoft yahei"; color: #333;font-size:18px;} h1{ font-size: 100px; font-weight: normal; margin-bottom: 12px; } p{ line-height: 1.6em; font-size: 42px }</style><div style="padding: 24px 48px;"> <h1>:) </h1><p> ThinkPHP V5.1<br/><span style="font-size:30px">12载初心不改（2006-2018） - 你值得信赖的PHP框架</span></p></div><script type="text/javascript" src="https://tajs.qq.com/stats?sId=64890268" charset="UTF-8"></script><script type="text/javascript" src="https://e.topthink.com/Public/static/client.js"></script><think id="eab4b9f840753f8e7"></think>';    
    }
    /**
     *  模版
     */
    public function temp()
    {    
        // 模版变量赋值
        $this->assign('name', '<h1>模版</h1>qaq');
        $this->assign('email', '123456789@qq.com');
        // 批量赋值
        $this->assign([
            'name' => '<h1>模版</h1>abc',
            'email' => '98765432@qq.com'
        ]);
        // 数组赋值
        $data['name'] = 'data_name';
        $data['email'] = 'data_1235467@qq.com';
        $this->assign('data',$data);
        $list = Db::name('data')->where('id',20)->select();
        $this->assign('list',$list);
        // 模版输出 - 制定模版文件名字
        return $this->fetch('index');
        // 不带任何参数 自动定位当前操作的模板文件
        //return $this->fetch();
    }

    public function hello($name = 'ThinkPHP5')
    {
        b::name('data')->insert(['name' => $name]);
        return 'hello,' . $name;
    }

    public function handleSql($name = 'ThinkPHP5')
    {
        Db::name('data')->insert(['name' => $name]);
        echo 'hello,' . $name;
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
        echo "<h1>添加</h1>";
 		
    }

    public function update()
    {
    	//原生
    	$res = Db::execute('update think_data set name = "ooo" where id = 8');
    	//dump($res);
        Db::table('think_data')->where('id',18)->update(['name' => "qaq"]);
        $db = db('data');
        $db->where('id',21)->update(['name' => "hhhhh",'title' => "文章标题"]);
    	echo "<h1> 更新</h1>";
    }
    public function select( )
    {
        echo "<h1>查找</h1>";
        $res = Db::query('select * from think_data where id <> 5');
        dump($res);
        $res1 = Db::name('data')->where('id',19)->select();
        dump($res1);
        $db = db('data');
        $res2 = $db->where('id',20)->select();
        dump($res2);
    }
    public function delete()
    {
        $res = Db::execute('delete from think_data where id = 6');
        dump($res);
        //Db::name('data')->where('id',7)->delete();
        $db = db('data');
        //$db->where('id','<',2)->delete();
        //$db->delete(1);
        $db->delete([1,2,3]);
    }
    public function show()
    {
        // 显示数据库列表
        $result = Db::query('show tables from antqueen');
        dump($result);
    }
    public function clear()
    {
        # // 清空数据表
        $result = Db::execute('TRUNCATE table think_data');
        dump($result);
    }
}
