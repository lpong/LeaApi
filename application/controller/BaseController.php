<?php
/**
 * Created by PhpStorm.
 * User: lea
 * Date: 2018/3/29
 * Time: 13:13
 */

namespace app\controller;

use app\model\ProjectUser;
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
    }

    //获取一个用户含有编辑项目的权限
    public function isCanWithProjectId($pid = 0)
    {
        if (!$pid) {
            return false;
        }
        $project_ids = (array)ProjectUser::where('user_id', session('user.id'))->where('status', 1)->where('auth', 'in', ['self', 'write'])->column('project_id');
        return in_array($pid, $project_ids);
    }
}