<?php
namespace app\api\controller;
use think\Controller;
use think\Request;
use think\Cache;
/**
 * @ClassName:    Base 
 * @Created by:   [villager] 
 * @DateTime:     2018-05-14T18:00:13+0800
 * @Description:  验证接口请求合法性的基础类
 */
Class Base extends Controller 
{
    private $version; //程序版本号
    private $appVersion; //app版本号
    /**
     * [_initialize:  获取客户端版本号、验证签名] 
     * @Created by:   [villager] 
     * @DateTime:     2018-05-14T18:00:13+0800
     */
    public function _initialize()
    {
        require_once APP_PATH.'common/apiLang/apiLang.php';//引入api语言包
        $controller_action = Request::instance()->controller(). '_' .Request::instance()->action();
        //验证签名方式
        if(in_array(strtoupper($controller_action), config('not_sign'))){ //验证mask签名
            $ip = Request::instance()->ip();
            if(!in_array($ip, config('ip_white'))) {
                apiSend(['code'=>FORBIDDEN,'msg'=>IP_FORBIDDEN,'status'=>FAIL_STATUS],'json',true);
            }
            $this->verify_mask();
        }else { //验证sign签名
            $this->verify_client();
        }
        // 链接redis
        
    }

    /**
     * [verify_client 验证sign签名]
     * @author    [villager]
     * @return    [json]   [返回信息]
     * @DateTime  2018-05-14T18:32:35+0800
     */
    private function verify_client()
    {
        $param = get_input();
        $header = Request::instance() -> header();
        if(!isset($header['timestamp']))  apiSend(['code'=>FAIL_CODE,'msg'=>LACK_TIMESTAMP,'status'=>ERROR_STATUS],'json',true);
        $server_time = time();//服务器当前时间
        $response_time = $server_time - $header['timestamp'];//中间的响应时间
        if($response_time > 1800)  apiSend(['code'=>FAIL_CODE,'msg'=>TIME_OUT,'status'=>ERROR_STATUS],'json',true);
        if(!isset($header['x-sg-agent'])) apiSend(['code'=>FAIL_CODE,'msg'=>LACK_SERVICE,'status'=>ERROR_STATUS],'json',true);
        if(isset($param['sign'])){
            $sign = $param['sign'];
            $param_str = $this->get_sign($header);
            if($param_str !== $sign){
                apiSend(['code'=>FAIL_CODE,'msg'=>SIGN_ERROR,'status'=>ERROR_STATUS],'json',true);
            }
        }else{
            apiSend(['code'=>FAIL_CODE,'msg'=>LACK_SIGN,'status'=>ERROR_STATUS],'json',true);
        }
        
    }
    /**
     * [verify_mask 验证mask签名]
     * @AuthorHTL
     * @DateTime  2018-07-03T13:55:29+0800
     * @return    [type]                   [description]
     */
    private function verify_mask()
    {
        $param = get_input();
        $origin_id = $param['origin_id'];
        if(!isset($param['act']) || empty($param['act'])) apiSend(['code'=>FAIL_CODE,'msg'=>LACK_ACT,'status'=>FAIL_STATUS],'json',true);//缺少操作类型
        if(!isset($param['mask']) || empty($param['mask'])) apiSend(['code'=>LACK_MASK_CODE,'msg'=>LACK_MASK,'status'=>FAIL_STATUS],'json',true);//缺少mask校验码
        $act = strtoupper($param['act']);
        if($act == 'ADD') {//添加操作校验
            $tmp_mask = md5(config('zqrb')['key'].'ADD' . $origin_id);
        }else if($act == 'DELETE') { //删除操作校验
            $tmp_mask = md5(config('zqrb')['key'].'DELETE' . $origin_id);
        }else { //没有该操作
            apiSend(['code'=>FAIL_CODE,'msg'=>NOSUPPORT_OPERATE.$param['act'],'status'=>FAIL_STATUS],'json',true);
        }
        if($param['mask'] != $tmp_mask) {//校验失败
            apiSend(['code'=>MASK_ERROR_CODE,'msg'=>MASK_ERROR_MSG,'status'=>FAIL_STATUS],'json',true);
        }
    }

    /**
     * 获取sign
     * @author villager
     * @param  $paramAry  接口数据
     * @param  $key       加密key
     * @return string    加密后的签名
     * @DateTime  2018-05-14T18:32:35+0800
     */
    private function get_sign($paramAry)
    {
        $key = "scs2018Signkey";
        $timestamp = $paramAry['timestamp'];
        $x_sg_agent = $paramAry['x-sg-agent'];
        $agent_arr = explode('/', $x_sg_agent);
        if(!isset($agent_arr[1])) apiSend(['code'=>FAIL_CODE,'msg'=>LACK_SERVICE,'status'=>ERROR_STATUS],'json',true);
        $device_token = $agent_arr[1];
        $temSign = $timestamp . '@' .$device_token . '@' . $key;
        return md5($temSign);
    }
}