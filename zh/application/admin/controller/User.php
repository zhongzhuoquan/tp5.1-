<?php
namespace app\admin\controller;
use app\admin\common\controller\Base;
use app\admin\common\model\User as UserModel;
use think\facade\Request;
use think\facade\Session;
class User extends Base {
    //渲染登录界面
    public function login(){
    $this->view->assign('title','管理员登录');
  return  $this->view->fetch();
}
    //验证后台登录
    public function checkLogin(){
        $data=Request::param();
        $map[]=['email','=',$data['email']];
        $map[]=['password','=',sha1($data['password'])];
        $result=UserModel::where($map)->find();
        if($result){
            Session::set('user_id',$result['id']);
            Session::set('user_name',$result['name']);
            Session::set('admin_level',$result['is_admin']);
        $this->success('登录成功！！！','admin/user/userList');
        }else{
            $this->error('登录失败!!!');
        }
    }
    //退出登录
    public function logout(){
        //1.清楚session
        Session::clear();
        //2.退出登录
        $this->success('退出成功！！！','admin/user/login');
    }
    //用户列表:
    public function userList(){
        //1.获取到当前用户的id和级别is_admin
        $data['user_id']=Session::get('user_id');
        $data['admin_level']=Session::get('admin_level');
        //2.获取当前用户的信息
            $userList=UserModel::where('id',$data['user_id'])->order('id','desc')->select();
        //3.如果是超级管理员,获取全部信息
        if($data['admin_level']==1){
            $userList=UserModel::select();
        }
        //4.模板赋值
        $this->view->assign('title','用户管理');
        $this->view->assign('empty','<span style="color: red;">没有任何数据</span>');
        $this->view->assign('userList',$userList);
        return $this->view->fetch('userList');
    }
    //渲染编制用户的界面
    public function userEdit(){
        //1获取要更新的用户ID
        $userId=Request::param('id');
        //2 查询
        $userInfo=UserModel::where('id',$userId)->find();
        //3.设置编制界面的模板变量
        $this->view->assign('title','编辑用户');
        $this->view->assign('userInfo',$userInfo);
        return $this->view->fetch('useredit');

    }
    //执行用户信息的保存
    public function doEdit(){
        //1.获取
        $data=Request::param();
        //2.取出主键
        $id=$data['id'];
        //3.将用户密码加密保存
        $data['password']=sha1($data['password']);
        //4.删除主键id
        unset($data['id']);
        //执行更新
       if( UserModel::where('id',$id)->data($data)->update()){
           return $this->success('更新成功！！！','userlist');
       }
        else{
            return $this->error('更新失败！！！') ;
        }
    }
    //执行用户的删除操作
    public function doDelete(){
        $id=Request::param('id');
        //2执行删除
        if(UserModel::where('id',$id)->delete()){
            return $this->success('删除成功！！！','userList');
        }else{
            return $this->error('删除失败！！！');
        }
    }
}