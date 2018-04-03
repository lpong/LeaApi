<?php

namespace app\validate;

use think\Validate;

class Category extends Validate
{
    protected $rule = [
        'title|分类名称' => 'require|max:64',
        'remark|描述'  => 'max:522',
        'sort|排序'    => 'number',
    ];

}
