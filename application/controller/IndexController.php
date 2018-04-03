<?php
/**
 * Created by PhpStorm.
 * User: lea
 * Date: 2018/3/27
 * Time: 15:42
 */

namespace app\controller;

use app\library\Auth;
use think\Config;

class IndexController extends Config
{
    public function index()
    {
        return view();
    }
}