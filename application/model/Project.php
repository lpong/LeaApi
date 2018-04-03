<?php

namespace app\model;

use think\Model;

class Project extends Model
{


    public function user()
    {
        return $this->belongsTo('User');
    }
}
