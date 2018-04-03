<?php

namespace app\validate;

use think\Validate;

class ApiResponse extends Validate
{
    protected $rule = [
        'name|名称' => 'require|max:128',
        'body|响应内容' => 'require',
    ];

}
