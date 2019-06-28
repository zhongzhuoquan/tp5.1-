<?php
namespace app\admin\controller;
use app\admin\common\controller\Base;
use app\admin\common\model\Cate as CateModel;
use think\facade\Request;
use think\facade\Session;

class Cate extends  Base {
//分类管理的首页
    public function index(){
        //检查用户是否登录
        $this->isLogin();
        //登录成功后就直接跳转
        return $this->redirect('cateList');
    }
    //分类列表
    public function cateList(){
        //1检查用户是否登录
        $this->isLogin();
        //2获取到所有的分类
        $cateList=CateModel::all();
        //3.设置模板变量
        $this->view->assign('title','分类管理');
        $this->view->assign('empty','<span style="color: red">没有分类</span>');
        $this->view->assign('cateList',$cateList);
        return $this->view->fetch('cateList');
    }
    //渲染编辑分类
    public function cateEdit(){
        //1.获取到分类的id
        $cateId=Request::param('id');
        //2.获取主键查询要更新的分类信息
        $cateInfo=CateModel::where('id',$cateId)->find();
        //3.设置模板变量
        $this->view->assign('title','编辑分类');
        $this->view->assign('cateInfo',$cateInfo);
        return $this->view->fetch('cateEdit');
    }
    //执行更新操作
    public function doEdit(){
        //1.获取
        $data=Request::param();
        //2.取出主键
        $id=$data['id'];
        //4.删除主键id
        unset($data['id']);
        //执行更新
        if( CateModel::where('id',$id)->data($data)->update()){
            return $this->success('更新成功！！！','catelist');
        }
        else{
            return $this->error('更新失败！！！') ;
        }

    }
    //删除
    public function doDelete(){
        $id=Request::param('id');
        //2执行删除
        if(CateModel::where('id',$id)->delete()){
            return $this->success('删除成功！！！','cateList');
        }else{
            return $this->error('删除失败！！！');
        }
    }
    //渲染添加界面
    public function cateAdd(){
    return $this->fetch('cateadd',['title'=>'添加分类']);
    }
    //添加
    public function doAdd(){
        //1获取要添加的分类信息
        $data=Request::param();
        //2执行新增
        if(CateModel::create($data)){
            $this->success('添加成功！！！','catelist');
        }else{
            $this->error('新增失败！！！');
        }
    }
}