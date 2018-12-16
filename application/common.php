<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 流年 <liu21st@gmail.com>
// +----------------------------------------------------------------------

// 应用公共文件
/**
 * 返回ajax请求
 * @param data 返回的数据
 * @param type 数据类型
 */
use think\Cache;
require_once APP_PATH.'common/apiLang/apiLang.php';//引入api语言包
function ajaxReturn($data,$type='JSON') {
    switch (strtoupper($type)){
        case 'JSON' :
            // 返回JSON数据格式到客户端 包含状态信息
            header('Content-Type:application/json; charset=utf-8');
            exit(json_encode($data));
        case 'XML'  :
            // 返回xml格式数据
            header('Content-Type:text/xml; charset=utf-8');
            $xml = "<root>";
            $xml.=xml_encode($data);
            $xml.="</root>";
            exit($xml);
        case 'JSONP':
            // 返回JSON数据格式到客户端 包含状态信息
            header('Content-Type:application/json; charset=utf-8');
            $handler  =   isset($_GET[C('VAR_JSONP_HANDLER')]) ? $_GET[C('VAR_JSONP_HANDLER')] : C('DEFAULT_JSONP_HANDLER');
            exit($handler.'('.json_encode($data).');');  
        case 'EVAL' :
            // 返回可执行的js脚本
            header('Content-Type:text/html; charset=utf-8');
            exit($data);            
        default     :
            // 用于扩展其他返回格式数据
            Hook::listen('ajax_return',$data);
    }
}
/**
 * 返回ajax请求及状态
 * @param data 返回的数据
 */
function apiSend($response,$type = 'JSON',$is_quit = false,$is_unicode = false) {
    if(isset($response['data']) && empty($response['data']) && $response['code'] != -100) {
        $msg = NULL_MESSAGE;
        if($is_unicode) {
            $msg = unicode_encode(NULL_MESSAGE);
        }
        $response = ['status' => SUCCESS_STATUS,'msg' => $msg,'code' =>NULL_CODE,'data' => []];
    }
    // 是否进行Unicode编码
    if($is_unicode) {
        $response['msg'] = unicode_encode($response['msg']);
    }
    //$error_code = [-100,-101];
    $token_expired = [401];
    //请求错误
    if($response['code'] < 0){
        header("HTTP/1.0 400 ERROR");
    }
    //token失效
    if(in_array($response['code'], $token_expired)) {
        header("HTTP/1.0 401 ERROR");
    }
    if($is_quit) ajaxReturn($response,$type);
    switch (strtoupper($type)) {
        case 'JSON' :
            // 返回JSON数据格式到客户端 包含状态信息
            return json($response);
            break;
        case 'XML'  :
            // 返回xml格式数据
            return xml($response,200,[],['root_node'=>'root']);
            break;
        case 'JSONP':
            // 返回JSON数据格式到客户端 包含状态信息
            return jsonp($response);
            break;
    }
}
/**
 * [send_post 发送post请求]
 * @villager
 * @param     [type]                   $url       [请求地址]
 * @param     [type]                   $post_data [请求数据]
 * @return    [type]                              [请求结果]
 * @DateTime  2018-05-15T10:04:56+0800
 */
function send_post($url, $post_data) {

    $postdata = http_build_query($post_data);
    $options = array(
        'http' => array(
            'method' => 'POST',
            'header' => 'Content-type:application/json,charset=UTF-8',
            'content' => $postdata,
            'timeout' => 15 * 60 // 超时时间（单位:s）
        )
    );
    $context = stream_context_create($options);
    $result = file_get_contents($url, false, $context);

    return $result;
}
/**
 * @param $url
 * @param array $header
 * @param null $postFields
 * @return mixed
 */
