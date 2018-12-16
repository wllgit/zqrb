<?php
/**
 * @ClassName:    Name 
 * @Description:  Detail 
 * @Created by:   [villager] 
 * @DateTime:     2018-05-28T14:10:59+0800
 */
namespace app\api\controller;
use app\common\logic\Comment as commentLogic;
use app\api\controller\Api;
Class Comment extends Api {
	protected $logic;
	/**
	 * [_initialize:  description] 
	 * @Created by:   [villager] 
	 * @DateTime:     2018-05-15T09:30:54+0800
	 */
	public function _initialize()
	{
		parent::_initialize();
		$this->logic = new commentLogic;
	}
	/**
	 * 评论列表
	 * @author    [villager]
	 * @return    [json]     [json格式的评论列表]
	 * @DateTime  2018-05-28T14:10:59+0800
	*/
	public function index()
	{
		$param = get_input();
		$data = $this->logic->commentList($param);
		return apiSend(['code'=>SUCCESS_CODE,'msg'=>SUCCESS_MESSAGE,'status'=>SUCCESS_STATUS,'data'=>$data],'json');
	}
	/**
	 * description 评论详情
	 * @author    [villager]
	 * @param     [type]     $param1 [description]
	 * @param     [type]     $param2 [description]
	 * @return    [type]     [description]
	 * @DateTime  2018-05-28T14:10:59+0800
	*/
	public function read()
	{
		$param = get_input();
		$data = $this->logic->commentDetail($param);
		return apiSend(['code'=>SUCCESS_CODE,'msg'=>SUCCESS_MESSAGE,'status'=>SUCCESS_STATUS,'data'=>$data],'json');
	}
	/**
	 * [save 保存评论]
	 * @AuthorHTL
	 * @DateTime  2018-06-07T16:54:36+0800
	 * @return    [type]                   [description]
	 */
	public function save()
	{
		$param = get_input();
		$result = $this->logic->commentAdd($param);
		if($result['status']) {
			apiSend(['code'=>SUCCESS_CODE,'msg'=>SUCCESS_MESSAGE,'status'=>SUCCESS_STATUS],'json',true);
		}
		apiSend(['code'=>FAIL_CODE,'msg'=>OPERATE_FAIL,'status'=>FAIL_STATUS],'json',true);
	}
}