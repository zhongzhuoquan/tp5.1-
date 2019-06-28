<?php


namespace app\index\controller;
use app\common\controller\Base;
use think\facade\Request;
use app\common\model\User as UserModel;
use app\facade\User as UserValidate;
use think\facade\Session;
class User extends Base {
    //注册页面
    public function register(){
        //检查是否开启注册
        $this->is_reg();
        $this->assign('title','用户注册');
        return $this->fetch();
    }
    //处理用户提交的信息
    public function insert(){
        if(Request::isAjax()){
            //验证数据
            $data=  Request::post();//获得验证的数据
            // $res=$this->validate($data,$rule);
            if(UserValidate::Check($data)){
                if($user=UserModel::create($data)){
                    $res=UserModel::get($user->id);
                    Session::set('user_id',$res->id);
                    Session::set('user_name',$res->name);
                    return ['status'=>1,'message'=>'恭喜，注册成功！！！'];
                    // echo"<script>alert('恭喜，注册成功！！！');window.location.href='index/index'</script>";
                }else{
                    return['status'=>0,'message'=>'注册失败!!!'];
                }
                // return ['status'=> -1,'message'=>UserValidate::getError()];

            }else{
                return ['status'=> -1,'message'=>UserValidate::getError()];
                // return ['status'=>1,'message'=>'恭喜，注册成功！！！'];
            }

            /*    switch($res){
                    case 1:
                        UserModel::create($data);
                        return ['status'=>1,'message'=>'恭喜，注册成功！！！'];

                    break;
                    case 0:
                        return ['status'=> -1,'message'=>$res];
                }
            */
            /*    if($res==true){
                    //false
                    return ['status'=>1,'message'=>'恭喜，注册成功！！！'];
                   // return ['status'=> -1,'message'=>$res];
                 }
                 if($res==false){
                     //true
                    // UserModel::create($data);
                        // return ['status'=>1,'message'=>'恭喜，注册成功！！！'];
                     return ['status'=> -1,'message'=>$res];

                 }
     */
//使用模型来创建数据
            //获取用户通过表单提交过来的数据

        }else{
            $this->error("请求类型错误",'register');
        }
    }

    //用户登录
    public function login(){
        $this->logined();
        return $this->view->fetch('login',['title'=>'用户登录']);

    }
    //用户登录验证与查询
    public function loginCheck(){
        if(Request::isAjax()){
            //验证数据
            $data=  Request::post();//获得验证的数据
            $rule=[
                'email|邮箱'=> 'require|email',
                'password|密码'=> 'require|alphaNum',
            ];
            $res=$this->validate($data,$rule);
            if(true!==$res){
                return ['status'=> -1,'message'=>$res];

            }else{
                //执行查询
                $result=UserModel::get(function($query) use($data){
                    $query->where('email',$data['email'])
                          ->where('password',sha1($data['password']));
                });
                if(null==$result){
                    return ['status'=>0,'message'=>'邮箱或密码错误，请检查！！！'];
                }else{
                    //将用户的数据写到session
                    Session::set('user_id',$result->id);
                    Session::set('user_name',$result->name);
                    Session::set('admin_level',$result->is_admin);
                    Session::set('admin_name',$result->name);
                    return ['status'=>1,'message'=>'登录成功！！！'];
                }
            }

        }else{
            $this->error("请求类型错误",'login');
        }
    }
    //用户退出登录
    public function logout(){
        Session::clear();
        $this->success('退出登录成功！！！','index/index');
    }
}