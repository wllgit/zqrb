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
namespace app\admin\validate;

use think\Validate;

class UserValidate extends Validate
{
    protected $rule = [
        'name|用户名称'  => 'max:20|unique:admin',
        'role_id|角色id'    => 'require|egt:1|number',
    ];
    protected $message = [
		'name.unique'  => '管理员已经存在',
		'role_id.require'   => '请选择管理员角色',
		'role_id.egt'   => '请选择管理员角色',
		'role_id.number'   => '请选择管理员角色',
	];

}