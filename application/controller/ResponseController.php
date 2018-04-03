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
use think\Db;
use think\Request;

class ResponseController extends BaseController
{
    /**
     * 添加页面和添加操作
     * @return mixed
     */
    public function add(Request $request)
    {
        if ($request->isPost()) {
            $post     = $request->post();
            $validate = new \app\validate\ApiResponse();
            if (!$validate->check($post)) {
                $this->error($validate->getError());
            }
            $post['project_id'] = $request->post('_pid', '', 'decrypt');
            if (!$post['project_id']) {
                $this->error('参数异常，请刷新页面重试');
            }
            if (!$this->isCanWithProjectId($post['project_id'])) {
                $this->error('您没有权限发布内容');
            }
            $post['user_id'] = session('user.id');
            if ((new ApiResponse())->save($post) !== false) {
                ApiRead::where('api_id', $post['api_id'])->delete();
                $this->success('添加成功', '/reload');
            }
            $this->error('添加失败');
        } else {
            return view('edit');
        }
    }

    /**
     * 修改页面和修改操作
     * @return mixed
     */
    public function edit(Request $request)
    {
        if ($request->isPost()) {
            $post     = $request->only(['name', 'body', 'remark', 'id']);
            $validate = new \app\validate\ApiResponse();
            if (!$validate->check($post)) {
                $this->error($validate->getError());
            }
            if ((new ApiResponse())->save($post, ['id' => $post['id'], 'user_id' => $this->user_id]) !== false) {
                ApiRead::where('api_id', ApiResponse::where('id', $post['id'])->value('api_id'))->delete();
                $this->success('更新成功', '/reload');
            }
            $this->error('更新失败');
        } else {
            $id       = $request->get('id', 0);
            $response = ApiResponse::get($id);
            if (!$response || $response->user_id != $this->user_id) {
                $this->error('该内容不存在');
            }
            return view('edit', [
                'response' => $response
            ]);
        }
    }

    public function delete(Request $request)
    {
        $id = $request->get('id', 0);

        if (ApiResponse::where('id', $id)->where('user_id', $this->user_id)->delete()) {
            ApiRead::where('api_id', ApiResponse::where('id', $id)->value('api_id'))->delete();
            $this->success('删除成功', '/reload');
        }
        $this->error('删除失败');

    }
}