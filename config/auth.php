<?php
/**
 * Created by PhpStorm.
 * User: lea
 * Date: 2018/3/29
 * Time: 15:03
 */
return [
    /**
     * 不需要登录的地址
     */
    'public_url'  => [
        '/public/login'
    ],

    /**
     * 登录用户都有的权限
     */
    'allow_visit' => [
        '/index/index',
        '/index/system'
    ]
];