<?php
/**
 * Created by PhpStorm.
 * User: lea
 * Date: 2018/3/29
 * Time: 14:22
 */
return [
    //上传大小限制，单位字节
    'upload_size_limit' => [
        'face'      => 524288,
        'image'     => 1048576,
        'attach'    => 524288000,
        'document'  => 5242880,
        'video'     => 209715200,
        'audio'     => 5242880,
        'um-editor' => 1048576,
    ],
    //上传允许的文件格式
    'upload_type_limit' => [
        'face'      => 'jpg,png,jpeg',
        'image'     => 'jpg,png,gif,jpeg',
        'attach'    => 'zip,rar,tar.gz,json,apk',
        'document'  => 'xls,xlsx,doc,docx,ppt,pptx',
        'video'     => 'mp4,',
        'audio'     => 'mp3',
        'um-editor' => 'jpg,png,gif,jpe,bmp',
    ],

    //上传的位置,也可以是/home/file定义
    'upload_path'       => env('root_path') . 'public/uploads',
];