<?php
/**
 * Created by PhpStorm.
 * User: lea
 * Date: 2018/3/29
 * Time: 13:13
 */

namespace app\controller;

use think\Controller;

class BaseController extends Controller
{

    /**
     * 当前登录用户
     * @var
     */
    protected $user;


    /**
     * 当前登录用户id
     * @var
     */
    protected $user_id;

    public function initialize()
    {
        parent::initialize();
        $user = session('user');
        if (!$user) {
            $this->redirect('/');
        }

        $this->user    = $user;
        $this->user_id = $user['id'];

        $this->assign('meta_title', config('app_name'));
    }
}