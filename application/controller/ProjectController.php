<?php
/**
 * Created by PhpStorm.
 * User: lea
 * Date: 2018/3/29
 * Time: 13:13
 */

namespace app\controller;

use app\library\Auth;
use app\library\Y;
use app\model\Api;
use app\model\ApiResponse;
use app\model\Category;
use app\model\Msg;
use app\model\Project;
use app\model\ProjectUser;
use app\model\User;
use think\App;
use think\Db;
use think\facade\Env;
use think\Request;

class ProjectController extends BaseController
{
    //进行中的项目
    public function index(Request $request)
    {
        return view();
    }

    //进行中的项目
    public function project()
    {
        $list = Db::name('project_user')->alias('a')
            ->field('p.id,p.user_id,p.name,p.cover,p.description,p.status as pstatus,p.update_time,a.create_time as join_time,a.status as astatus,u.nickname,u.face,u.email')
            ->leftJoin('project p', 'a.project_id=p.id')
            ->leftJoin('user u', 'u.id=p.user_id')
            ->where('a.user_id', $this->user_id)
            ->where('a.status', 1)//看 不看
            ->where('p.status', 1)
            ->order('a.id desc')
            ->paginate(4, true);

        return view('project', [
            'list' => $list
        ]);
    }

    //我的项目
    public function my()
    {
        $list = Db::name('project')->where('user_id', $this->user_id)->where('status', 'in', '0,1')->paginate(10, true);
        return view('my', [
            'list' => $list
        ]);
    }

    //我参与的项目
    public function myJoin()
    {
        $list = Db::name('project_user')->alias('a')
            ->field('p.id,p.user_id,p.name,p.status as pstatus,p.update_time,a.create_time as join_time,a.status as astatus,u.nickname,u.face,u.email')
            ->leftJoin('project p', 'a.project_id=p.id')
            ->leftJoin('user u', 'u.id=p.user_id')
            ->where('a.user_id', $this->user_id)
            ->where('a.status', 'in', '0,1')//看 不看
            ->where('p.status', 'in', '0,1')
            ->order('a.id desc')
            ->paginate(10, true);

        return view('my_join', [
            'list' => $list
        ]);
    }


    /**
     * 添加页面和添加操作
     * @return mixed
     */
    public function add(Request $request)
    {
        if ($request->isPost()) {
            $post           = $request->post();
            $post['script'] = $request->post('script', '', 'trim');
            $validate       = new \app\validate\Project();
            if (!$validate->check($post)) {
                $this->error($validate->getError());
            }
            $post['user_id'] = session('user.id');
            $project         = Project::create($post);
            if ($project) {
                (new ProjectUser())->save(['project_id' => $project->id, 'user_id' => session('user.id'), 'auth' => 'self', 'status' => 1]);
                $this->success('发布成功', '');
            }
            $this->error('发布失败');
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
            $post           = $request->only(['_id', 'name', 'cover', 'remark', 'description', 'status'], 'post');
            $post['script'] = $request->post('script', '', 'trim');
            $validate       = new \app\validate\Project();
            if (!$validate->check($post)) {
                $this->error($validate->getError());
            }
            $id = decrypt($post['_id']);
            if ((new Project())->allowField(true)->save($post, ['id' => $id, 'user_id' => $this->user_id]) > 0) {
                $this->success('修改成功', '');
            }
            $this->error('修改失败');
        } else {
            $id = $request->get('_id', 0, 'decrypt');
            if (!$id) {
                $this->error('参数错误');
            }
            $info = Project::get($id);
            if (!$info || $info->user_id != $this->user_id) {
                $this->error('项目不存在');
            }

            return view('edit', [
                'info' => $info
            ]);
        }
    }

    //删除项目
    public function delete(Request $request)
    {
        $ids = $request->get('_id', '');
        if (!$ids) {
            return $this->error('请选择要操作的数据');
        }
        $ids = array_filter(explode(',', $ids));
        array_walk_recursive($ids, function (&$id) {
            $id = decrypt($id);
        });
        if (Project::where('id', 'in', $ids)->where('user_id', $this->user_id)->setField('status', 2) > 0) {
            $this->success('删除成功', '');
        }
        $this->error('删除失败');
    }

