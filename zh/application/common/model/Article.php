<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/5/17 0017
 * Time: 15:43
 */
namespace app\common\model;
use think\Model;
class Article extends Model {
    protected $pk='id';//默认主键
    protected $table='zh_article';//默认数据表
    protected $createTime='create_time';
    protected $updateTime='update_time';
    protected $dateFormat='Y年m月d日';
    //开启自动设置
    protected $auto=[];//无论是新增或更新都要设置的字段
    //仅新增的有效
    protected $insert=['create_time','status'=>1,'is_top'=>0,'is_hot'=>0];
    //仅更新的时候设置
    protected $update=['update_time'];

}