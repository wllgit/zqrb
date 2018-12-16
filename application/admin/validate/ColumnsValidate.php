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

class ColumnsValidate extends Validate
{
    protected $rule = [
        'title|栏目名称'  => 'max:20|unique:column',
        'parent_id|父级栏目'  => 'egt:1',
    ];
    protected $message = [
		'title.unique'  => '该栏目已经存在',
		'parent_id.egt' => '请选择父级栏目'
	];
	protected $scene = [
		'first' => ['title'],
		'second' => ['parent_id','title']
	];
}