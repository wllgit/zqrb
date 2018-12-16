<?php
class sendMsg{

    public $appkey;

    public $secretKey;

    public $code;
    public $msg;
    public $phone;
    public $sms_id;
    public $sign_name;

    public function __construct($appkey='',$secretKey='',$code='',$msg='',$phone='',$sms_id='',$sign_name=''){
        $this->appkey = $appkey;
        $this->secretKey = $secretKey ;
        $this->code = $code;
        $this->msg = $msg ;
        $this->phone = $phone;
        $this->sms_id = $sms_id ;
        $this->sign_name = $sign_name;
    }


    public function send_sms(){
        $code=$this->code;
        $msg=$this->msg;
        $phone=$this->phone;
        $sign_name = $this->sign_name;
        $appkey = $this->appkey;
        $secretKey = $this->secretKey;
        $sms_id = $this->sms_id;

        include "TopSdk.php";
        $c = new TopClient();
        $c->appkey = $appkey;
        $c->secretKey = $secretKey;
        $req = new AlibabaAliqinFcSmsNumSendRequest;
        $req->setSmsType("normal");
        $req->setSmsFreeSignName($sign_name);
        $arr['code'] = $code;
        $arr['product'] = $msg;
        //短信模板： 验证码${code}，您正在登录${product}，若非本人操作，请勿泄露。
        $req->setSmsParam(json_encode($arr));
        $req->setRecNum($phone);
        $req->setSmsTemplateCode($sms_id);//短信模板id
        $resp = $c->execute($req);
        return $resp;
    }
}


?>