function curl($url, $header = [], $postFields = null)
{
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_FAILONERROR, false);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 120);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 50);
    curl_setopt($ch, CURLOPT_HEADER, false);
    curl_setopt($ch, CURLOPT_NOBODY, false);
    //https请求
    if(strlen($url) > 5 && strtolower(substr($url, 0, 5)) == 'https') {
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);//阻止对证书的合法性的检查
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);//不校验当前的域名是否与CN匹配
    }
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);//这个是重点。

    if(0 < count($header)) {
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
    }

    if(is_array($postFields) && 0 < count($postFields)) {
        curl_setopt($ch, CURLOPT_POST, true);//使用post提交数据
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($postFields));//设置 post提交的数据
    }
    if(!empty($postFields) && !is_array($postFields)) {
        curl_setopt($ch, CURLOPT_POST, true);//json格式数据
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postFields);//设置 post提交的数据
    }

    if ($ret = curl_exec($ch)) {
        return $ret;
    } else {
        return curl_error($ch);
    }
}
/**
 * 获取客户端传递端数据
 * @return mixed
 * @author magic
 */
function get_input(){
    $params = input();
    if(!$params){
        $input = file_get_contents('php://input');
        $params = json_decode($input, true);
    }
    return $params;
}

/**
 * swoole客户端发送send
 * @param $data[]
 * 计算热点传$news_id 新闻id，type=1
 * 分词传news_id 新闻id，title 标题,type=2
 */
function swoole_client($data){
    $client = new \swoole_client(SWOOLE_SOCK_TCP);
    //连接到服务器
    if (!$client->connect('0.0.0.0', 9501, 0.5))
    {
        exit("connect failed. Error: {$client->errCode}\n");
    }
    //计算热点
    if($data['type'] == 1){
        $send = array(
            'news_id' => intval($data['news_id']),
            'type'    => $data['type']
        );
    }
    //分词
    if($data['type'] == 2){
        $send = array(
            'news_id' => intval($data['news_id']),
            'title'   => trim($data['title']),
            'type'    => $data['type']
        );
    }

    $msg = json_encode($send);
    //向服务器发送数据
    if (!$client->send($msg))
    {
        exit("send failed. Error: {$client->errCode}\n");
    }
    //关闭连接
    $client->close();
}
/**
 * @param $code 手机验证码
 * @param $phone 手机号码
 * @param string $msg 短信部分内容
 * @return array  error为false表示发送成功
 * @author LingTM<lingtima@gmail.com>
 */
function send_sms($code,$phone,$msg='PE系统'){
    $code=(string)$code;
    $phone=(string)$phone;
    import('lib.taobao_sdk.sendMsg');
    $appkey = '24550680';
    $secretKey = '1988a9f0d2186e89f87dcd5077d44bad';
    //验证码${code}，您正在登录${product}，若非本人操作，请勿泄露。
    $sms_id = "SMS_10661854";//短信模版id
    $sign_name='登录验证';//配置短信签名
    $c = new sendMsg($appkey,$secretKey,$code,$msg,$phone,$sms_id,$sign_name);
    $result = $c->send_sms();
    $result = object_array($result);
    if(isset($result['result']['success']) && $result['result']['success'] == 'true'){
        //return ['error'=>false,'msg'=>''];
        apiSend(['code'=>SUCCESS_CODE,'msg'=>SEND_CODE_SUCCESS,'status'=>SUCCESS_STATUS],'json',true);
    }
    apiSend(['code'=>FAIL_CODE,'msg'=>$result['msg'],'status'=>FAIL_STATU,'data'=>$data],'json',true);
}
/**
*验证验证码
*@param phone 电话
*@param code  验证码
*@param type  类型 register 注册验证码 login 登录验证码
*/
function check_code($phone,$code){
    $cache_code = Cache::get('code_' . $phone);
    if($cache_code != $code) {
         return false;
    }
    return true;
}
/**
 * 生成随机字符串
 * @param $length
 * @return null|string
 */
function get_rand_char($length){
    $str = null;
    $strPol = "ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789abcdefghijklmnopqrstuvwxyz";
    $max = strlen($strPol)-1;

    for ($i=0; $i<$length; $i++) {
        $str.=$strPol[rand(0,$max)];//rand($min,$max)生成介于min和max两个数之间的一个随机整数
    }
    return $str;
}
function object_array($array) {
    if(is_object($array)) {
        $array = (array)$array;
    } if(is_array($array)) {
        foreach($array as $key=>$value) {
            $array[$key] = object_array($value);
        }
    }
    return $array;
}
/**
 * [urlencode_ch 只对url的中文内容进行urlencode]
 * @AuthorHTL
 * @DateTime  2018-06-04T17:56:28+0800
 * @param     [type]                   $url [description]
 * @return    [type]                        [description]
 */
