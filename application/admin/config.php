<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006~2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------
// $Id$
return [
    //模板参数替换
    'view_replace_str'       => array(
        '__CSS__'    => '/static/admin/css',
        '__JS__'     => '/static/admin/js',
        '__IMG__' => '/static/admin/images',
        '__BOOT__' => 'FlatLabBootstrap3/admin/template',
        '__FILE_UPLOAD__' => '/file_upload',
        '__URL__' => 'https://zqrb.stockalert.cn/admin',
//        '__URL__' => 'http://www.zqrb.com/admin',
    ),


    //管理员状态
    'user_status' => [
        '1' => '正常',
        '2' => '禁止登录'
    ],
    //角色状态
    'role_status' => [
        '1' => '启用',
        '2' => '禁用'
    ],
    //上传文件到阿里云oss配置
    'oss_file_conf' => [
        'accessKeyId' => "dZwbJBSoG9OREtPi",
        'accessKeySecret' => "3ShvUa4w153SVkoEjGod8HG6IyCUIQ",
        'endpoint' => "http://oss-cn-hangzhou.aliyuncs.com",
        'bucket' => "zhengquanrb",
    ],
    'log' => [
        // 日志记录方式，内置 file socket 支持扩展
        'type'  => 'File',
        // 日志保存目录
        'path'  => APP_PATH.'admin'.DS.'runtime'.DS ,
        // 日志记录级别
        'level' => ['debug','notice','error','sql','info'],
    ],
];
