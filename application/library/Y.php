<?php

namespace app\library;

class Y
{
    //成功返回
    public static function success($msg = 'success', $data = [], $url = '')
    {
        return json([
            'code' => 0,
            'msg'  => $msg,
            'url'  => $url,
            'data' => $data,
        ], 200);
    }

    //失败返回
    public static function error($msg = 'fail', $data = [], $url = '')
    {
        return json([
            'code' => 1,
            'msg'  => $msg,
            'url'  => $url,
            'data' => $data,
        ], 200);
    }

    //table
    public static function table($data = [], $count = 0)
    {
        return json([
            'code'  => 0,
            'msg'   => 'success',
            'count' => $count,
            'data'  => $data,
        ], 200);
    }
}