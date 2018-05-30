<?php
/**
 * Created by PhpStorm.
 * User: lea
 * Date: 2018/3/27
 * Time: 15:42
 */

namespace app\controller;

use app\model\Api;
use app\model\ApiRead;
use app\model\ApiResponse;
use app\model\Category;
use app\model\Project;
use app\model\ProjectUser;
use traits\controller\Jump;

class ShareController
{
    use Jump;

    public function index($id)
    {
        $id      = decrypt($id);
        $project = Project::find($id);
        if (!$project || $project['is_public'] != 1) {
            $this->error('信息不存在');
        }

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
        $fun = function ($x, $y) {
            if (empty($y['sort']) || empty($y['id'])) {
                return -1;
            }
            if ($x['sort'] == $y['sort']) {
                return $x['id'] < $y['id'] ? -1 : 1;
            }
            return $x['sort'] < $y['sort'] ? -1 : 1;
        };

        uasort($category, $fun);
        $project['items'] = $category;

        $project = $project->toArray();
        //获取已读
        return view('index', $project);
    }
}