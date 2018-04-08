<?php

namespace app\validate;

use think\Validate;

class Project extends Validate
{
    protected $rule = [
        'name|项目名称'        => 'require|max:128',
        'remark|接口说明'      => 'require|max:522',
        'description|项目描述' => 'require|max:522',
        'sort|排序'          => 'number',
        'cover|封面'         => 'require',
    ];

}
