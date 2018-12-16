<?php
/**
 * @ClassName:    Name 
 * @Description:  Detail 
 * @Created by:   [villager] 
 * @DateTime:     2018-05-28T14:10:59+0800
 */
namespace app\api\controller;
use app\common\logic\News as newsLogic;
use app\api\controller\Api;
Class News extends Api {
	protected $logic;
	/**
	 * [_initialize:  description] 
	 * @Created by:   [villager] 
	 * @DateTime:     2018-05-15T09:30:54+0800
	 */
	public function _initialize()
	{
		parent::_initialize();
		$this->logic = new newsLogic;
	}
	/**
	 * 新闻列表
	 * @author    [villager]
	 * @return    [json]     [json格式的新闻列表]
	 * @DateTime  2018-05-28T14:10:59+0800
	*/
	public function index()
	{
		$param = get_input();
		if((isset($param['action']) && $param['action'] == 'hot') || $param['column_id'] == 2) {
			$data = $this->logic->hotNews($param);//热点栏目新闻
		}else {   //普通栏目新闻
			$data = $this->logic->newsList($param);
		}
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
		$data = $this->logic->newsDetail($param);
		return apiSend(['code'=>SUCCESS_CODE,'msg'=>SUCCESS_MESSAGE,'status'=>SUCCESS_STATUS,'data'=>$data],'json');
	}
	/**
	 * [save 保存新闻]
	 * @AuthorHTL
	 * @DateTime  2018-06-07T16:54:36+0800
	 * @return    [type]                   [description]
	 */
	public function save()
	{
		$param = get_input();
		$act = $param['act'];
		if(strtoupper($act) == 'ADD') {// 添加新闻
			$result = $this->logic->newsAdd($param);
			$msg = ADD_SUCCESS;
		}else if(strtoupper($act) == 'DELETE') { // 删除新闻
			$result = $this->logic->newsDelete($param);
			$msg = DELETE_SUCCESS;
		}
		if($result['status']) {
			apiSend(['code'=>SUCCESS_CODE,'msg'=>$msg,'status'=>SUCCESS_STATUS],'json',true,true);
		}
		apiSend(['code'=>FAIL_CODE,'msg'=>FAIL_MESSAGE,'status'=>FAIL_STATUS],'json',true,true);
	}
	/**
	 * [newsSearch 新闻搜索]
	 * @AuthorHTL
	 * @DateTime  2018-06-11T18:52:44+0800
	 * @return    [type]                   [description]
	 */
	public function newsSearch()
	{
		$param = get_input();
		$data = $this->logic->newsSearch($param);
		return apiSend(['code'=>SUCCESS_CODE,'msg'=>SUCCESS_MESSAGE,'status'=>SUCCESS_STATUS,'data'=>$data],'json');
	}
	/**
	 * [newsTransmit 新闻转发]
	 * @AuthorHTL
	 * @DateTime  2018-06-28T10:30:49+0800
	 * @return    [type]                   [description]
	 */
	public function newsTransmit()
	{
		$param = get_input();
		$result = $this->logic->newsTransmit($param);
		if($result['status']) {
			apiSend(['code'=>SUCCESS_CODE,'msg'=>SUCCESS_MESSAGE,'status'=>SUCCESS_STATUS,'data'=>$result['data']],'json',true);
		}
		apiSend(['code'=>FAIL_CODE,'msg'=>FAIL_MESSAGE,'status'=>FAIL_STATUS],'json',true);
	}
}