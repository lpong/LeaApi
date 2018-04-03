<?php
/**
 * Created by PhpStorm.
 * User: lea
 * Date: 2018/3/29
 * Time: 15:26
 */

namespace app\validate;

use think\Validate;

class User extends Validate
{
    protected $rule = [
        'email|邮箱'         => 'require|email|max:64|unique:user',
        'nickname|昵称'      => 'require|max:32',
        'password|密码'      => 'require|length:6,16',
        're_password|重复密码' => 'require|confirm:password',
        'captcha|验证码'      => 'require|captcha',
    ];
}