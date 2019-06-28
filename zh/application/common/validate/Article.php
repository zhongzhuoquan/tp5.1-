<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/5/17 0017
 * Time: 15:45
 */
namespace app\common\validate;
use think\Validate;
class Article extends Validate{
    protected $rule=[
        'title|标题'=> 'require|length:5,20|chsAlphaNum',
        // 'title_img|标题图片'=> 'require',
        'content|文字内容'=> 'require',
        // 'user_id|作者'=> 'require',
        //'cate_id|栏目名称'=>'require'
        //chsAlphaNum'只允许汉字，字母和数字
    ];
}