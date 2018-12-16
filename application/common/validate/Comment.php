<?php 
namespace app\common\validate;
use think\Validate;
/**
 * 用户中心
 * Class Personal
 * @author Steed
 * @package app\index\validate
 */
class Comment extends Validate {
	protected $rule = [
        'comment|评论'   => 'require|length:1,150'
    ];
    protected $message = [
		'comment.require'  => ['code'=> -800,'msg'=>'请填写评论内容'],
		'comment.length'   => ['code'=> -801,'msg'=>'评论内容不能超过150个字符']
	];
}