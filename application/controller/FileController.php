<?php

namespace app\controller;

use app\library\Y;
use think\Controller;
use think\Request;

class FileController extends BaseController
{
    public function upload(Request $request)
    {
        $type = $request->param('type', 'image');
        $file = $request->file('file');
        if (empty($file)) {
            $this->error('文件不存在');
        }
        //获取上传配置
        $config = config('upload.');
        $path   = $config['upload_path'] . '/' . $type;
        if (!isset($config['upload_size_limit'][$type])) {
            $this->error('上传文件格式不允许');
        }
        $info = $file->validate(['size' => $config['upload_size_limit'][$type], 'ext' => $config['upload_type_limit'][$type]])->move($path);
        if ($info) {
            $result = ['src' => '/uploads/' . $type . '/' . $info->getSaveName()];
            $this->success('上传成功', '', $result);
        } else {
            $this->error($file->getError());
        }
    }

}
