<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/5/17 0017
 * Time: 15:45
 */
namespace app\common\validate;
use think\Validate;
class ArtCate extends Validate{
    protected $rule=[
        'name|标题'=> 'require|length:3,20|chsAlpha',
        //chsAlphaNum'只允许汉字，字母和数字


    ];
}