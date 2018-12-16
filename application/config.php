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

    'url_route_on' => true,

    'trace' => [
        'type' => 'html', // 支持 socket trace file
    ],
    //各模块公用配置
    'extra_config_list' => ['database', 'route', 'validate'],
    //临时关闭日志写入
    'log' => [
        // 日志记录方式，内置 file socket 支持扩展
        'type'  => 'test'   
    ],

    'app_debug' => true,
    'default_filter' => ['strip_tags', 'htmlspecialchars'],



    // +----------------------------------------------------------------------
    // | 缓存设置
    // +----------------------------------------------------------------------
    'cache' => [
        // 驱动方式
        'type' => 'file',
        // 缓存保存目录
        'path' => CACHE_PATH,
        // 缓存前缀
        'prefix' => '',
        // 缓存有效期 0表示永久缓存
        'expire' => 0,
        'host' => '192.168.6.55',
        'port' => 11211,
    ],

    //加密串
    'salt' => 'wZPb~yxvA!ir38&Z',
    //天眼查域名
    'skyEyesHost'=>'https://static.tianyancha.com/',
    //备份数据地址
    'back_path' => APP_PATH .'../back/',

    //  'default_return_type'    => 'json'
    'MODULE_ALLOW_LIST' => array ('admin'),
    'DEFAULT_MODULE' => 'admin',
    'zqrb' => [
        'key' => 'zqrbcn2018',
        'interface_url' => 'http://passport.zqrb.cn/api/method.php',
        'domain' => 'https://zqrb.stockalert.cn'
    ]

];
