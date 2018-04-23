<?php
/**
 * Created by PhpStorm.
 * User: lea
 * Date: 2018/3/30
 * Time: 22:41
 */

namespace app\controller;

use app\model\Msg;
use app\model\User;
use think\facade\Validate;

class UserController extends BaseController
{
    public function index()
    {
        $count = Msg::where('user_id', $this->user_id)->where('is_read', 0)->count();
        return view('index', [
            'user'  => session('user'),
            'count' => $count
        ]);
    }

    public function editInfo()
    {
        $nicknme = $this->request->post('nickname', '');
        if (!$nicknme) {
            $this->error('请输入昵称');
        }
        if (User::where('id', session('user.id'))->setField('nickname', $nicknme) !== false) {
            session('user.nickname', $nicknme);
            $this->success('修改成功', '');
        }
        $this->error('修改失败');
    }

    public function editPassword()
    {
        $validate = Validate::make([
            'nowpass|当前密码' => 'require|length:6,16',
            'pass|新密码'     => 'require|length:6,16',
            'repass|确认密码'  => 'require|length:6,16|confirm:pass'
        ]);

        $post = $this->request->post();
        if (!$validate->check($post)) {
            $this->error($validate->getError());
        }

        $password = User::where('id', session('user.id'))->value('password');
        if (!User::password_check($post['nowpass'], $password)) {
            $this->error('当前密码错误');
        }

        if (User::where('id', session('user.id'))->setField('password', User::password($post['pass'])) !== false) {
            $this->success('修改成功', '');
        }

        $this->error('修改失败');
    }

    public function updateFace()
    {
        $face = $this->request->post('face', '');
        if (!$face) {
            $this->error('请上传头像');
        }
        if (User::where('id', session('user.id'))->setField('face', $face) !== false) {
            session('user.face', $face);
            $this->success('修改成功', '');
        }

        $this->error('修改失败');
    }

    public function msg()
    {
        Msg::where('user_id', $this->user_id)->where('is_read', 0)->setField('is_read', 1);
        $list = Msg::where('user_id', $this->user_id)->order('id desc')->with(['sender' => function ($query) {
            return $query->field('id,nickname,face');
        }])->paginate(10);
        return view('msg', [
            'list' => $list
        ]);
    }

    //退出登录
    public function logout()
    {
        session('user', null);
        $this->success('退出成功', url('index'));
    }
}