<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 流年 <liu21st@gmail.com>
// +----------------------------------------------------------------------

// 应用公共文件
/**
 * {{val}} 高亮
 * @param $val
 * @return null|string|string[]
 */
function highLight($str)
{
    $str = str_replace(' ', '&nbsp;', $str);
    $str = preg_replace_callback('/\{\{(.*)\}\}/Us', function ($matches) {
        return '<span style="color: #FF5722">' . $matches[0] . '</span>';
    }, $str);
    $str = preg_replace_callback('/`(.*)`/Us', function ($matches) {
        return '<span style="color: #FF5722">' . $matches[1] . '</span>';
    }, $str);
    return nl2br($str);
}

/**
 * @return object|static
 */
function auth()
{
    return app\library\Auth::ins();
}

/**
 * @return bool
 */
function islogin()
{
    return app\library\Auth::ins()->isLogin();
}

/**
 * 加密一个数字
 * @param $num
 * @return string
 */
function encrypt($num)
{
    static $hashids;
    if (empty($hashids)) {
        $hashids = new \Hashids\Hashids(config('param.secret'), 16);
    }
    return $hashids->encode($num);
}

/**
 * 解密一个数字
 * @param $str
 * @return bool
 */
function decrypt($str)
{
    static $hashids;
    if (empty($hashids)) {
        $hashids = new \Hashids\Hashids(config('param.secret'), 16);
    }
    $ret = $hashids->decode($str);
    return isset($ret[0]) ? $ret[0] : false;
}