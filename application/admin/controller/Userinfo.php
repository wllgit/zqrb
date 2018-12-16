<?php
// +----------------------------------------------------------------------
// | snake
// +----------------------------------------------------------------------
// | Copyright (c) 2016~2022 http://baiyf.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: NickBai <1902822973@qq.com>
// +----------------------------------------------------------------------
namespace app\admin\controller;


use app\admin\model\UserModel;
use app\admin\model\UserType;

class Userinfo extends Base
{
    //用户信息列表
    public function userlist(){
        $userinfo = db('admin')->select();
        $this->assign('userinfo',$userinfo);
        return $this->fetch();
    }
}
