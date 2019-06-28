<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/6/4 0004
 * Time: 21:27
 */
namespace app\admin\controller;
use app\admin\common\controller\Base;
use app\admin\common\model\Site as SiteModel;
use think\facade\Request;
use think\facade\Session;
class Site extends  Base {

    //站点的管理首页
    public function index(){
        //1.获取站点信息
        $siteInfo=SiteModel::get(['status'=>1]);
        $this->view->assign('siteInfo',$siteInfo);
        return $this->view->fetch('index');
    }
    //保存站点保存信息
    public function save(){
        //获取数据
        $data=Request::param();
        if(SiteModel::update($data)){
            $this->success('更新成功！！！','index');
        }else{
            $this->error('更新失败！！！','index');
        }
    }
}