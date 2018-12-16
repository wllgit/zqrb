<?php
namespace app\common\logic;
use think\Cache;
use think\Request;
use app\common\logic\Base;
use app\common\logic\Zqrb;
use app\common\model\WordBlackList;
use app\common\model\User as UserModel;
use app\common\model\CommentReply as CommentReplyModel;
/**
 * @ClassName:    News 
 * @Description:  新闻类 
 * @Created by:   [villager] 
 * @DateTime:     2018-05-28T14:13:06+0800
 */
Class User extends Base 
{
	protected $page = 1;
	protected $offset = 10;
	protected $field = '';
	protected $zqrbInfo = '';
	/**
	 * [initialize:  初始化绑定news模型] 
	 * @Created by:   [villager] 
	 * @DateTime:     2018-05-16T15:16:16+0800
	 */
	protected function initialize() {
        parent::initialize();
        $this->zqrb = new Zqrb();
        $this->model = new UserModel;
        $this->validate = validate('User');
    }
	/**
	 * [register 用户注册]
	 * @AuthorHTL villager
	 * @DateTime  2018-05-29T14:15:00+0800
	 * @param     [type array]                   $param [传参]
	 * @return    [type]                          [description]
	 */
	public function register($param)
	{
		// 检验验证码
		$check_result = $this->zqrb->checkCode($param);
		if($check_result['code'] != 200) {
			apiSend(['code'=>FAIL_CODE,'msg'=>$check_result['msg'],'status'=>FAIL_STATUS],'json',true);
		}
		$local_param['phone'] = $zqrb_param['phone'] = $param['phone'];
		$zqrb_param['code']   = $param['code'];
		$zqrb_param['type']   = 'phone';
		//第三方openID
		if(!empty($param['wx_openid'])) {                                              // 微信手机号绑定注册
			$local_param['wx_openid'] = $zqrb_param['openid'] = $param['wx_openid'];
			$zqrb_param['type']    = 'wx';
		}else if(!empty($param['qq_openid'])) {                                        // qq手机号绑定注册
			$local_param['qq_openid'] = $zqrb_param['openid'] = $param['qq_openid'];
			$zqrb_param['type']    = 'qq';
		}else if(!empty($param['sina_openid'])) {                                     // 新浪手机号绑定注册
			$local_param['sina_openid'] = $zqrb_param['openid']  = $param['sina_openid'];
			$zqrb_param['type']    = 'sina';
		}
		$zqrb_param['nikename'] = !empty($param['nickname']) ? $param['nickname'] : null;//昵称
		$local_param['avatar']   = $zqrb_param['avatar']   = !empty($param['avatar']) ? $param['avatar'] : null;//用户头像
		$register_result = $this->zqrb->userRegister($zqrb_param);                    // 证券日报端用户注册
		if($register_result['code'] != 200) {                                         // 注册失败返回错误信息
			apiSend(['code'=>FAIL_CODE,'msg'=>$register_result['msg'],'status'=>FAIL_STATUS],'json',true);
		}
		// 查询用户信息
		$zqrb_param = ['var' => $register_result['data']['userid'],'type' => 'uid'];
		$zqrb_userInfo = $this->zqrb->userSelect($zqrb_param);
		$local_param['user_id'] = $register_result['data']['userid']; // 证券日报端用户源id
		if(!empty($zqrb_userInfo['data']['nikeName'])) {
			$nickname = $zqrb_userInfo['data']['nikeName']; // 用户名
		}else if(!empty($zqrb_param['nikename'])){
			$nickname = $zqrb_param['nikename'];
		}else {
			$nickname = $zqrb_userInfo['data']['userName']; // 用户昵称
		}
		// $nickname    = !empty($param['nickname']) ? $param['nickname'] : '用户'.get_rand_char(3). rand(1000,9999); // 用户昵称
        $local_param['nickname'] = $nickname;
		$local_param['sex']         = !empty($param['sex']) ? $param['sex'] : 0;              //用户性别 默认0:未知,1:男,2:女
		$local_param['birthday']    = !empty($param['birthday']) ? $param['birthday'] : null;    //用户生日
		$local_param['scs_id']      = 'scs_'.get_rand_char(5). rand(10000,99999);             //用户唯一标示
		$local_param['token']       = get_rand_char(5). rand(10000,99999);                    //用户token
		$local_param['token_expire_time'] = time() + 604800;                                  //token失效时间
		$local_param['password']        = $zqrb_userInfo['data']['password'];                            //用户密码
		$local_param['last_login_ip']   = Request::instance()->ip();                          //用户登录IP
		$local_param['last_login_time'] = time();
		$query['data'] = $local_param;
		$result['status'] = $this->model->index('userAdd',$query);
		if($result['status']) {
			$result['data']['user_id'] = $this->model->id;                //新用户id
			$result['data']['phone']  = $local_param['phone'];
			$result['data']['token']  = $local_param['token'];
			$result['data']['avatar'] = $local_param['avatar'];
			$result['data']['sex']    = $local_param['sex'];
			$result['data']['birthday'] = $local_param['birthday'];
			$result['data']['nickname'] = $nickname;
			Cache::set('user_token_' . $this->model->id,$local_param['token'],604800);
			apiSend(['code'=>SUCCESS_CODE,'msg'=>REGISTER_SUCCESS,'status'=>SUCCESS_STATUS,'data'=>$result['data']],'json',true);
		}
		apiSend(['code'=>FAIL_CODE,'msg'=>CODE_ERROR,'status'=>FAIL_STATUS],'json',true);
	}
	/**
	 * [login 用户登录]
	 * @AuthorHTL villager
	 * @DateTime  2018-05-29T16:58:20+0800
	 * @param     [type array]                   $param [传参]
	 * @return    [type]                          [description]
	 */
	public function login($param) {
		$phone = '';
		$is_phone_login = false;
		// 1.第三方登录绑定手机号
		if(!empty($param['phone'])  && (!empty($param['wx_openid']) || !empty($param['qq_openid']) || !empty($param['sina_openid']))) {
			//验证手机号
			if(!$this->validate->check($param)){
				apiSend(['code'=>FAIL_CODE,'msg'=>$this->validate->getError(),'status'=>FAIL_STATUS],'json',true);
			}
			/******----调用证券日报查询用户接口开始----******/
			$zqrb_param['type'] = 'phone';
			$zqrb_param['var']  = $param['phone'];
			$zqrb_info = $this->zqrb->userSelect($zqrb_param);
			if($zqrb_info['code'] == -1 || $zqrb_info['code'] == -113) { // 用户手机号尚未在证券日报端注册
				return $this->register($param);
			}
			/******----调用证券日报查询用户接口结束----******/
			$param['user_info'] = $zqrb_info['data'];
			return $this->bind($param);
		}else if(!empty($param['wx_openid']) && empty($param['phone'])) {     // 2.微信登录
			// $where = ['wx_openid'=>$param['wx_openid'],'is_delete' => 0];
			// $post_field = ['wx_openid'=>$param['wx_openid']];
			$zqrb_param['var'] = $param['wx_openid'];
			$zqrb_param['type'] = 'wx_openid';
			$login_result = $this->zqrb->userLogin($zqrb_param);
		}else if(!empty($param['qq_openid']) && empty($param['phone'])) {     // 3.qq登录
			// $where = ['qq_openid'=>$param['qq_openid'],'is_delete' => 0];
			// $post_field = ['qq_openid'=>$param['qq_openid']];
			$zqrb_param['var'] = $param['qq_openid'];
			$zqrb_param['type'] = 'qq_openid';
			$login_result = $this->zqrb->userLogin($zqrb_param);
		}else if(!empty($param['sina_openid']) && empty($param['phone'])) {   // 4.新浪微博登录
			// $where = ['sina_openid'=>$param['sina_openid'],'is_delete' => 0];
			// $post_field = ['sina_openid'=>$param['sina_openid']];
			$zqrb_param['var'] = $param['sina_openid'];
			$zqrb_param['type'] = 'sina_openid';
			$login_result = $this->zqrb->userLogin($zqrb_param);
		}else {                                                               // 5.手机号验证码登录或注册
			//验证手机号
			if(!$this->validate->check($param)){
				apiSend(['code'=>FAIL_CODE,'msg'=>$this->validate->getError(),'status'=>FAIL_STATUS],'json',true);
			}
			$phone = $param['phone'];//手机号
			//是否缺少验证码
			if(empty($param['code'])) apiSend(['code'=>FAIL_CODE,'msg'=>LACK_CODE_MSG,'status'=>FAIL_STATUS],'json',true);
			//验证码是否失效
			// if(!Cache::has('code_' . $phone)) apiSend(['code'=>FAIL_CODE,'msg'=>CODE_EXPIRED,'status'=>FAIL_STATUS],'json',true);
			// $code = $param['code'];
			//验证码是否正确
			// if(!check_code($phone,$code)) apiSend(['code'=>FAIL_CODE,'msg'=>CODE_ERROR,'status'=>FAIL_STATUS,'data'=>$code],'json',true);
			// $where=['phone'=>$phone,'is_delete' => 0];
			// $post_field = ['phone'=>$param['phone']];
			$is_phone_login = true;
			$zqrb_param['var']  = $param['phone'];
			$zqrb_param['code'] = $param['code'];
			$zqrb_param['type'] = 'phone';

			$zqrb_info = $this->zqrb->userSelect($zqrb_param);
			if($zqrb_info['code'] == -1 || $zqrb_info['code'] == -113) {
				return $this->register($param);
			}
			$login_result = $this->zqrb->userLogin($zqrb_param);
		}
		//要查询的字段id、昵称、密码、token、token过期时间、头像、手机号
		// $field = 'id as user_id,nickname,avatar,phone,sex,birthday';
		//生成查询条件
		// $query = compact('field','where');
		// $info = $this->model->index('userInfo',$query);//再model中执行查询
		/******----调用证券日报查询用户接口开始----******/
		//登陆成功
		if($login_result['code'] == 200 || $login_result['code'] == "200") {      // 用户在证券日报端登陆成功
			$uid   = $login_result['data']['uid'];
			$phone = $login_result['data']['phone'];
			$select_param['var'] = $uid;
			$select_param['type'] = 'uid';
			//查询证券日报端用户信息
			if(empty($zqrb_info)) {
				$zqrb_userInfo = $this->zqrb->userSelect($select_param);
			}else{
				$zqrb_userInfo = $zqrb_info;
			}
			$where = ['user_id' => $uid];
			$field = 'id as user_id,nickname,avatar,phone,sex,birthday';
			$query = compact('where','field');
			$info  = $this->model->index('userInfo',$query);
			$token = get_rand_char(5). rand(10000,99999);                   // 用户token
			$token_expire_time = time() + 604800;                           // token失效时间

			$nickname = '';
			if(!empty($zqrb_userInfo['data']['nikeName'])) {                // 用户自设昵称
				$nickname = $zqrb_userInfo['data']['nikeName'];     
			}else if(!empty($param['nickname'])){                   // 微信登录时用微信昵称
				$nickname = $param['nickname'];
			}else{
				$nickname = $zqrb_userInfo['data']['userName'];
			}

			$avatar = '';
			if(!empty($zqrb_userInfo['data']['imgorigin'])) {
				if(strpos($zqrb_userInfo['data']['imgorigin'],'http') !== false){ 
				 	$avatar = $zqrb_userInfo['data']['imgorigin'];
				}else{
				 	$avatar = 'http://passport.zqrb.cn/'.$zqrb_userInfo['data']['imgorigin'];
				}
			}else if(!empty($param['avatar'])){
				$avatar = $param['avatar'];
			}

			if(empty($info)) {                                              // 证券日报端用户，且并未在APP注册，并将用户信息保存到APPserver
				$info['phone'] = $phone;
				$info['token'] = $token;
				$info['nickname'] = $nickname; // 昵称
				$info['avatar'] = $avatar;     // 头像
				$info['sex'] = 0;
				$info['birthday'] = null;
				$save_data = $info;
				$save_data['scs_id']      = 'scs_'.get_rand_char(5). rand(10000,99999);             //用户唯一标示
				$save_data['user_id']     = $uid;
				$save_data['phone']       = $phone;
				$save_data['password']    = $zqrb_userInfo['data']['password'];
				$save_data['last_login_ip']   = Request::instance()->ip();                          //用户登录IP
				$save_data['last_login_time'] = time();
				$save_data['token_expire_time'] = $token_expire_time;
				$save_query['data'] = $save_data;
				$result['status'] = $this->model->index('userAdd',$save_query);// APP端静默注册
				$info['user_id'] = $this->model->id;
			}else {                                                            // 在APP端已经注册，更新用户登录在APPserver状态
				$update_time = time();
				$data = compact('token','token_expire_time','update_time');
				$info['nickname'] = $data['nickname'] = $nickname;
				$info['avatar']   = $data['avatar']   = $avatar;
				$query = [];
				$query['data'] = $data;
				$query['where'] = $where;
				$info['token'] = $token;
				$result['status'] = $this->model->index('userUpdate',$query);        //更新token和其失效时间
			}
			if(!$result['status']) {
				apiSend(['code'=>FAIL_CODE,'msg'=>LOGIN_FAIL,'status'=>FAIL_STATUS],'json',true);
			}
			Cache::set('user_token_' . $info['user_id'],$token,604800);
			apiSend(['code'=>SUCCESS_CODE,'msg'=>LOGIN_SUCCESS,'status'=>SUCCESS_STATUS,'data'=>$info],'json',true);
		}else if($login_result['code'] == -118 || $login_result['code'] == -119 || $login_result['code'] == -120) {// 用户在证券日报端，第三方登录失败
			apiSend(['code'=>NULL_CODE,'msg'=>NULL_USER,'status'=>NULL_STATUS],'json',true);
		}else {																										// 一般登录失败
			apiSend(['code'=>FAIL_CODE,'msg'=>$login_result['msg'],'status'=>FAIL_STATUS],'json',true);
		}
	}
	/**
	 * [bind 绑定第三方信息接口]
	 * @AuthorHTL
	 * @DateTime  2018-06-25T16:16:47+0800
	 * @param     [type]                   $param [description]
	 * @return    [type]                          [description]
	 */
	public function bind($param)
	{
		// 登录证券日报端
		$zqrb_param['var']  = $param['phone'];
		$zqrb_param['code'] = $param['code'];
		$zqrb_param['type'] = 'phone';
		$login_result = $this->zqrb->userLogin($zqrb_param);
		if($login_result['code'] != 200) {
			apiSend(['code'=>FAIL_CODE,'msg'=>$login_result['msg'],'status'=>FAIL_STATUS],'json',true);
		}
		$zqrb_Bindparam['phone'] = $param['phone'];		//第三方openID
		if(!empty($param['wx_openid'])) {                                              // 微信手机号绑定注册
			$local_param['wx_openid'] = $zqrb_Bindparam['var'] = $param['wx_openid'];
			$zqrb_Bindparam['type']   = 'wx';
		}else if(!empty($param['qq_openid'])) {                                        // qq手机号绑定注册
			$local_param['qq_openid'] = $zqrb_Bindparam['var'] = $param['qq_openid'];
			$zqrb_Bindparam['type']   = 'qq';
		}else if(!empty($param['sina_openid'])) {                                     // 新浪手机号绑定注册
			$local_param['sina_openid'] = $zqrb_Bindparam['var'] = $param['sina_openid'];
			$zqrb_Bindparam['type']   = 'sina';
		}
		//用户昵称
		$nickname = '';
		if(!empty($param['nickname'])) {
			$nickname = $zqrb_Bindparam['nickname']   = $param['nickname'];
		}
		// 用户头像
		$avatar = '';
		if(!empty($param['avatar'])) {
			$avatar = $zqrb_Bindparam['avatar']   = $param['avatar'];
		}
		// 证券日报端绑定
		$bind_result = $this->zqrb->userBind($zqrb_Bindparam);
		if($bind_result['code'] == -117 || $bind_result['code'] == '-117') {
			apiSend(['code'=>FAIL_CODE,'msg'=>QQ_BINDED,'status'=>FAIL_STATUS],'json',true);
		}else if($bind_result['code'] == -117 || $bind_result['code'] == '-117') {
			apiSend(['code'=>FAIL_CODE,'msg'=>QQ_BINDED,'status'=>FAIL_STATUS],'json',true);
		}
		if($bind_result['code'] != 200) {
			apiSend(['code'=>FAIL_CODE,'msg'=>$bind_result['msg'],'status'=>FAIL_STATUS],'json',true);
		}
		//用户昵称
		if(!empty($param['user_info']['nikeName'])) {
			$nickname = $param['user_info']['nikeName'];
		}else if(empty($nickname)){
			$nickname = $param['user_info']['userName'];
		}
		// 用户头像
		if(!empty($param['user_info']['imgorigin'])) {
			$avatar = $param['user_info']['imgorigin'];
		}
		$local_param['avatar'] = $avatar;
		$local_param['nickname'] = $nickname;
		$local_param['update_time'] = time();
		$token  = get_rand_char(5). rand(10000,99999);
		$local_param['token'] = $token;              //用户token
		$local_param['token_expire_time'] = time() + 604800;                      //token失效时间
		$where = ['user_id' => $login_result['data']['uid']];
		$query['where'] = $where;
		$query['data'] = $local_param;
		$result['status'] = $this->model->index('userUpdate',$query);
		$query['field'] = 'id as user_id,sex,birthday';
		// 查询APP服务器的用户信息
		$local_info = $this->model->index('userInfo',$query);
		$result['data'] = [];
		if($result['status']) {
			$result['data']['user_id']  = $local_info['user_id'];                // 新用户id
			$result['data']['phone']    = $param['phone'];						 // 手机号
			$result['data']['token']    = $token;                                // token
			$result['data']['avatar']   = $avatar;                               // avater
			$result['data']['sex']      = $local_info['sex'];					 // sex
			$result['data']['birthday'] = $local_info['birthday'];               // birthday
			$result['data']['nickname'] = $nickname;       // 昵称
			Cache::set('user_token_' . $local_info['user_id'],$token,604800);
			apiSend(['code'=>SUCCESS_CODE,'msg'=>BIND_SUCCESS,'status'=>SUCCESS_STATUS,'data'=>$result['data']],'json',true);
		}
		apiSend(['code'=>FAIL_CODE,'msg'=>BIND_FAIL,'status'=>FAIL_STATUS],'json',true);
	}
	/**
	 * [userInfoUpdate 更新用户信息]
	 * @AuthorHTL villager
	 * @DateTime  2018-06-19T15:16:02+0800
	 * @param     [type]                   $param [description]
	 * @return    [type]                          [description]
	 */
	public function userInfoUpdate($param) {
		$data = [];
		$result['status'] = false;
		$header = $this->header;
		$where = ['id' => $header['uid']];
		$user_query['where'] = $where;
		$user_query['field'] = 'id,user_id,nickname,avatar';
		$user_info = $this->model->index('userInfo',$user_query);
		// 用户昵称
		if(!empty($param['nickname']) && ($user_info['nickname'] != $param['nickname'])) {
			$black_query['where'] = ['content' => ['like','%'.$param['nickname'].'%','is_delete' => 0]];
			$black_query['field'] = 'id,content';
			// 查看敏感词黑名单
			$this->blackModel = new WordBlackList();
			$black_list = $this->blackModel->index('wordBlackList',$black_query);
			if(!empty($black_list)) {
				apiSend(['code'=>BLACK_WORD,'msg'=>BLACK_WORD_MSG,'status'=>FAIL_STATUS],'json',true);
			}
			$result = check_specialChars($param['nickname']);
			if(!$result['status']) {
				apiSend(['code'=>SPECIAL_CHARS_CODE,'msg'=>SPECIAL_CHARS_MSG,'status'=>FAIL_STATUS],'json',true);
			}
			if(mb_strlen($param['nickname']) > 15 || mb_strlen($param['nickname']) < 3) {
				apiSend(['code'=>NICKNAME_LONG_CODE,'msg'=>NICKNAME_LONG_MSG,'status'=>FAIL_STATUS],'json',true);
			}
			$zqrb_param['nikename'] = $data['nickname'] = $param['nickname'];
		}
		// 用户头像
		if(!empty($param['avatar']) && $user_info['avatar'] != $param['avatar']) {
			$zqrb_param['avatar'] = $data['avatar'] = $param['avatar'];
		}
		$zqrb_param['uid'] = $user_info['user_id'];
		if(!empty($data)) {
			$update_result = $this->zqrb->userUpdate($zqrb_param);
			$data['update_time'] = time();
			$query['where'] = $where;
			$query['data']  = $data;
			$result['status'] = $this->model->index('userUpdate',$query);
		}
		return $result;
	}
	/**
	 * [userInfo 用户信息]
	 * @AuthorHTL villager
	 * @DateTime  2018-06-20T16:26:34+0800
	 * @param     [type]                   $param [description]
	 * @return    [type]                          [description]
	 */
	public function userInfo($param)
	{
		$data = [];
		$header = $this->header;
		if(isset($header['uid']) && !empty($header['uid'])) {
			if(!isset($header['authorization']) || empty($header['authorization'])) apiSend(['code'=>FAIL_CODE,'msg'=>LACK_TOKEN,'status'=>FAIL_STATUS],'json',true); //没有token，非法访问
	        if(!Cache::has('user_token_' . $header['uid'])) apiSend(['code'=>TOKEN_EXPIRED_CODE,'msg'=>TOKEN_EXPIRED,'status'=>FAIL_STATUS],'json',true);//token失效，重新登录
	        $token = Cache::get('user_token_' . $header['uid']);
	        if($token != $header['authorization']){
	            apiSend(['code'=>TOKEN_EXPIRED_CODE,'msg'=>TOKEN_ERROR,'status'=>FAIL_STATUS],'json',true);//token错误，重新登录
	        }
			$user_id = $header['uid'];
		}else {
			$user_id = 0;
		}
		$query['where'] = ['id' => $user_id];
		$query['field'] = 'id as user_id,nickname,avatar';
		//查询用户信息
		$user_info = $this->model->index('userInfo',$query);
		if(empty($user_info)) {
			$user_info = ['user_id' => 0,'nickname' => null,'avatar' => null];
		}
		$num = 0;
		//用户的最新未读消息
		$reply_user_info = null;
		if(!empty($user_info['lastMessage'])) {
			$reply_user_id = $user_info['lastMessage'][0]['user_id'];
			$reply_user_query['where'] = ['id' => $reply_user_id];
			$reply_user_query['field'] = 'id as user_id,nickname,avatar';
			//发出最新消息的用户信息
			$reply_user_info = $this->model->index('userInfo',$reply_user_query);
			unset($reply_user_info['lastMessage']);
			$comment_reply_query['where'] = ['commented_user_id' => $header['uid'],'is_delete' => 0,'is_read' => 0];
			$commentReplyModel = new CommentReplyModel();
			//未读消息的条数
			$num = $commentReplyModel -> index('count',$comment_reply_query);
		}
		$message = ['num' => $num,'replyUserInfo' => $reply_user_info];
		$user_info['message'] = $message;
		unset($user_info['lastMessage']);
		return $user_info;
	}
	
}