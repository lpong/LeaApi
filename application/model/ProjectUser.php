<?php

namespace app\model;

use think\Model;

class ProjectUser extends Model
{


    public static $status = [
        0 => '隐藏',
        1 => '显示',
        2 => '退出',
        9 => '邀请中'
    ];


    public static $auth = [
        'read'  => '读',
        'write' => '读，写',
    ];

}
