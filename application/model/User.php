<?php
/**
 * Created by PhpStorm.
 * User: lea
 * Date: 2018/3/29
 * Time: 15:30
 */

namespace app\model;

use think\Model;

class User extends Model
{


    public static function password($pass)
    {
        return password_hash($pass, PASSWORD_DEFAULT);
    }

    public static function password_check($pass, $hash)
    {
        return password_verify($pass, $hash);
    }
}