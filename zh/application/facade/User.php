<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/5/15 0015
 * Time: 15:35
 */

namespace app\facade;
use think\Facade;

class User extends Facade {
    protected static function getFacadeClass()
    {
         return 'app\common\validate\User';
    }
}