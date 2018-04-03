<?php
/**
 * Created by PhpStorm.
 * User: lea
 * Date: 2018/3/27
 * Time: 16:21
 */

namespace app\model;

use think\Model;

class Msg extends Model
{

    protected $updateTime = false;

    //发消息
    public static function send($user_id, $msg)
    {
        if (self::create(['user_id' => $user_id, 'msg' => $msg, 'send_id' => session('user.id')])) {
            return true;
        }
        return false;
    }

    public function sender()
    {
        return $this->belongsTo('User', 'send_id', 'id');
    }
}