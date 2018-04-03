<?php

namespace app\validate;

use think\Validate;

class Api extends Validate
{
    protected $rule = [
        'name|项目名称'   => 'require|max:64',
        'url|请求地址'    => 'require',
        'method|请求方式' => 'require',
    ];

}
