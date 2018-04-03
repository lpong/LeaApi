<?php

namespace app\model;

use think\Model;

class Project extends Model
{

    protected $readonly = [
        'user_id',
    ];


    public function user()
    {
        return $this->belongsTo('User');
    }
}
