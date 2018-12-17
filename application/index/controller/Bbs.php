<?php 
namespace app\index\controller;
use think\Controller;
use think\Db;
use think\Request;
/**
 * 
 */
class Bbs extends Controller
{
	
   public function show()
    {
      $db = db('data');
      // $show = $db->where('id','>',5)->select();
      //分页
      $show = Db::name('data')->where('id','>',0)->order('id','desc')->paginate(10);
	    // 把分页数据赋值给模板变量list
	    $this->assign('show',$show);

      return $this->fetch('Bbs/show');
    }
    /*
    
     */
    public function ajax()
    {
      return $this->fetch();
    } 
    /*
    
     */
    public function add()
    {
      // echo '请求方法:'.request()->method().'<br/>';
      // dump(request()->param());
      // echo $this->request->param('title');
      // echo Request::param('title');
      // echo "-----------------";
      // echo input('param.title');
      $title = input('param.title');
      $content = input('param.content');
      if ($title<>'') {
        $res = Db::name('data')->insert(['name' => 'test','age' => 0,'title' => $title, 'content' => $content,'creat_time' => time(),'delete_time' => 0]);
        //dump($res);
        return $this->success('恭喜您留言成功!','Bbs/show');
      }else{
        //return $this->error('新增失败');

      }
      
      return $this->fetch('Bbs/add');
    }
    public function view()
    {
      $id = input('param.id');
      echo input('param.id');
      if ($id<>'') {
        $list = Db::name('data')->where('id','=',$id)->select();
        dump($list);
        $up = Db::name('data')->where('id','>',$id)->order('id','')->limit(1)->value('id');
        dump($up);
        $next = Db::name('data')->where('id','<',$id)->order('id','desc')->limit(1)->value('id');
        dump($next == 0);

        $this->assign('up',$up);
        $this->assign('next',$next);
        $this->assign('list',$list);
        return $this->fetch('Bbs/view');
      }
      return "留言不存在";

    }
}
 ?>