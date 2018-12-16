<?php
/**
 * @ClassName:    Init 
 * @Description:  Detail 
 * @Created by:   [villager] 
 * @DateTime:     2018-05-28T14:10:59+0800
 */
namespace app\api\controller;
use app\common\logic\AppVersion as AppVersionLogic;
use app\api\controller\Api;
Class Init extends Api {
	protected $logic;
	/**
	 * [_initialize:  description] 
	 * @Created by:   [villager] 
	 * @DateTime:     2018-05-15T09:30:54+0800
	 */
	public function _initialize()
	{
		parent::_initialize();
		$this->logic = new AppVersionLogic;
	}
	/**
	 * 初始化列表
	 * @author    [villager]
	 * @return    [json]     [json格式的初始化列表]
	 * @DateTime  2018-05-28T14:10:59+0800
	*/
	public function index()
	{
		$param = get_input();  //初始化
		$data = $this->logic->checkVersion($param);
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
		$data = $this->logic->InitDetail($param);
		return apiSend(['code'=>SUCCESS_CODE,'msg'=>SUCCESS_MESSAGE,'status'=>SUCCESS_STATUS,'data'=>$data],'json');
	}
	/**
	 * [delete 删除个人初始化]
	 * @AuthorHTL
	 * @DateTime  2018-06-21T17:16:57+0800
	 * @return    [type]                   [description]
	 */
	public function delete()
	{
		$param = get_input();
		$result = $this->logic->InitDelete($param);
		if($result['status']) {
			apiSend(['code'=>SUCCESS_CODE,'msg'=>SUCCESS_MESSAGE,'status'=>SUCCESS_STATUS],'json',true);
		}
		apiSend(['code'=>FAIL_CODE,'msg'=>OPERATE_FAIL,'status'=>FAIL_STATUS],'json',true);
	}
}