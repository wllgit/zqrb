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
        '__CSS__'    => '/static/index/css',
        '__JS__'     => '/static/index/js',
        '__IMG__' => '/static/index/image/img',
    ),
    
    'log' => [
        // 日志记录方式，内置 file socket 支持扩展
        'type'  => 'File',
        // 日志保存目录
        'path'  => APP_PATH.'api'.DS.'runtime'.DS ,
        // 日志记录级别
        'level' => ['debug','notice','error','sql','info'],
    ],
];
