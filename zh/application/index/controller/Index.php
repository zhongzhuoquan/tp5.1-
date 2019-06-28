<?php
namespace app\index\controller;
use app\common\controller\Base;
use app\common\model\ArtCate;
use app\common\model\Article;
use think\facade\Request;
use think\Db;
use think\facade\Session;
use app\common\model\Comment;
class Index extends Base
{
    public function index()
    {
        //全局查询条件
        $map=[];//将所有的查询条件封装到资格数组中
        //条件1
        $map[]=['status','=',1];
        $keywords=Request::param('keywords');
        if(!empty($keywords)){
            //条件2
            $map[]=['title','like','%'.$keywords.'%'];
        }


        //分类信息的显示
        $cateID=Request::param('cate_id');
        //如果存在这个分类id
        if(isset($cateID)){
            //条件3:
            $map[]=['cate_id','=',$cateID];

            $res=ArtCate::get($cateID);

            $artList=Db::table('zh_article')
                ->where($map)
                ->order('create_time','desc')
                ->paginate(3);

            $this->view->assign('cateName',$res['name']);
        }else{
            $this->view->assign('cateName','全部文章');
            $artList=Db::table('zh_article')
                ->where($map)
                ->order('create_time','desc')
                ->paginate(3);

        }
        $this->view->assign('empty','没有文章');
        $this->view->assign('artList',$artList);
        return $this->fetch('index',['name'=>'zzq']);

    }
    //添加文章界面
    public function insert(){

        //1.登录才允许发布文章
        $this->isLogin();
        //2.设置页面标题
        $this->view->assign('title','发布文章');
        //3.获取一下栏目信息
        $cateList=ArtCate::all();
        if(count($cateList)>0){
            //将查询的栏目信息赋值给模板
            $this->assign('cateList',$cateList);
        }else{
            $this->error('请添加栏目','index/index');
        }
        //4.发布页面的渲染
        return $this->view->fetch('insert');
    }
    //保存文章
    public function save(){
        //判断提交类型

        if(Request::isPost()){
            //1.获取用户提交的文章信息
            $data=Request::post();
            $res=$this->validate($data,'app\common\validate\Article');
            if(true !== $res){
                //验证失败
                echo'<script>alert("'.$res.'");window.location.href="insert";</script>';
            }else{
                //验证成功
                //获取图片信息
                $file=Request::file('title_img');
                //文件信息验证后，再上传到服务器上目录,以public为开始
                $info=$file->validate([
                    'size'=>10000000,
                    'ext'=>'jpeg,jpg,png,gif',
                ])->move('public/uploads/');
                if($info){
                    $data['title_img'] = $info->getSaveName();

                }else{
                    $this->error( $file->getError());
                }
                //将数据写到表
                if(Article::create($data)){
                    $this->success('文章发布成功','index/index');
                }else{
                    $this->error('文章发布成功');
                }

            }
        }else{
            $this->error('请求类型错误','index/insert');
        }
    }
    //详情
    public function detail(){
        $artId=Request::param('id');
        $art= Article::get(function($query)use($artId){
            $query->where('id',$artId)
                ->setInc('pv');

        });
        if(!is_null($art)){
            $this->view->assign('art',$art);
        }
        //收藏功能提升体现
        $userId=Session::get('user_id');
        if(Db::table('zh_user_fav')->where('article_id',$artId)->where('user_id',$userId)->find()){
            $statu=1;
        }else{
            $statu=0;
        }
        //点赞功能提升体现
        $userId=Session::get('user_id');
        if(Db::table('zh_user_like')->where('article_id',$artId)->where('user_id',$userId)->find()){
            $statu2=1;
        }else{
            $statu2=0;
        }
        //halt($statu);
        //添加评论
        $commentList=Comment::where('status',1)->where('article_id',$artId)->order('create_time','desc')->paginate(3);
        $this->view->assign('commentList',$commentList);
        $this->view->assign('title','详情页');
        $this->view->assign('statu',$statu);//收藏
        $this->view->assign('statu2',$statu2);//点赞
        return $this->view->fetch('detail');



    }
    //收藏
    public function fav(){
        if(!Request::isAjax()){
            return ['status'=>-1,'message'=>'请求类型错误'];
        }
        $data=Request::param();
        //判断用户是否登录
        if(empty($data['session_id'])){
            return['status'=>-2,'message'=>'请登录后再收藏!!!'];
        }
        $userId=Session::get('user_id');
        $map[] = ['user_id', '=', $userId];
        $map[] = ['article_id', '=', $data['article_id']];
        $fav = Db::table('zh_user_fav')->where($map)->find();
        if (is_null($fav)) {
            Db::table('zh_user_fav')->data([
                'user_id' => $userId,
                'article_id' => $data['article_id'],
            ])->insert();
            return ['status' => 1, 'message' => '收藏成功'];
        } else {
            Db::table('zh_user_fav')->where($map)->delete();
            return ['status' => 0, 'message' => '已取消'];
        }
    }
    //点赞
    public function ok(){


        if(!Request::isAjax()){
            return ['status'=>-1,'message'=>'请求类型错误'];
        }
        $data=Request::param();
        //判断用户是否登录
        if(empty($data['session_id'])){
            return['status'=>-2,'message'=>'请登录后再点赞!!!'];
        }
        $userId=Session::get('user_id');
        $map[]=['user_id','=',$userId];
        $map[]=['article_id','=',$data['article_id']];
        $fav=Db::table('zh_user_like')->where($map)->find();
        if(is_null($fav)){
            Db::table('zh_user_like')->data([
                'user_id'=>$userId,
                'article_id'=>$data['article_id'],
            ])->insert();
            return['status'=>1,'message'=>'点赞成功'];
        }else{
            Db::table('zh_user_like')->where($map)->delete();
            return['status'=>0,'message'=>'已取消'];
        }
    }
    //评论
    public function insertComment()
    {
        if (Request::isAjax()) {
            //1获取评论
            $data = Request::param();
            //2插入数据
            if (Comment::create($data, true)) {
                return ['status'=>1,'message'=>'评论发表成功!!!'];
            }else{
                return ['status'=>0,'message'=>'评论发表失败!!!'];
            }
        }
    }
}