    //显示项目
    public function projectUserStatus(Request $request)
    {
        $ids    = $request->get('_id', '');
        $status = $request->get('status', '');
        if (!$ids) {
            $this->error('请选择要操作的数据');
        }
        if (!in_array($status, [0, 1, 2])) {
            $this->error('参数错误');
        }
        $ids = array_filter(explode(',', $ids));
        array_walk_recursive($ids, function (&$id) {
            $id = decrypt($id);
        });
        if (ProjectUser::where('project_id', 'in', $ids)->where('user_id', $this->user_id)->where('status', 'neq', 2)->setField('status', 1) > 0) {
            $this->success('操作成功', '');
        }
        $this->error('操作失败');
    }

    //用户
    public function users(Request $request)
    {
        if ($request->isPost()) {
            $id = $request->param('_id', 0, 'decrypt');
            if (!$id) {
                $this->error('参数错误');
            }
            $project = Project::get($id);
            if (!$project || $project['user_id'] != $this->user_id || $project['status'] == 2) {
                $this->error('项目不存在或已被删除');
            }
            if ($project['status'] != 1) {
                $this->error('项目已关闭');
            }
            $list = Db::name('project_user')->alias('a')
                ->field('a.id,a.user_id,a.create_time as join_time,a.status as astatus,a.auth,u.nickname,u.face,u.email')
                ->leftJoin('user u', 'u.id=a.user_id')
                ->where('a.project_id', $id)
                ->order('a.id desc')
                ->paginate(10, true);
            return view('users_list', [
                'list'   => $list,
                'status' => ProjectUser::$status,
                'auth'   => ProjectUser::$auth,
            ]);
        } else {
            return view('users');
        }
    }

    //设置权限
    public function auth()
    {
        $id   = $this->request->get('id', 0);
        $info = ProjectUser::get($id);
        if (!$info || $info['status'] == 2 || $info['user_id'] == $this->user_id) {
            $this->error('当前信息无法操作');
        }
        $project = Project::get($info['project_id']);
        if (!$project || $project['user_id'] != $this->user_id || $project['status'] == 2) {
            $this->error('项目不存在');
        }

        $info->auth = $info->auth == 'read' ? 'write' : 'read';
        if ($info->save()) {
            $this->success('设置成功', '');
        }
        $this->error('设置失败');

    }

    //踢出该项目
    public function pUnlink()
    {
        $id   = $this->request->get('id', 0);
        $info = ProjectUser::get($id);
        if (!$info || $info['status'] == 2 || $info['user_id'] == $this->user_id) {
            $this->error('当前信息无法操作');
        }
        $project = Project::get($info['project_id']);
        if (!$project || $project['user_id'] != $this->user_id || $project['status'] == 2) {
            $this->error('项目不存在');
        }

        $info->status = 2;
        if ($info->save()) {
            $this->success('设置成功', '');
        }
        $this->error('设置失败');
    }

    //邀请用户
    public function ask()
    {
        $email      = $this->request->post('email', '');
        $project_id = $this->request->get('_id', '', 'decrypt');
        if (!$email) {
            $this->error('请输入邮箱');
        }
        $user = User::where('email', $email)->find();
        if (!$user) {
            $this->error('用户不存在');
        }
        if ($user['id'] == $this->user_id) {
            $this->error('不要邀请自己');
        }
        $project = Project::get($project_id);
        if ($project['status'] != 1) {
            $this->error('该项目已关闭，无法邀请');
        }
        if (ProjectUser::where('user_id', $user['id'])->where('project_id', $project_id)->where('status', 'neq', '2')->find()) {
            $this->error('该用户已邀请，无需重复操作');
        }

        $data = [
            'project_id' => $project_id,
            'user_id'    => $user['id'],
            'auth'       => 'read',
            'status'     => 9
        ];
        if ($info = ProjectUser::create($data)) {
            Msg::send($user['id'], $this->user['nickname'] . '邀请您<a href="' . url('project/link', ['_id' => encrypt($info->id), '_p_id' => encrypt($project_id)]) . '">加入' . $project['name'] . '</a>');

            $this->success('邀请成功', '');
        }
        $this->error('邀请失败');

    }

    //接受邀请
    public function link()
    {
        $id         = $this->request->get('_id', '', 'decrypt');
        $project_id = $this->request->get('_p_id', '', 'decrypt');

        $info = ProjectUser::get($id);

        if (!$info || $info['user_id'] != $this->user_id || $info['status'] != 9 || $info['project_id'] != $project_id || (time() - strtotime($info['create_time'])) > 24 * 3600) {
            $this->error('该邀请不存或已过期');
        }
        $project = Project::get($project_id);
        if ($project['status'] != 1) {
            $this->error('该项目已关闭，无法加入');
        }
        $info->status = 1;
        if ($info->save()) {
            $this->success('恭喜您已成功加入' . $project['name']);
        }
        $this->error('加入该项目失败');
    }

