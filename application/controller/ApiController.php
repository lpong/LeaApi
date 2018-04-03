<?php
/**
 * Created by PhpStorm.
 * User: lea
 * Date: 2018/3/27
 * Time: 15:42
 */

namespace app\controller;

use app\library\Auth;
use app\library\Y;
use app\model\Api;
use app\model\ApiRead;
use app\model\ApiResponse;
use app\model\Category;
use app\model\Project;
use app\model\ProjectUser;
use think\Db;
use think\Request;

class ApiController extends BaseController
{
    public function index($id)
    {
        $id      = decrypt($id);
        $project = Project::find($id);
        if (!$project) {
            $this->error('信息不存在');
        }

        session('project_id', $id);

        $category    = Category::where('project_id', $id)->column('*', 'id');
        $category[0] = [];
        $response    = ApiResponse::where('project_id', $id)->select();
        $api         = Api::where('project_id', $id)->column('*', 'id');


        if ($response && $api) {
            foreach ($response as $val) {
                if (isset($api[$val['api_id']])) {
                    $api[$val['api_id']]['response'][] = $val;
                }
            }
        }
        if ($api) {
            foreach ($api as $val) {
                if (isset($category[$val['category_id']])) {
                    $category[$val['category_id']]['request'][] = $val;
                }
            }
        }
        ksort($category);
        $project['items'] = $category;

        $project = $project->toArray();

        //获取已读
        $read            = ApiRead::where('project_id', $id)->where('user_id', session('user.id'))->column('api_id');
        $project['read'] = $read;

        //获取权限
        $auth                 = ProjectUser::where('project_id', $id)->where('user_id', session('user.id'))->where('status', 1)->value('auth');
        $project['edit_auth'] = in_array($auth, ['write', 'self']);

        $project['is_self'] = $project['user_id'] === session('user.id');

        return $this->fetch('index', $project);
    }

    public function add(Request $request)
    {
        if ($request->isPost()) {
            $post     = $request->post();
            $validate = new \app\validate\Api();
            if (!$validate->check($post)) {
                $this->error($validate->getError());
            }

            if (empty($post['params'])) {
                $post['params'] = array_values($post['params']);
            } else {
                $post['params'] = [];
            }
            if (!empty($post['headers'])) {
                $post['headers'] = array_values($post['headers']);
            } else {
                $post['headers'] = [];
            }
            if (!empty($post['formdata'])) {
                $post['formdata'] = array_values($post['formdata']);
            } else {
                $post['formdata'] = [];
            }
            if (!empty($post['urlencode'])) {
                $post['urlencode'] = array_values($post['urlencode']);
            } else {
                $post['urlencode'] = [];
            }
            $post['project_id'] = session('project_id');
            $post['user_id']    = session('user.id');
            $api                = new Api();
            if ($api->allowField(true)->save($post) > 0) {
                $this->success('发布成功');
            }
            $this->error('发布失败');
        } else {
            //获取分类
            $category = Category::where('project_id', session('project_id'))->select();
            return view('add', [
                'category' => $category
            ]);
        }
    }

    //修改
    public function edit()
    {
        if ($this->request->isPost()) {
            $post     = $this->request->post();
            $validate = new \app\validate\Api();
            if (!$validate->check($post)) {
                $this->error($validate->getError());
            }

            if (!empty($post['params'])) {
                $post['params'] = array_values($post['params']);
            } else {
                $post['params'] = [];
            }
            if (!empty($post['headers'])) {
                $post['headers'] = array_values($post['headers']);
            } else {
                $post['headers'] = [];
            }
            if (!empty($post['formdata'])) {
                $post['formdata'] = array_values($post['formdata']);
            } else {
                $post['formdata'] = [];
            }
            if (!empty($post['urlencode'])) {
                $post['urlencode'] = array_values($post['urlencode']);
            } else {
                $post['urlencode'] = [];
            }
            $api = new Api();
            if ($api->allowField(true)->save($post, ['id' => $post['id']]) > 0) {
                ApiRead::where('api_id', $api->id)->delete();
                $this->success('修改成功');
            }
            $this->error('修改失败');

        } else {
            $id  = $this->request->get('id', 0);
            $api = Api::get($id);
            if (!$api || $api->user_id != session('user.id')) {
                $this->error('该Api不存在');
            }
            $category          = Category::where('project_id', $api->project_id)->select();
            $api['headers']    = (array)json_decode($api['headers'], true);
            $api['params']     = (array)json_decode($api['params'], true);
            $api['formdata']   = (array)json_decode($api['formdata'], true);
            $api['urlencoded'] = (array)json_decode($api['urlencoded'], true);
            return view('edit', [
                'api'      => $api,
                'category' => $category
            ]);
        }
    }

    //删除
    public function delete()
    {
        $id = $this->request->get('id', 0);
        if (Api::where('id', $id)->where('user_id', session('user.id'))->delete() > 0) {
            $this->success('删除成功');
        }
        $this->error('删除失败');
    }

    //学习
    public function read()
    {
        $api_id     = $this->request->get('_id', 0);
        $project_id = $this->request->get('_pid', '', 'decrypt');

        if (!$project_id) {
            $this->error('参数错误');
        }

        if (!ApiRead::where('project_id', $project_id)->where('api_id', $api_id)->where('user_id', $this->user_id)->count()) {
            ApiRead::create([
                'user_id'    => $this->user_id,
                'project_id' => $project_id,
                'api_id'     => $api_id
            ]);
        }
        $this->success();
    }
}