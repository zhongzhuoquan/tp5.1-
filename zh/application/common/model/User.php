<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/5/17 0017
 * Time: 15:43
 */
namespace app\common\model;
use think\Model;
class User extends Model {
protected $pk='id';//默认主键
    protected $table='zh_user';//默认数据表
    protected $createTime='create_time';
    protected $updateTime='update_time';
    protected $dateFormat='Y年m月d日';
    //获取器
        public function getStatusAttr($value){
        $status=['1'=>'启用','0'=>'禁用'];
        return $status[$value];
    }

    //修改器
   public function setPasswordAttr($values){
       return sha1($values);
   }

}