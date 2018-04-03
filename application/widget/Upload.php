<?php
/**
 * Created by PhpStorm.
 * User: lea
 * Date: 2018/3/29
 * Time: 14:05
 */

namespace app\widget;

use think\facade\View;

class Upload
{
    public function image($field = "", $value = "")
    {
        return View::fetch('widget/upload/image', [
            'field' => $field,
            'value' => $value,
        ]);
    }

    public function file($field = "", $value = "", $exts = '')
    {
        return View::fetch('widget/upload/file', [
            'field' => $field,
            'value' => $value,
            'exts'  => $exts,
        ]);
    }
}