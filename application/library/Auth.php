<?php
/**
 * Created by PhpStorm.
 * User: lea
 * Date: 2018/3/29
 * Time: 15:01
 */

namespace app\library;

use think\Db;
use think\facade\Config;
use think\facade\Request;

class Auth
{
    /**
     * user
     * @var
     */
    protected $user;


    /**
     * @var array
     */
    protected $config;

    /**
     * @var object 对象实例
     */
    protected static $instance;

    /**
     * 类架构函数
     * Auth constructor.
     */
    public function __construct()
    {
        if ($config = Config::get('auth.')) {
            $this->config = $config;
        }
    }

    /**
     * 初始化
     * @param array $options
     * @return object|static
     */
    public static function ins()
    {
        if (is_null(self::$instance)) {
            self::$instance = new static();
        }
        return self::$instance;
    }

    /**
     * 登录
     * @param null $user
     * @return bool
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function login($user = null)
    {
        if (is_numeric($user)) {
            $user = Db::name('user')->find($user);
            unset($user['password']);
        }

        if ($user) {
            session('user', $user);
            return true;
        }
        return false;
    }

    public function logout()
    {
        session('user', null);
        $this->user = null;
        return true;
    }

    /**
     * 校验url，是否需要用户验证
     * @return bool
     */
    public function checkPublicUrl()
    {
        $urls = $this->config['public_url'];
        if (in_array($this->getPath(), $urls)) {
            return true;
        }
        return false;
    }


    /**
     * 检查是否登录
     * @return bool
     */
    public function isLogin()
    {
        return !!$this->user();
    }

    public function refresh()
    {
        return $this->login($this->getUserId());
    }


    /**
     * 当前登录用户
     * @return mixed|null
     */
    public function user()
    {
        $this->user = !empty($this->user) ? $this->user : session('user');
        return $this->user;
    }

    public function projectIds()
    {
        return Db::name('project')->where('user_id', $this->getUserId())->column('id');
    }

    /**
     * 获取用户id
     * @return mixed
     */
    public function getUserId()
    {
        $user = $this->user();
        return $user ? $user->id : null;
    }

    /**
     * 获取path
     * @return string
     */
    public function getPath()
    {
        return '/' . str_replace('.', '/', strtolower(Request::controller() . '/' . Request::action()));
    }

    public static function __callStatic($name, $arguments)
    {
        // TODO: Implement __callStatic() method.
        exit('1');
    }

}