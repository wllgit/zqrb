<?php
namespace app\api\controller;
use app\api\controller\Api;
use app\common\logic\Column as ColumnLogic;
/**
 * @ClassName:    Name 
 * @Description:  Detail 
 * @Created by:   [villager] 
 * @DateTime:     2018-06-01T15:36:50+0800
 */
Class Column extends Api 
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
		$this->logic = new ColumnLogic;
	}
	/**
	 * 首页列表
	 * @author    [villager]
	 * @param     [type]     $param1 [description]
	 * @param     [type]     $param2 [description]
	 * @return    [type]     [description]
	 * @DateTime  2018-05-21T15:42:58+0800
	*/
	public function index()
	{
		$param = get_input();
		$list = $this->logic->columnList($param);
		apiSend(['code'=>SUCCESS_CODE,'msg'=>SUCCESS_MESSAGE,'status'=>SUCCESS_STATUS,'data'=>$list],'json',true);
	}
	/**
	 * description
	 * @author    [villager]
	 * @param     [type]     $param1 [description]
	 * @param     [type]     $param2 [description]
	 * @return    [type]     [description]
	 * @DateTime  2018-05-21T15:42:58+0800
	*/
	public function read()
	{
		return 'this is module of User resource interface test read';
	}
	/**
	 * description
	 * @author    [villager]
	 * @param     [type]     $param1 [description]
	 * @param     [type]     $param2 [description]
	 * @return    [type]     [description]
	 * @DateTime  2018-05-21T15:42:58+0800
	*/
	public function edit()
	{
		return 'this is module of User resource interface test edit';
	}
	/**
	 * 用户注册
	 * @author    [villager]
	 * @param     [type]     $param1 [description]
	 * @param     [type]     $param2 [description]
	 * @return    [type]     [description]
	 * @DateTime  2018-05-21T15:42:58+0800
	*/
	public function save()
	{
		$param = get_input();
		$result = $this->logic->columnAdd($param);
		if($result['status']) {
			apiSend(['code'=>SUCCESS_CODE,'msg'=>SUCCESS_MESSAGE,'status'=>SUCCESS_STATUS,'data'=>$result['data']],'json',true);
		}
		apiSend(['code'=>FAIL_CODE,'msg'=>ADD_FAIL,'status'=>FAIL_STATUS],'json',true);
	}
	/**
	 * description
	 * @author    [villager]
	 * @param     [type]     $param1 [description]
	 * @param     [type]     $param2 [description]
	 * @return    [type]     [description]
	 * @DateTime  2018-05-21T15:42:58+0800
	*/
	public function delete()
	{
		return 'this is module of User resource interface test delete';
	}
	/**
	 * 用户登录
	 * @author    [villager]
	 * @param     [type]     $param1 [description]
	 * @param     [type]     $param2 [description]
	 * @return    [type]     [description]
	 * @DateTime  2018-05-21T15:42:58+0800
	*/
	public function update()
	{
		$param = get_input();
		$data = $this->logic->login($param);
		return apiSend(['code'=>SUCCESS_CODE,'msg'=>SUCCESS_MESSAGE,'status'=>SUCCESS_STATUS,'data'=>$data],'json');
	}
}