function urlencode_ch($url)
{
    $pregstr = "/[\x{4e00}-\x{9fa5}]|[\u3002\uff1b\uff0c\uff1a\u201c\u201d\uff08\uff09\u3001\uff1f\u300a\u300b]+/u";//中文正则
    if (preg_match_all($pregstr, $url, $matchArray)) {
        foreach ($matchArray[0] as $key => $val) {
            $url = str_replace($val, urlencode($val), $url);//将转译替换中文
        }
        if (strpos($url, ' ')) {//若存在空格
            $url = str_replace(' ', '%20', $url);
        }
    }
    return $url;
}

/**
 * json_encode重载，改为默认支持中文
 * @param $param
 * @return string
 * @author magic
 */
function enjson($param)
{
    $param = json_decode(json_encode($param,JSON_NUMERIC_CHECK));
    return json_encode($param,JSON_UNESCAPED_UNICODE);
}

/**
 * json_decode重载，改为默认输出数组
 * @param $param
 * @param bool $mode 模式：true返回数组，false返回对象
 * @return mixed
 * @author magic
 */
function dejson($param,$mode=true)
{
    if($mode == true)
    {
        return json_decode($param,true);
    }else{
        return json_decode($param,false);
    }
}

/**
 * 将毫秒时间戳转化为date类型
 * @param $time
 * @return false|string
 * @author yjj
 */
function dateTime($time){
    return date('Y-m-d',$time/1000);
}

/**
 * 字符串过滤
 * @param $text 文本内容
 * @param bool $parseBr
 * @param bool $nr
 * @return mixed|string
 * @author magic
 */
function text( $text, $parseBr = false, $nr = false )
{
    $text = htmlspecialchars_decode( $text );
    $text = safe( $text, "text" );
    if ( !$parseBr && $nr )
    {
        $text = str_ireplace( array( "\r", "\n", "\t", "&nbsp;" ), "", $text );
        $text = htmlspecialchars( $text, ENT_QUOTES );
    }
    else if ( !$nr )
    {
        $text = htmlspecialchars( $text, ENT_QUOTES );
    }
    else
    {
        $text = htmlspecialchars( $text, ENT_QUOTES );
        $text = nl2br( $text );
    }
    $text = trim( $text );
    return $text;
}

/**
 * 安全过滤
 */
function safe( $text, $type = "html")
{
    $text_tags = "";
    $font_tags = "<i><b><u><s><em><strong><font><big><small><sup><sub><bdo><h1><h2><h3><h4><h5><h6>";
    $base_tags = $font_tags."<p><br><hr><a><img><map><area><pre><code><q><blockquote><acronym><cite><ins><del><center><strike>";
    $form_tags = $base_tags."<form><input><textarea><button><select><optgroup><option><label><fieldset><legend>";
    $html_tags = $base_tags."<ul><ol><li><dl><dd><dt><table><caption><td><th><tr><thead><tbody><tfoot><col><colgroup><div><span><object><embed>";
    $all_tags = $form_tags.$html_tags."<!DOCTYPE><html><head><title><body><base><basefont><script><noscript><applet><object><param><style><frame><frameset><noframes><iframe>";
    $text = strip_tags( $text, ${ $type."_tags" } );
    if ( $type != "all" )
    {
        while ( preg_match( "/(<[^><]+) (onclick|onload|unload|onmouseover|onmouseup|onmouseout|onmousedown|onkeydown|onkeypress|onkeyup|onblur|onchange|onfocus|action|background|codebase|dynsrc|lowsrc)([^><]*)/i", $text, $mat ) )
        {
            $text = str_ireplace( $mat[0], $mat[1].$mat[3], $text );
        }
        while ( preg_match( "/(<[^><]+)(window\\.|javascript:|js:|about:|file:|document\\.|vbs:|cookie)([^><]*)/i", $text, $mat ) )
        {
            $text = str_ireplace( $mat[0], $mat[1].$mat[3], $text );
        }
    }
    return $text;
}

