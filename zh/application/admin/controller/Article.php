<?php
namespace app\admin\controller;
use app\admin\common\controller\Base;
use app\admin\common\model\Article as ArtModel;
use app\admin\common\model\Cate;
use think\Db;
use think\facade\Request;
use think\facade\Session;
class Article extends Base {
    //文章管理的首页
    public function index(){
        //检查用户是否登录
        $this->isLogin();
        //登录成功后就直接跳转
        return $this->redirect('artList');
    }
    //文章列表
    public function artList(){
        //1检查用户是否登录
        $this->isLogin();
        //2获取用户ID
        $user_id=Session::get('user_id');
        $isAdmin=Session::get('admin_level');
        //3.获取当前用户发布的文章
        $artList=ArtModel::where('user_id',$user_id)->paginate(5);
        //4.超级管理员
        if($isAdmin==1){
        $artList=ArtModel::paginate(5);
        }

        //5.设置模板变量
        $this->view->assign('title','文章管理');
        $this->view->assign('empty','没有文章');
        $this->view->assign('artList',$artList);
        return $this->view->fetch('artList');
    }
    //编辑文章
    public function artEdit(){
        //1.获取到文章的id
        $artId=Request::param('id');
        //2.获取主键查询要更新的文章信息
        $artInfo=ArtModel::where('id',$artId)->find();
        //3.获取栏目信息
        $cateList=Cate::all();
        //3.设置模板变量
        $this->view->assign('title','编辑文章');
        $this->view->assign('cateList',$cateList);
        $this->view->assign('artInfo',$artInfo);
        return $this->view->fetch('artEdit');
    }
    //保存
    public function doEdit(){
        //1.获取用户提交的id
        $data=Request::param();
        //2.获取一下上传的图片信息
        //验证成功
        //获取图片信息
        $file=Request::file('title_img');
        //文件信息验证后，再上传到服务器上目录,以public为开始
        $info=$file->validate([
            'size'=>1000000000,
            'ext'=>'jpeg,jpg,png,gif',
        ])->move('public/uploads/');
        if($info){
            $data['title_img'] = $info->getSaveName();

        }else{
            $this->error( $file->getError());
        }
        //将数据写到表
        if(ArtModel::update($data)){
            $this->success('文章更新成功','artList');
        }else{
            $this->error('文章更新失败');
        }
    }
    //删除
    public function doDelete(){
        $artId=Request::param('id');
        //2执行删除
        if(ArtModel::destroy($artId)){
            return $this->success('删除成功！！！');
        }else{
            return $this->error('删除失败！！！');
        }
    }

}