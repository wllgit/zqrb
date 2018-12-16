<?php 
namespace app\common\validate;
use think\Validate;
/**
 * 用户中心
 * Class Personal
 * @author Steed
 * @package app\index\validate
 */
class User extends Validate {
	protected $rule = [
        'phone|用户电话'   => 'require|number|regex:/^1[34578]\d{9}$/'
    ];
    protected $message = [
		'phone.require'  => '手机号必填',
		'phone.number'   => '手机号必须是数字',
		'phone.length'   => '手机号格式不正确',
		'phone.regex'    => '手机号格式不正确'
	];
}