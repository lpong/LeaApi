<?php
/**
 * Created by PhpStorm.
 * User: lea
 * Date: 2018/3/27
 * Time: 16:21
 */

namespace app\model;

use think\Model;

class Api extends Model
{


    /**
     * 所属项目
     * @return \think\model\relation\BelongsTo
     */
    public function project()
    {
        return $this->belongsTo('Project');
    }

    /**
     * 分类
     * @return \think\model\relation\HasOne
     */
    public function category()
    {
        return $this->belongsTo('Category');
    }

    /**
     * 响应
     * @return \think\model\relation\HasOne
     */
    public function responses()
    {
        return $this->hasMany('ApiResponse', 'api_id', 'id');
    }
}