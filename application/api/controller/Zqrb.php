<?php
/**
 * @ClassName:    Name 
 * @Description:  Detail 
 * @Created by:   [villager] 
 * @DateTime:     2018-05-28T14:10:59+0800
 */
namespace app\api\controller;
use think\Controller;
use app\common\logic\Zqrb as ZqrbLogic;
Class Zqrb  extends Controller{
	/**
	 * [_initialize:  description] 
	 * @Created by:   [villager] 
	 * @DateTime:     2018-05-15T09:30:54+0800
	 */
	public function _initialize()
	{
		parent::_initialize();
		$this->logic = new ZqrbLogic();
	}
	public function selectUser()
	{
		$param = get_input();
		$info = $this->logic->userSelect($param);
		return $info;
	}
	/**
	 * 新闻列表
	 * @author    [villager]
	 * @return    [json]     [json格式的新闻列表]
	 * @DateTime  2018-05-28T14:10:59+0800
	*/
	public function sendCode()
	{
		$param = get_input();
		$info = $this->logic->sendCode($param);
		return $info;
	}
	/**
	 * description
	 * @author    [villager]
	 * @param     [type]     $param1 [description]
	 * @param     [type]     $param2 [description]
	 * @return    [type]     [description]
	 * @DateTime  2018-05-28T14:10:59+0800
	*/
	public function checkCode()
	{
		$param = get_input();
		$info = $this->logic->checkCode($param);
		return $info;
	}
	/**
	 * [save 保存新闻]
	 * @AuthorHTL
	 * @DateTime  2018-06-07T16:54:36+0800
	 * @return    [type]                   [description]
	 */
	public function register()
	{
		$param = get_input();
		$info = $this->logic->userRegister($param);
		return $info;
	}
	/**
	 * [ZqrbSearch 新闻搜索]
	 * @AuthorHTL
	 * @DateTime  2018-06-11T18:52:44+0800
	 * @return    [type]                   [description]
	 */
	public function login()
	{
		$param = get_input();
		$info = $this->logic->userLogin($param);
		return $info;
	}
	/**
	 * [ZqrbTransmit 新闻转发]
	 * @AuthorHTL
	 * @DateTime  2018-06-28T10:30:49+0800
	 * @return    [type]                   [description]
	 */
	public function bind()
	{
		$param = get_input();
		$info = $this->logic->userBind($param);
		return $info;
	}
	/**
	 * [updateUser 修改用户信息]
	 * @AuthorHTL
	 * @DateTime  2018-06-28T10:30:49+0800
	 * @return    [type]                   [description]
	 */
	public function updateUser()
	{
		$param = get_input();
		$info = $this->logic->userUpdate($param);
		return $info;
	}
}