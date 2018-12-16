<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006~2018 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------
use think\Route;
Route::rule('newsDetail/:id','index/News/detail');
return [
    '__pattern__' => [
        'name' => '\w+',
    ],
    '[hello]'     => [
        ':id'   => ['index/hello', ['method' => 'get'], ['id' => '\d+']],
        ':name' => ['index/hello', ['method' => 'post']],
    ],
    'index'        => 'api/Index/index',
    'oauth2/index' => 'api/Oauth2/index',
    'sendCode'     => 'api/User/sendCode',
    'logout'       => 'api/User/logout',
    'interface1'   => 'api/Index/interface1',
    'newsSearch'   => 'api/News/newsSearch',
    'user/update'  => 'api/User/update',
    'aa'  => 'api/User/aa',
    'userInfo'     => 'api/User/read',
    'aboutUs'      => 'api/Config/aboutUs',
    'transmit'     => 'api/News/newsTransmit',
    '__rest__'=>[
        'user'          => 'api/user',
        'news'          => 'api/news',
        'column'        => 'api/column',
        'userColumn'    => 'api/userColumn',
        'userPraise'    => 'api/userPraise',
        'comment'       => 'api/comment',
        'commentPraise' => 'api/userCommentPraise',
        'reply'         => 'api/commentReply',
        'feedback'      => 'api/userFeedback',
        'message'       => 'api/message',
        'userStock'     => 'api/userStock',
        'init'          => 'api/Init'
    ],
    'bannerList' => 'api/NewIndex/bannerList',
    'adverList' => 'api/NewIndex/adverList',
    'advertisements' => 'api/NewIndex/advertisements',
    'collection' => 'api/NewIndex/collection',
    'newsFlash' => 'api/NewIndex/newsFlash',
    'saveNewFlash' => 'api/NewFlash/saveNewFlash',
//    'saveNewFlash' => 'api/NewIndex/saveNewFlash',
    'eNewsPaper' => 'api/NewIndex/eNewsPaper',
    'ePaperDetail' => 'api/NewIndex/ePaperDetail',
    'collectionList' => 'api/NewIndex/collectionList',
    'cronPaper' => 'cron/NewsPaper/eNewsPaper',
    'swoole'    => 'cron/NewsPaper/swoole',
    'client'    => 'cron/NewsPaper/client',
    'adverNewsList'    => 'api/NewIndex/adverNewsList',
    'bannerAdver'    => 'api/NewIndex/bannerAdver',
    'scws'    => 'cron/Scws/scws',
];