/**
 * 统一返回结果信息
 * @param string $message 状态
 * @param array $data 消息数组
 * @param bool $print 是否直接返回
 * @return mixed $data
 */
function message($message, $data = null, $print = false)
{
    if (IS_CLI) {
        !empty($data) and print_r($data);
        return $message;
    }
    $data = [
        'message'   => $message,
        'timestamp' => getMillisecond(),
        'payload'   => $data
    ];
    //dump($data);exit;
    // 结果json字符串
    if ($print) {
        echo json_encode($data);
        exit();
    } else {
        return $data;
    }
}

/**
 * 获取当前时间戳
 * @return float 返回当前微妙数
 */
function getMillisecond()
{
    list($t1, $t2) = explode(' ', microtime());
    return (float)sprintf('%.0f', (floatval($t1) + floatval($t2)) * 1000);
}

/**
 * 浏览器友好的变量输出
 * @param mixed $var 变量
 * @param boolean $echo 是否输出 默认为True 如果为false 则返回输出字符串
 * @param string $label 标签 默认为空
 * @param boolean $strict 是否严谨 默认为true
 * @return void|string
 */
function dump($var, $echo=true, $label=null, $strict=true) {
    $label = ($label === null) ? '' : rtrim($label) . ' ';
    if (!$strict) {
        if (ini_get('html_errors')) {
            $output = print_r($var, true);
            $output = '<pre>' . $label . htmlspecialchars($output, ENT_QUOTES) . '</pre>';
        } else {
            $output = $label . print_r($var, true);
        }
    } else {
        ob_start();
        var_dump($var);
        $output = ob_get_clean();
        if (!extension_loaded('xdebug')) {
            $output = preg_replace('/\]\=\>\n(\s+)/m', '] => ', $output);
            $output = '<pre>' . $label . htmlspecialchars($output, ENT_QUOTES) . '</pre>';
        }
    }
    if ($echo) {
        echo($output);
        return null;
    }else
        return $output;
}

use think\Db;

/**
 * 记录错误信息
 * @param $msg 错误内容
 * @param string $name 错误接口
 * @param string $c_id 公司id
 * @param string $type 接口类型 1海量 2天眼查 3企查查
 * @author magic
 */
function insert_error_info($msg,$name='',$c_id=0,$type=0){


    try{
        $data['company_id'] = $c_id;
        $data['name'] = $name;
        $data['type'] = $type;
        $data['info'] = $msg;
        $data['add_time'] = date('Y-m-d H:i:s');
        Db::table('sg_error_info')->insert($data);
    }catch (Exception $e){
        //dump($e->getMessage());exit;
        //Log::error('[失败]Reg:Database Error:'.$e->getMessage());
    }


}
/*
 * 获取文件后缀名
 * @param  string $ext 文件名
 * @author yjj
 */
function get_extension($ext){
    return pathinfo($ext,PATHINFO_EXTENSION);
}

/**
 * 下载天眼查图片
 * @param $file_url 天眼查图片路径
 * @return bool|int
 * @author magic
 */
function file_uploads_img($file_url){
    if(!$file_url){
        return false;
    }
    $img_tianyan = "http://static.tianyancha.com";
    if(!strstr($file_url,'http://')){
        $file_url = $img_tianyan."/".$file_url;
    }
    $aa = strripos($file_url,'/');
    $path_name = substr($file_url,$aa);
    $save_to = ROOT_PATH . 'public' . DS ."/logo/product".$path_name;
    $content = file_get_contents($file_url);
    $re = file_put_contents($save_to, $content);
    return $re;
}

/**
 * 时间转换
 * @param $time
 * @param int $type
 * @return false|float|int|string
 * @author magic
 */
function date_time($time,$type=1){
    if(is_numeric($time)){
        if(strlen($time)>10){
            $time = $time/1000;
        }
        if($type==1){
            $time = date('Y-m-d H:i:s',$time);
        }else{
            $time = date('Y-m-d',$time);
        }
    }
    return $time;
}

