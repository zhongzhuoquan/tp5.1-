<?php
namespace app\admin\controller;
use app\admin\common\controller\Base;
use app\common\model\Comment as CommentModel;
use think\facade\Request;
use think\facade\Session;
class Comment extends Base {
    public function index(){
        $data=CommentModel::all();
        $this->view->assign('title','评论回复');
        $this->view->assign('empty','<span style="color: red;">没有任何数据</span>');
        $this->view->assign('data',$data);
        return $this->view->fetch('index');
    }
    //渲染回复评论
    public function CommentEdit(){
        //1获取要更新的用户ID
        $commentId=Request::param('id');
        //2 查询
        $commentInfo=CommentModel::where('id',$commentId)->find();
        //3.设置编制界面的模板变量
        $this->view->assign('title','评论回复');
        $this->view->assign('commentInfo',$commentInfo);
        return $this->view->fetch('commentEdit');

    }
    //保存评论
    public function doedit(){
        $data=Request::param();
        //2.取出主键
        $id=$data['id'];
        //4.删除主键id
        unset($data['id']);
        //执行更新
        if( CommentModel::where('id',$id)->data($data)->update()){
            return $this->success('回复成功！！！','index');
        }
        else{
            return $this->error('回复失败！！！') ;
        }
    }
    public function doDelete(){
        $id=Request::param('id');
        //2执行删除
        if(CommentModel::where('id',$id)->delete()){
            return $this->success('删除成功！！！','index');
        }else{
            return $this->error('删除失败！！！');
        }
    }

}