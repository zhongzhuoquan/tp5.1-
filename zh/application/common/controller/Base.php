<?php
/**
 * 基础控制器
 * 必须继承think/controller.php
 */
namespace app\common\controller;
use think\Controller;
use think\facade\Session;
use app\common\model\ArtCate;
use app\admin\common\model\Site;
use think\facade\Request;
use app\common\model\Article;
class Base extends  Controller{
    protected  function initialize(){
        //显示分类导航
        $this->showNav();
        //检查网站是否关闭
        $this->is_open();
        //获取pv数据，右侧
        $this->getHotArt();
    }
    //防止重复登录
    public function logined(){
        if(Session::has('user_id')){
            $this->error('您已经登录了！！！','index/index');
        }
    }
    public function isLogin(){
        if(!Session::has('user_id')){
            $this->error('您忘记登录了！！！','user/login');

        }
    }
    //获取到所有的分类信息
    protected function showNav(){
        //1.查询分类表获取到所有的分类信息
        $cateList=ArtCate::all(function($query){
            $query->where('status',1)->order('sort','asc');
        });
        //2.将分类信息赋值给模板 nav.html
        $this->view->assign('cateList',$cateList);
    }
    //检查站点是否关闭
    public function is_open(){
        //1.获取当前状态
        $isOpen=Site::where('status',1)->value('is_open');
        //2如果站点已经关闭，那我们只允许关闭前台
        if($isOpen==0 && Request::module()=='index'){
            $info= <<<'INFO'
<body style="background-color:#333">
<h1 style="color:#eee;text-align:center;margin:200px">站点维护中...</h1>
</body>
INFO;

            exit($info);
        }
    }
    //检查注册
    public function is_reg(){
        //1获取注册状态
        $isReg=Site::where('status',1)->value('is_reg');
        //2如果已经关闭，直接跳转首页
       if($isReg==0){
           $this->error('注册功能已关闭！！！','index/index');
       }
    }
    //根据pv获取内容
    public function getHotArt(){
        $hotArtList=Article::where('status',1)
            ->order('pv','desc')
            ->limit(12)
            ->all();
        $this->view->assign('hotArtList',$hotArtList);

    }
}