function insert_history($company_id,$content,$type=1){
    $data['company_id'] = $company_id;
    $data['content'] = $content;
    $data['time'] = date('Y-m-d H:i:s',time());
    $data['type'] = $type;
    Db::table('sg_history')->insert($data);
}

/**
 * 将date格式转化为时间戳
 * @param $time
 * @return false|int
 * yjj
 */
function time_date($time){
    if((is_numeric($time)&&strlen($time)===13)||empty($time)){
        return $time;
    }else{
        return strtotime($time)*1000;
    }
}
/**
 * 分页公共函数
 * @author Steed
 * @param int $rows 每页显示条数
 * @return string
 */
function page($rows = 20) {
    $request = \think\Request::instance()->param();
    //当前页，默认第一页
    $page = (isset($request['page']) && !empty($request['page'])) ? $request['page'] : 1;
    //当前页开始条数
    $start = ($page - 1) * $rows;
    return $start . ',' . $rows;
}
/**
 * 计算总条数
 * @author Steed
 * @param $total
 * @param $rows
 * @return float
 */
function getTotalRow($tableName, $where) {
    $rows = Db::name($tableName) -> where($where) -> count();
    return $rows;
}
/**
 * 计算总页数
 * @author Steed
 * @param $total
 * @param $rows
 * @return float
 */
function getTotalPage($total, $rows) {
    return ceil($total / $rows);
}
function xml_encode($arr){
    $xml = '';
    foreach ($arr as $key=>$val){
        if(is_array($val)){
            $xml.="<".$key.">".xml_encode($val)."</".$key.">";
        }else{
            $xml.="<".$key.">".$val."</".$key.">";
        }
    }
    return $xml;
}
//将内容进行UNICODE编码
// function unicode_encode($name)
// {
//   $name = iconv('UTF-8', 'UCS-4', $name);
//   $len = strlen($name);
//   $str = '';
//   for ($i = 0; $i < $len - 1; $i = $i + 2)
//   {
//     $c = $name[$i];
//     $c2 = $name[$i + 1];
//     if (ord($c) > 0)
//     {  // 两个字节的文字
//       $str .= '\u'.base_convert(ord($c), 10, 16).base_convert(ord($c2), 10, 16);
//     }
//     else
//     {
//       $str .= $c2;
//     }
//   }
//   return $str;
// }
function unicode_encode($str, $encoding = 'UTF-8', $prefix = '\u', $postfix = ';') {
    $str = iconv($encoding, 'UCS-2', $str);
    $arrstr = str_split($str, 2);
    $unistr = '';
    for($i = 0, $len = count($arrstr); $i < $len; $i++) {
        $dec = hexdec(bin2hex($arrstr[$i]));
        $unistr .= $prefix . $dec . $postfix;
    }
    return $unistr;
}
  
function UnicodeEncode($str){
    //split word
    preg_match_all('/./u',$str,$matches);
 
    $unicodeStr = "";
    foreach($matches[0] as $m){
        //拼接
        $unicodeStr .= "\u".base_convert(bin2hex(iconv('UTF-8',"UCS-4",$m)),16,10);
    }
    return $unicodeStr;
}
// 图片转base64
function base64EncodeImage ($image_file) {
    $base64_image = '';
    $image_info = getimagesize($image_file);
    $image_data = file_get_contents($image_file);
    // $image_data = fread(fopen($image_file, 'r'), filesize($image_file));
    $base64_image = 'data:' . $image_info['mime'] . ';base64,' . chunk_split(base64_encode($image_data));
    return $base64_image;
}
function check_specialChars($text) {
    $flag = true;
    $text = trim($text,'');
    $preg='/[\w\x{4e00}-\x{9fa5}]+/u';
    $result = [];
    if(preg_match($preg,$text,$matches)){ //不允许特殊字符
        $new_str = $matches[0];
    }
    if(strlen($new_str) != strlen($text)) {
        $flag = false;
    }
    $result['status'] = $flag;
    return $result;
}