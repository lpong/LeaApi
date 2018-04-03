<?php

namespace app\model;

use think\Model;

class ApiResponse extends Model
{
    protected $readonly = [
        'project_id',
        'user_id',
        'api_id',
    ];
}
