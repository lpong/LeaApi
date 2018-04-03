<?php
/**
 * Created by PhpStorm.
 * User: lea
 * Date: 2018/3/29
 * Time: 14:57
 */

namespace app\controller;

use app\library\Auth;
use app\library\Y;
use app\model\User;
use think\Controller;
use think\facade\Validate;
use think\Request;

class PublicController extends Controller
{

    public function auth()
    {
        return view();
    }

    public function login(Request $request)
    {
        $post     = $request->only(['email', 'password'], 'post');
        $validate = Validate::make([
            'email|邮箱'    => 'require|max:32|email',
            'password|密码' => 'require|length:6,16',
        ]);
        if (!$validate->check($post)) {
            $this->error($validate->getError());
        }

        $user = User::get(['email' => $post['email']]);
        if (!$user) {
            $this->error('用户名不存在');
        }
        if (!password_verify($post['password'], $user['password'])) {
            $this->error('密码错误');
        }
        if (1 != $user['status']) {
            $this->error('该用户已被禁用，无法登陆');
        }

        //登录成功
        $user['login_times']     = $user['login_times'] + 1;
        $user['last_login_ip']   = $request->ip();
        $user['last_login_time'] = date('Y-m-d H:i:s');
        if ($user->save()) {
            unset($user['password']);
            session('user', $user);
            cookie('email', $user['email']);
            $this->success('登录成功', '/');
        }
        $this->error('登录失败');
    }

    public function reg(Request $request)
    {
        $post     = $request->only('email,nickname,password,re_password,captcha','post');
        $validate = new \app\validate\User();
        if (!$validate->check($post)) {
            $this->error($validate->getError());
        }
        $post['password']        = password_hash($post['password'], PASSWORD_DEFAULT);
        $post['login_times']     = 1;
        $post['face']            = '/static/image/default.png';
        $post['last_login_time'] = date('Y-m-d H:i:s');
        $post['last_login_ip']   = $request->ip();
        $post['status']          = 1;
        $user                    = new User();
        if ($user->allowField(true)->save($post) > 0) {
            session('user', $user->toArray());
            $this->success('注册成功', '/');
        }
        $this->error('注册失败');


    }
}