<?php
use think\Db;
//根据用户的主键id，查询用户名称
if(!function_exists('getUserName')) {
    function getUserName($id)
    {
        return Db::table('zh_user')->where('id', $id)->value('name');
    }
}
//过滤文章摘要
function getArtContent($content){
return mb_substr(strip_tags($content),0,50).'>>>';
}
if(!function_exists('getCateName')) {
    function getCateName($cate_id)
    {
        return Db::table('zh_article_category')->where('id',$cate_id)->value('name');
    }
}
if(!function_exists('getArtName')) {
    function getArtName($cate_id)
    {
        return Db::table('zh_article')->where('id',$cate_id)->value('title');
    }
}