    public function import()
    {
        if ($this->request->isGet()) {
            $list = Db::name('project_user')->alias('a')
                ->field('p.id,p.name')
                ->leftJoin('project p', 'a.project_id=p.id')
                ->leftJoin('user u', 'u.id=p.user_id')
                ->where('a.user_id', $this->user_id)
                ->where('a.status', 'in', '0,1')//看 不看
                ->where('a.auth', 'in', 'self,write')
                ->where('p.status', 'in', '0,1')
                ->order('a.id desc')
                ->select();

            return view('import', [
                'list' => $list
            ]);
        }

        $v          = $this->request->post('v', '');
        $project_id = $this->request->post('project_id');
        if (!empty($project_id)) {
            $project_id = decrypt($project_id);
        }
        $project_id = is_numeric($project_id) && $project_id > 0 ? $project_id : 0;
        $file       = $this->request->post('file', '');

        if ($v != 'p-2.1') {
            $this->error('版本不支持');
        }

        $data = file_get_contents(Env::get('root_path') . '/public' . $file);
        $data = json_decode($data, true);

        if (!$data) {
            $this->error('读取数据异常');
        }


        Db::transaction(function () use ($data, $project_id) {
            if ($data) {
                $user_id = session('user.id');
                //项目信息
                if ($project_id > 0) {
                    (new Project())->save([
                        'remark' => $data['info']['description'],
                    ], ['id' => $project_id]);
                } else {
                    $project = Project::create([
                        'user_id' => $user_id,
                        'name'    => $data['info']['name'],
                        'remark'  => isset($data['info']['description']) ? $data['info']['description'] : '',
                        'sort'    => 0,
                        'status'  => 1
                    ]);
                    ProjectUser::create([
                        'user_id'    => $user_id,
                        'project_id' => $project->id,
                        'auth'       => 'self',
                        'status'     => 1
                    ]);
                    $project_id = $project->id;
                }

                //添加分类
                if ($data['item']) {
                    foreach ($data['item'] as $ca) {
                        $cate = Category::where('title', $ca['name'])->find();
                        if (!$cate) {
                            $category    = Category::create([
                                'project_id' => $project_id,
                                'user_id'    => $user_id,
                                'title'      => $ca['name'],
                                'remark'     => $ca['description'],
                                'sort'       => 0
                            ]);
                            $category_id = $category->id;
                        } else {
                            $category_id = $cate['id'];
                        }

                        if (!empty($ca['item'])) {
                            foreach ($ca['item'] as $val) {
                                $request = $val['request'];
                                $temp    = [
                                    'user_id'      => $user_id,
                                    'project_id'   => $project_id,
                                    'category_id'  => $category_id,
                                    'method'       => $request['method'],
                                    'name'         => $val['name'],
                                    'remark'       => empty($request['description']) ? '' : $request['description'],
                                    'url'          => isset($request['url']['raw']) ? $request['url']['raw'] : '',
                                    'headers'      => $request['header'],
                                    'params'       => [],
                                    'formdata'     => isset($request['body']['formdata']) ? $request['body']['formdata'] : [],
                                    'urlencoded'   => isset($request['body']['urlencoded']) ? $request['body']['urlencoded'] : [],
                                    'raw'          => isset($request['body']['raw']) ? $request['body']['raw'] : '',
                                    'model'        => isset($request['body']['mode']) ? $request['body']['mode'] : '',
                                    'model_header' => '',
                                ];

                                $a = Api::where('url', $temp['url'])->where('project_id', $project_id)->where('method', $temp['method'])->where('name', $val['name'])->find();

                                if (!$a) {
                                    $api    = Api::create($temp);
                                    $api_id = $api->id;
                                } else {
                                    $api_id = $a['id'];
                                }
                                if ($val['response']) {
                                    foreach ($val['response'] as $v) {
                                        if (!ApiResponse::where('api_id', $api_id)->where('name', $v['name'])->count()) {
                                            ApiResponse::create([
                                                'api_id'     => $api_id,
                                                'project_id' => $project_id,
                                                'user_id'    => $user_id,
                                                'name'       => $v['name'],
                                                'status'     => $v['status'],
                                                'remark'     => '',
                                                'body'       => $v['body']
                                            ]);
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }
        });

        $this->success('导入成功');
    }

}