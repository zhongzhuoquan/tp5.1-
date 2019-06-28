<?php
/**
 *
 * 测试专用
 */

namespace app\index\controller;
use app\common\controller\Base;
use app\common\model\User;

class Test extends Base{
//测试用户的验证器
    public function test1(){
        dump(User::get(1));
    }
}