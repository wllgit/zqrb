<?php
/**
 * Created by PhpStorm.
 * User: sogu
 * Date: 2018/6/21
 * Time: 下午3:13
 */
namespace app\api\controller;

use think\Controller;
use think\Db;
use app\api\controller\Rsa;

class NewFlash extends Controller{

    protected $data = null;

    public function _initialize()
    {
        //跨域请求
        header("Access-Control-Allow-Origin: *");
        header("Access-Control-Allow-Headers: Accept,Authorization,Cache-Control,Content-Type,DNT,If-Modified-Since,Keep-Alive,Origin,User-Agent,X-Mx-ReqToken,X-Requested-With");
        header("Access-Control-Allow-Methods: GET, POST, PUT,DELETE,OPTIONS");
        header('Content-type: application/json');


        if($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
            header("Access-Control-Allow-Origin: *");
            header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept, Authorization");
            header('Access-Control-Allow-Methods: GET, POST, PUT,DELETE,OPTIONS,PATCH');
            exit;
        }

        $pubfile = APP_PATH.'/../public/rsa/rsa_public_key.pem';
        $prifile = APP_PATH.'/../public/rsa/rsa_private_key.pem';

        $res = get_input();

        $rsa = new Rsa($pubfile,$prifile);
        $endata = $rsa->decrypt($res['data']);

        $request_info = json_decode($endata,true);

        $this->data = $request_info;

        if(!isset($request_info['sign']) || !$request_info['sign']){
            apiSend(['code'=>FAIL_CODE,'msg'=>LACK_SIGN,'status'=>ERROR_STATUS],'json',true);
        }
        if(!isset($request_info['timestamp']) || !$request_info['timestamp']){
            apiSend(['code'=>FAIL_CODE,'msg'=>LACK_TIMESTAMP,'status'=>ERROR_STATUS],'json',true);
        }

        if (time() - $request_info['timestamp'] > 300)  //同一签名调用时间限制
        {
            apiSend(['code'=>FAIL_CODE,'msg'=>TIME_OUT,'status'=>ERROR_STATUS],'json',true);
        }

        $sign = "type=".$request_info['type'].'&title='.$request_info['title'].'&summary='.$request_info['summary'].'&timestamp='.$request_info['timestamp'].'&key=brscs2018Signkeyqb';
        //type=104&title=测试&summary=sdhf&timestamp=1531814909&key=brscs2018Signkeyqb
        $sign = md5($sign);  //加密

        if($sign !== $request_info['sign'])
        {
            apiSend(['code'=>FAIL_CODE,'msg'=>SIGN_ERROR,'status'=>ERROR_STATUS],'json',true);
        }
    }
    /**
     * 存储快讯接口
     * type 类型   11 微信、4 微博 、2 股吧、0 消息、101 新闻、102 研报、103 公告、 104 电报
     * title 标题
     * summary 内容
     */
    public function saveNewFlash(){
        //跨域请求
//        header("Access-Control-Allow-Origin: *");
//        header("Access-Control-Allow-Headers: Accept,Authorization,Cache-Control,Content-Type,DNT,If-Modified-Since,Keep-Alive,Origin,User-Agent,X-Mx-ReqToken,X-Requested-With");
//        header("Access-Control-Allow-Methods: GET, POST, PUT,DELETE,OPTIONS");
//        header('Content-type: application/json');

        //$res = get_input();print_r($res);

        $request_info = $this->data;

        if(!isset($request_info['type'])){
            return apiSend(['code'=>FAIL_CODE,'msg'=>LACK_PARAM,'status'=>FAIL_STATUS],'json');
        }
        if(!isset($request_info['title'])){
            return apiSend(['code'=>FAIL_CODE,'msg'=>LACK_PARAM,'status'=>FAIL_STATUS],'json');
        }
        if(!isset($request_info['summary'])){
            return apiSend(['code'=>FAIL_CODE,'msg'=>LACK_PARAM,'status'=>FAIL_STATUS],'json');
        }

        $data = [
            'type' => trim($request_info['type']),
            'title' => trim($request_info['title']),
            'summary'   => trim($request_info['summary']),
            'create_time'   => time(),
            'update_time'   => time()
        ];

        $res = Db::table('scs_news_flash')->insert($data);

        if($res){
            return apiSend(['code'=>SUCCESS_CODE,'msg'=>ADD_SUCCESS,'status'=>SUCCESS_STATUS],'json');
        }else{
            return apiSend(['code'=>FAIL_CODE,'msg'=>ADD_FAIL,'status'=>FAIL_STATUS],'json');
        }

    }

    //测试rsa
    public function rsa(){
        $pubfile = APP_PATH.'/../public/rsa/rsa_public_key.pem';
        $prifile = APP_PATH.'/../public/rsa/rsa_private_key.pem';


        $rsa = new Rsa($pubfile,$prifile);

        $rst = array(
            'ret' => 200,
            'code' => 1,
            'data' => array(1, 2, 3, 4, 5, 6),
            'msg' => "success",
        );
        $ex = json_encode($rst);
        //加密
        $ret_e = $rsa->encrypt($ex);
        //解密
        $ret_d = $rsa->decrypt($ret_e);
//        echo $ret_e;
//        echo '<pre>';
//        echo $ret_d;

        $res = $rsa->sign($data = 'scs_zqrb2018');//print_r($res);
        $res1 = $rsa->verify($data = 'scs_zqrb2018',$res);print_r($res1);

    }
}