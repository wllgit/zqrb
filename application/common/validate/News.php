<?php 
namespace app\common\validate;
use think\Validate;
/**
 * 用户中心
 * Class Personal
 * @author Steed
 * @package app\index\validate
 */
class News extends Validate {
	protected $rule = [
        'origin_id|新闻源id'   			=> 'require|number',
        'title|新闻标题'   				=> 'require',
        'author|新闻作者'   				=> 'require',
        'keywords|关键字'   				=> 'require',
        'summary|新闻摘要'   			=> 'require',
        'detail|新闻详情'   				=> 'require',
        'source|新闻来源'   				=> 'require',
        'detail_type|新闻详情类型'   	    => 'number|regex:/^[12]$/',
        'click_num|点击数量'   			=> 'number',
        'collect_num|收藏数量'   		=> 'number',
        'praise_num|点赞数量'   			=> 'number',
        'comments_num|评论数量'   		=> 'number',
        'transmit_num|转发数量'   		=> 'number',
        'allow_comment|是否允许评论'		=> 'number|regex:/^[01]$/',
        'is_show|是否显示'       		=> 'number|regex:/^[01]$/',
        'publish_time|发布时间'  		=> 'require|number|length:10',
        'is_ad|是否显示广告'         		=> 'number|regex:/^[01]$/',
        'is_repost|是否可转发'			=> 'number|regex:/^[01]$/',
        'is_recommend|是否推送相关新闻'	=> 'number|regex:/^[01]$/',
        'is_banner|是否设置为banner'		=> 'number|regex:/^[01]$/',
        'mask|验证信息'          		=> 'require|length:32'
    ];
    protected $message = [
		'origin_id.require'    => ['code'=> -201,'msg'=>'缺少新闻源id'],
		'origin_id.number'     => ['code'=> -202,'msg'=>'新闻源id格式不正确'],
		'title.require'        => ['code'=> -203,'msg'=>'缺少新闻标题'],
		'author.require'       => ['code'=> -204,'msg'=>'缺少新闻作者'],
		'keywords.require'     => ['code'=> -205,'msg'=>'缺少新闻关键词'],
		'summary.require'      => ['code'=> -206,'msg'=>'缺少新闻摘要'],
		'detail.require'       => ['code'=> -207,'msg'=>'缺少新闻详情'],
		'source.require'       => ['code'=> -208,'msg'=>'缺少新闻来源'],
		'click_num.number'     => ['code'=> -209,'msg'=>'点击数量必须为数字'],
		'collect_num.number'   => ['code'=> -210,'msg'=>'收藏数量必须为数字'],
		'praise_num.number'    => ['code'=> -211,'msg'=>'点赞数量必须为数字'],
		'comments_num.number'  => ['code'=> -212,'msg'=>'评论数量必须为数字'],
		'transmit_num.number'  => ['code'=> -213,'msg'=>'转发数量必须为数字'],
		'allow_comment.number' => ['code'=> -214,'msg'=>'是否允许评论状态不正确'],
		'allow_comment.regex'  => ['code'=> -215,'msg'=>'是否允许评论状态只有0或1'],
		'is_show.number'       => ['code'=> -216,'msg'=>'是否显示状态不正确'],
		'is_show.regex'        => ['code'=> -217,'msg'=>'是否显示状态只有0或1'],
		'publish_time.require' => ['code'=> -218,'msg'=>'缺少发布时间'],
		'publish_time.number'  => ['code'=> -219,'msg'=>'发布时间格式不正确'],
		'publish_time.length'  => ['code'=> -219,'msg'=>'发布时间格式不正确'],
		'is_ad.number'         => ['code'=> -220,'msg'=>'是否显示广告状态不正确'],
		'is_ad.regex'          => ['code'=> -221,'msg'=>'是否显示广告状态只有0或1'],
		'is_repost.number'     => ['code'=> -222,'msg'=>'是否可转发状态不正确'],
		'is_repost.regex'      => ['code'=> -223,'msg'=>'是否可转发状态只有0或1'],
		'is_recommend.number'  => ['code'=> -224,'msg'=>'是否推荐相关新闻状态不正确'],
		'is_recommend.regex'   => ['code'=> -225,'msg'=>'否推荐相关新闻状态只有0或1'],
		'is_banner.number'     => ['code'=> -226,'msg'=>'是否设置为banner状态不正确'],
		'is_banner.regex'      => ['code'=> -227,'msg'=>'是否设置为banner状态只有0或1'],
		'detail_type.number'   => ['code'=> -229,'msg'=>'新闻详情类型格式不正确'],
		'detail_type.regex'    => ['code'=> -230,'msg'=>'新闻详情类型只有1或2'],
		'mask.require'         => ['code'=> -300,'msg'=>'缺少验证信息'],
		'mask.length'          => ['code'=> -301,'msg'=>'验证信息格式不正确'],
	];
}