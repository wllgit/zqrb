<?php
/**
 * @ClassName:    Name 
 * @Description:  Detail 
 * @Created by:   [villager] 
 * @DateTime:     2018-05-28T14:10:59+0800
 */
namespace app\api\controller;
use app\common\logic\Message as messageLogic;
use app\api\controller\Api;
Class Message extends Api {
	protected $logic;
	/**
	 * [_initialize:  description] 
	 * @Created by:   [villager] 
	 * @DateTime:     2018-05-15T09:30:54+0800
	 */
	public function _initialize()
	{
		parent::_initialize();
		$this->logic = new messageLogic;
	}
	/**
	 * 消息列表
	 * @author    [villager]
	 * @return    [json]     [json格式的消息列表]
	 * @DateTime  2018-05-28T14:10:59+0800
	*/
	public function index()
	{
		$param = get_input();  //消息
		$data = $this->logic->messageList($param);
		return apiSend(['code'=>SUCCESS_CODE,'msg'=>SUCCESS_MESSAGE,'status'=>SUCCESS_STATUS,'data'=>$data],'json');
	}
	/**
	 * description
	 * @author    [villager]
	 * @param     [type]     $param1 [description]
	 * @param     [type]     $param2 [description]
	 * @return    [type]     [description]
	 * @DateTime  2018-05-28T14:10:59+0800
	*/
	public function read()
	{
		$param = get_input();
		$data = $this->logic->messageDetail($param);
		return apiSend(['code'=>SUCCESS_CODE,'msg'=>SUCCESS_MESSAGE,'status'=>SUCCESS_STATUS,'data'=>$data],'json');
	}
	/**
	 * [delete 删除个人消息]
	 * @AuthorHTL
	 * @DateTime  2018-06-21T17:16:57+0800
	 * @return    [type]                   [description]
	 */
	public function delete()
	{
		$param = get_input();
		$result = $this->logic->messageDelete($param);
		if($result['status']) {
			apiSend(['code'=>SUCCESS_CODE,'msg'=>SUCCESS_MESSAGE,'status'=>SUCCESS_STATUS],'json',true);
		}
		apiSend(['code'=>FAIL_CODE,'msg'=>OPERATE_FAIL,'status'=>FAIL_STATUS],'json',true);
	}
}