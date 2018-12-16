<?php
namespace app\api\controller;
use think\Cache;
use think\Loader;
use think\Request;
use app\api\controller\Api;
use app\common\logic\Zqrb;
use app\common\logic\User as UserLogic;
/**
 * @ClassName:    User 
 * @Description:  用户 
 * @Created by:   [villager] 
 * @DateTime:     2018-05-21T15:42:58+0800
 */
Class User extends Api 
{
	/**
	 * [__construct:  description] 
	 * @Created by:   [villager] 
	 * @DateTime:     2018-05-21T15:42:58+0800
	 */
	public function  _initialize()
	{
		parent::_initialize();
		$this->logic = new UserLogic;
		$this->validate = validate('User');
		$this->header = Request::instance() -> header();

	}
	public function aa(){
        apiSend(['code'=>SUCCESS_CODE,'msg'=>6666,'status'=>SUCCESS_STATUS,'data'=>666],'json',true);
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
		return $this->logic->login($param);
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
		$param = get_input();
		$data = $this->logic->userInfo($param);
		apiSend(['code'=>SUCCESS_CODE,'msg'=>SUCCESS_MESSAGE,'status'=>SUCCESS_STATUS,'data'=>$data],'json',true);
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
		$result = $this->logic->register($param);
		if($result['status']) {
			apiSend(['code'=>SUCCESS_CODE,'msg'=>SUCCESS_MESSAGE,'status'=>SUCCESS_STATUS,'data'=>$result['data']],'json',true);
		}
		apiSend(['code'=>FAIL_CODE,'msg'=>REGISTER_FAIL,'status'=>FAIL_STATUS,'data'=>$result['data']],'json',true);
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
		$result = $this->logic->userInfoUpdate($param);
		if($result['status']) {
			apiSend(['code'=>SUCCESS_CODE,'msg'=>SUCCESS_MESSAGE,'status'=>SUCCESS_STATUS],'json',true);
		}
		apiSend(['code'=>FAIL_CODE,'msg'=>UPDATE_FAIL,'status'=>FAIL_STATUS],'json',true);
	}
	/**
	 * [sendMsg 发送验证码]
	 * @AuthorHTL
	 * @DateTime  2018-05-29T14:13:46+0800
	 * @return    [type]                   [description]
	 */
	// public function sendCode() {
	// 	$input = get_input();
	// 	//手机号
	// 	$phone = $input['phone'];
	// 	//验证手机号
	// 	if(!$this->validate->check($input)){
	// 		apiSend(['code'=>FAIL_CODE,'msg'=>$this->validate->getError(),'status'=>FAIL_STATUS],'json',true);
	// 	}

	// 	//return $this->online($input);

	// 	$code  = rand(100000,999999);
	// 	Cache::set('code_' . $phone, $code, 300);//5分钟有效
	// 	$data['code'] = $code;
	// 	apiSend(['code'=>SUCCESS_CODE,'msg'=>SUCCESS_MESSAGE,'status'=>SUCCESS_STATUS,'data'=>$data],'json',true);
	// 	// if($code_info['code'] == 200) {
	// 	// 	apiSend(['code'=>SUCCESS_CODE,'msg'=>$code_info['msg'],'status'=>SUCCESS_STATUS,'data'=>$data],'json',true);
	// 	// }
	// 	// apiSend(['code'=>FAIL_CODE,'msg'=>$code_info['msg'],'status'=>FAIL_STATUS,'data'=>$data],'json',true);
	// }
	/**
	 * [logout 用户登出]
	 * @AuthorHTL
	 * @DateTime  2018-06-06T17:47:05+0800
	 * @return    [type]                   [description]
	 */
	public function logout() {
		$input = get_input();
		if(!isset($this->header['uid'])) apiSend(['code'=>FAIL_CODE,'msg'=>LACK_PARAM,'status'=>FAIL_STATUS],'json',true);
		if(!isset($this->header['authorization'])) apiSend(['code'=>FAIL_CODE,'msg'=>LACK_TOKEN,'status'=>FAIL_STATUS],'json',true);
		$uid = $this->header['uid'];
		if(Cache::has('user_token_' . $uid)){
			if($this->header['authorization'] != Cache::get('user_token_'.$uid)) apiSend(['code'=>FAIL_CODE,'msg'=>TOKEN_ERROR,'status'=>FAIL_STATUS],'json',true);
			Cache::rm('user_token_'.$uid);
		}else {
			apiSend(['code'=>FAIL_CODE,'msg'=>LOGOUT_FAIL,'status'=>FAIL_STATUS],'json',true);
		}
		return apiSend(['code'=>SUCCESS_CODE,'msg'=>LOGOUT_SUCCESS,'status'=>SUCCESS_STATUS],'json');
	}
	/**
	 * [online 在线调试]
	 * @AuthorHTL
	 * @DateTime  2018-06-28T17:40:36+0800
	 * @return    [type]                   [description]
	 */
	public function sendCode()
	{
		$input = get_input();
		//手机号
		$phone = $input['phone'];
		//验证手机号
		if(!$this->validate->check($input)){
			apiSend(['code'=>FAIL_CODE,'msg'=>$this->validate->getError(),'status'=>FAIL_STATUS],'json',true);
		}
		$this->zqrb = new Zqrb();
		//查询用户发送验证码的类型 发送post请求数据
		$post_data['var']   = $phone;
		$post_data['type']  = 'phone';
		// 查询用户是否已经注册
		$info = $this->zqrb->userSelect($post_data);
		file_put_contents('cui.txt', print_r($info,true).PHP_EOL);
		if($info['code'] == 200 || $info['code'] == '200') {
			$type = '1'; // 用户登录
			file_put_contents('code.txt', 'user has been registered');
		}else {
			$type = '0'; // 用户注册
		}
		$code_data['phone'] = $phone;
		$code_data['type']  = $type;
		// 发送验证码
		$code_info = $this->zqrb->sendCode($code_data);
		file_put_contents('code_info.txt', print_r($code_info,true).PHP_EOL);
		if($code_info['code'] == 200) {
			apiSend(['code'=>SUCCESS_CODE,'msg'=>$code_info['msg'],'status'=>SUCCESS_STATUS],'json',true);
		}
		apiSend(['code'=>FAIL_CODE,'msg'=>$code_info['msg'],'status'=>FAIL_STATUS],'json',true);
	}
}