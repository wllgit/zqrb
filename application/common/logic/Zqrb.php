<?php
namespace app\common\logic;
use think\Cache;
use think\Request;
use app\common\logic\Base;
/**
 * @ClassName:    zqrb 
 * @Description:  新闻类 
 * @Created by:   [villager] 
 * @DateTime:     2018-05-28T14:13:06+0800
 */
Class Zqrb extends Base 
{
	
	protected $curl_header = ['Content-Type:application/x-www-form-urlencoded;charset=utf8'];
	protected $curl_url;
	/**
	 * [initialize:  初始化绑定zqrb模型] 
	 * @Created by:   [villager] 
	 * @DateTime:     2018-05-16T15:16:16+0800
	 */
	protected function initialize() {
        parent::initialize();
        $this->curl_url = config('zqrb')['interface_url'];
        $this->key = config('zqrb')['key'];
    }
	/**
	 * [selectUser 证券日报查询用户接口]
	 * @AuthorHTL
	 * @DateTime  2018-07-20T09:45:04+0800
	 * @return    [type]                   [description]
	 */
	public function userSelect($param) {
		// 请求头
		$curl_header = $this->curl_header;
		// 查询用户信息接口地址
		$curl_url = $this->curl_url.'?act=select'; 
		// 查询用户信息的方式
		$type = $param['type'];
		switch ($type) {
			// 手机号查询
			case 'phone':  
				$post['phone'] = $param['var'];
				break;
			// 微信查询
			case 'wx_openid':
				$post['wx_openid'] = $param['var'];
				break;
			// QQ查询
			case 'qq_openid':
				$post['qq_openid'] = $param['var'];
				break;
			// 新浪账号查询
			case 'sina_openid':
				$post['sina_openid'] = $param['var'];
				break;
			// 用户名查询
			case 'username':
				$post['username'] = $param['var'];
				break;
			// 用户id查询
			case 'uid':
				$post['uid'] = $param['var'];
				break;
		}
		$post['mask']  = md5($this->key.$param['var']);
		$info = curl($curl_url,$curl_header,$post);
		$info = @iconv("UTF-8", "GBK//IGNORE", $info);
		$info = @iconv("GBK", "UTF-8//IGNORE", $info);
		return json_decode($info,true);
	}
	/**
	 * [userRegister 证券日报注册用户接口]
	 * @AuthorHTL
	 * @DateTime  2018-07-20T09:47:53+0800
	 * @param     [type]                   $param [description]
	 * @return    [type]                          [description]
	 */
	public function userRegister($param) {
		// 请求头
		$curl_header = $this->curl_header;
		// 用户注册接口地址
		$curl_url = $this->curl_url.'?act=register'; 
		// 用户注册方式
		$type = $param['type'];
		$post['phone'] = $param['phone'];
		$post['password'] = md5($param['code']);
		if(!empty($param['openid'])) {
			$post['imgorigin'] = !empty($param['avatar']) ? base64EncodeImage($param['avatar']) : null;
			$post['nikename']  = !empty($param['nikename']) ? $param['nikename'] : null;//用户昵称
		}
		
		switch ($type) {
			// 微信注册
			case 'wx':
				$post['wx_openid'] = urlencode($param['openid']);
				break;
			// QQ注册
			case 'qq':
				$post['qq_openid'] = urlencode($param['openid']);
				break;
			// 新浪账号注册
			case 'sina':
				$post['sina_openid'] = urlencode($param['openid']);
				break;
		}
		$post['mask'] = md5($param['phone'].$this->key.$post['password']);
		$info = curl($curl_url,$curl_header,$post);
		return json_decode($info,true);
	}
	/**
	 * [userLogin 证券日报用户登录接口]
	 * @AuthorHTL
	 * @DateTime  2018-07-20T09:48:45+0800
	 * @param     [type]                   $param [description]
	 * @return    [type]                          [description]
	 */
	public function userLogin($param) {
		// 请求头
		$curl_header = $this->curl_header;
		// 用户登录接口地址
		$curl_url = $this->curl_url.'?act=login'; 
		// 用户注册方式
		$type = $param['type'];
		switch ($type) {
			// 手机号登录
			case 'phone':  
				$post['phone'] = $param['var'];
				$post['identifying_code'] = $param['code'];
				break;
			// 微信登录
			case 'wx_openid':
				$post['wx_openid'] = $param['var'];
				break;
			// QQ登录
			case 'qq_openid':
				$post['qq_openid'] = $param['var'];
				break;
			// 新浪账号登录
			case 'sina_openid':
				$post['sina_openid'] = $param['var'];
				break;
			// 用户名登录
			case 'username':
				$post['username'] = urlencode($param['var']);
				break;
		}
		$post['mask']  = md5($this->key.$param['var']);
		$info = curl($curl_url,$curl_header,$post);
		return json_decode($info,true);
	}
	/**
	 * [userBind 证券日报用户绑定接口]
	 * @AuthorHTL
	 * @DateTime  2018-07-20T09:49:15+0800
	 * @param     [type]                   $param [description]
	 * @return    [type]                          [description]
	 */
	public function userBind($param) {
		// 请求头
		$curl_header = $this->curl_header;
		// 用户登录接口地址
		$curl_url = $this->curl_url.'?act=bind';
		// 绑定手机号
		$post['phone'] = $param['phone'];
		$type = $param['type'];
		switch ($type) {
			case 'wx':
				$post['wx_openid'] = $param['var'];
				break;
			case 'qq':
				$post['qq_openid'] = $param['var'];
				break;
			case 'sina':
				$post['sina_openid'] = $param['var'];
				break;
		}
		//用户昵称
		if(!empty($param['nickname'])) {
			$post['nickname']   = $param['nickname'];
		}
		// 用户头像
		if(!empty($param['avatar'])) {
			$post['avatar']   = $param['avatar'];
		}
		$post['mask'] = md5($this->key.$param['phone']);
		$info = curl($curl_url,$curl_header,$post);
		return json_decode($info,true);
	}
	/**
	 * [userUpdate 证券日报用户更新接口]
	 * @AuthorHTL
	 * @DateTime  2018-07-20T09:51:10+0800
	 * @param     [type]                   $param [description]
	 * @return    [type]                          [description]
	 */
	public function userUpdate($param) {
		// 请求头
		$curl_header = $this->curl_header;
		// 用户登录接口地址
		$curl_url = $this->curl_url.'?act=edit';
		// 用户UID
		$post['uid'] = $param['uid'];
		// 用户昵称
		if(!empty($param['nickName'])) {
			$post['nickName'] = urlencode($param['nickName']);
		}
		if(!empty($param['nikename'])) {
			// $post['nickname'] = urlencode($param['nickname']);
			$post['nikename'] = urlencode($param['nikename']);
		}
		// 用户名
		if(!empty($param['username'])) {
			$post['userName'] = urlencode($param['username']);
		}
		// 用户头像
		if(!empty($param['avatar'])) {
			$post['imgorigin'] = $post['imgsmall']  = $post['userimg'] = base64EncodeImage($param['avatar']);
		}
		$post['mask']  = md5($this->key.$param['uid']);
		$info = curl($curl_url,$curl_header,$post);
		return json_decode($info,true);
	}
	/**
	 * [sendCode 证券日报发送短信接口]
	 * @AuthorHTL
	 * @DateTime  2018-07-20T09:50:02+0800
	 * @param     [type]                   $param [description]
	 * @return    [type]                          [description]
	 */
	public function sendCode($param) {
		// 请求头
		$curl_header = $this->curl_header;
		// 验证验证码接口地址
		$curl_url = $this->curl_url.'?act=send_sms';
		$post['phone'] = $param['phone'];
		// 发送短信的类型 注册=0 登陆=1 找回密码=2 修改手机号码=3 强制验证手机=4
		$post['type']  = $param['type'];
		$post['mask']  = md5($param['phone'].$this->key);
		$info = curl($curl_url,$curl_header,$post);
		return json_decode($info,true);
	}
	/**
	 * [checkCode 证券日报验证验证码接口]
	 * @AuthorHTL
	 * @DateTime  2018-07-20T11:31:28+0800
	 * @param     [type]                   $param [description]
	 * @return    [type]                          [description]
	 */
	public function checkCode($param) {
		// 请求头
		$curl_header = $this->curl_header;
		// 验证验证码接口地址
		$curl_url = $this->curl_url.'?act=check_sms';
		$post['phone'] = $param['phone'];
		$post['identifying_code'] = $param['code'];
		$post['type'] = 0;
		$post['mask'] = md5($param['phone'].$this->key.$param['code']);
		$info = curl($curl_url,$curl_header,$post);
		return json_decode($info,true);
	}
	
}