<?php
namespace app\api\controller;
use app\common\model\UserLog;
use app\api\controller\Base;
use think\Request;
use think\Route;
use think\Cache;
/**
 * @ClassName:    Api 
 * @Description:  Api接口控制 
 * @Created by:   [villager] 
 * @DateTime:     2018-05-15T14:52:26+0800
 */
Class Api extends Base 
{
    protected $request;
    protected $version = '1.0';
	/**
	 * [_initialize:  接口请求初始化] 
	 * @Created by:   [villager] 
	 * @DateTime:     2018-05-15T14:52:26+0800
	 */
	public function _initialize()
	{
		parent::_initialize();//父类初始化
        $this->request = Request::instance();
        $this->header = $this->request->header();
        $controller_action = Request::instance()->controller(). '_' .Request::instance()->action();
		if(in_array(strtoupper($controller_action), config('user_action')))
		{
			$this -> verify_token();
		}
        //获取版本号
        $this->get_version();
        //记录用户接口操作
        $this->record_log();
	}

	/**
	 * 获取版本号
	 * @author   [villager]
	 * @DateTime  2018-05-14T18:00:13+0800
	*/
	private function get_version()
    {
        $header = $this->header;
        if(isset($header['version']) && !empty($header['version'])) {
            $this->version = $header['version'];
        }
    }
    /**
     * [record_log 记录用户操作日志]
     * @AuthorHTL
     * @DateTime  2018-07-02T13:30:14+0800
     * @return    [type]                   [description]
     */
    private function record_log()
    {
        $header  = $this->header;
        $user_id = isset($header['uid']) ? $header['uid'] : 0; //用户id
        $request_ip = $this->request -> ip();//用户IP
        $interface  = $this->request -> url(true);//用户接口地址
        $request_method = $this->request -> method();//请求方法
        $service_info = isset($header['x-sg-agent']) ? $header['x-sg-agent'] : null;
        $interface_version = $this->version;
        $data = compact('user_id','request_method','request_ip','interface','service_info','interface_version');
        $query['data'] = $data;
        $userLogModel = new UserLog();
        $userLogModel -> index('userLogAdd',$query);
        //以最后一次操作作为节点，七天后token失效
        if(Cache::has('user_token_' . $user_id)) {
            $token = Cache::get('user_token_' . $user_id);
            Cache::set('user_token_' . $user_id,$token,604800);
        }
    }
    /**
     * [verify_token 验证token]
     * @author villager
     * @DateTime  2018-05-21T14:57:05+0800
     * @return    [type]                   [description]
     */
    private function verify_token()
    {
        $header  = $this->header;
        if(!isset($header['uid']) || empty($header['uid'])) apiSend(['code'=>NOT_LOGIN,'msg'=>NOT_LOGIN_MSG,'status'=>FAIL_STATUS],'json',true); //没有uid，非法访问
        if(!isset($header['authorization']) || empty($header['authorization'])) apiSend(['code'=>FAIL_CODE,'msg'=>LACK_TOKEN,'status'=>FAIL_STATUS],'json',true); //没有token，非法访问
        if(!Cache::has('user_token_' . $header['uid'])) apiSend(['code'=>TOKEN_EXPIRED_CODE,'msg'=>TOKEN_EXPIRED,'status'=>FAIL_STATUS],'json',true);//token失效，重新登录
        $token = Cache::get('user_token_' . $header['uid']);
        if($token != $header['authorization']){
            apiSend(['code'=>TOKEN_EXPIRED_CODE,'msg'=>TOKEN_ERROR,'status'=>FAIL_STATUS],'json',true);//token错误，重新登录
        }
        return true;
        
    }
}