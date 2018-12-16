<?php
namespace app\api\controller;
use think\Request;
use think\Cache;
use app\api\controller\Api;
use app\common\logic\UserPraise as UserPraiseLogic;
/**
 * @ClassName:    UserPraise 
 * @Description:  Detail 
 * @Created by:   [villager] 
 * @DateTime:     2018-06-01T15:36:50+0800
 */
Class UserPraise extends Api 
{
	protected $logic;
	/**
	 * [_initialize:  description] 
	 * @Created by:   [villager] 
	 * @DateTime:     2018-05-15T09:30:54+0800
	 */
	public function _initialize()
	{
		parent::_initialize();
		$this->logic = new UserPraiseLogic();
		$header = Request::instance() -> header();
	}
	/**
	 * 点赞列表
	 * @author    [villager]
	 * @param     [type]     $param1 [description]
	 * @param     [type]     $param2 [description]
	 * @return    [type]     [description]
	 * @DateTime  2018-05-21T15:42:58+0800
	*/
	public function index()
	{
		$param = get_input();
		$list = $this->logic->praiseList($param);
		apiSend(['code'=>SUCCESS_CODE,'msg'=>SUCCESS_MESSAGE,'status'=>SUCCESS_STATUS,'data'=>$list],'json',true);
	}
	/**
	 * 点赞或取消点赞
	 * @author    [villager]
	 * @param     [type]     $param1 [description]
	 * @param     [type]     $param2 [description]
	 * @return    [type]     [description]
	 * @DateTime  2018-05-21T15:42:58+0800
	*/
	public function save()
	{
		$param = get_input();
		$result = $this->logic->praiseUpdate($param);
		if($result['status']) {
			apiSend(['code'=>SUCCESS_CODE,'msg'=>SUCCESS_MESSAGE,'status'=>SUCCESS_STATUS],'json',true);
		}
		apiSend(['code'=>FAIL_CODE,'msg'=>OPERATE_FAIL,'status'=>FAIL_STATUS],'json',true);
	}
	/**
	 * [read 用户是否点赞]
	 * @AuthorHTL
	 * @DateTime  2018-06-11T10:15:31+0800
	 * @return    [type]                   [description]
	 */
	public function read()
	{
		$param = get_input();
		$result = $this->logic->isPraise($param);
		apiSend(['code'=>SUCCESS_CODE,'msg'=>SUCCESS_MESSAGE,'status'=>SUCCESS_STATUS,'data'=>$result],'json',true);
	}
}