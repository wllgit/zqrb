<?php
namespace app\admin\controller;
use think\Request;
class Theme extends Base
{
    private function get_sign($paramAry){
        if(isset($paramAry['sign']))
            unset($paramAry['sign']);
        ksort($paramAry);
        $paramsTmp = array();
        foreach ($paramAry as $k => $v) {
            $paramsTmp[] = "$k=$v";
        }
        return md5(implode("&", $paramsTmp).'hylanda');
    }

    public function a(){
        $a='12';
        $a=array('aa'=>'9999999');
        $this->assign('a',$a);
        return $this->fetch();
    }

    //用户列表
    public function index()
    {
        //新闻头条
        $dd = file_get_contents("http://dev.api.stockalert.cn/tmpInfos/tmpList");
        //快涨头条列表
        $fasthead = file_get_contents("http://dev.api.stockalert.cn/topInfos/list");
        //解析json数据
        $data = json_decode($dd,true);
        $fasthead = json_decode($fasthead,true);
        $arr = $data['payload'];
        $fastitem = $fasthead['payload'];
        foreach ($fastitem as $key => &$value)
        {
            $value['publishedAt']  =  date("Y-m-d H:i:s",strtotime($value['publishedAt']));
            $value['index']=$key+1;
        }
        foreach ($arr as $key => &$value)
        {
            $value['publishedAt']  =  date("Y-m-d H:i:s",strtotime($value['publishedAt']));
            $value['nowtime']  =  date("Y-m-d H:i:s");
            $value['timedif']=floor((strtotime($value['nowtime'])-strtotime($value['publishedAt']))%86400/3600);
            //$value['timedif']  =  date("Y-m-d H:i:s");
            switch ($value['status'])
            {
                case -1:
                    $value['status']="已删除";
                    break;
                case 0:
                    $value['status']="未创建";
                    break;
                case 1:
                    $value['status']="已创建";
                    break;
                case 2:
                    $value['status']="已发布";
                    break;
            }
            $value['index']=$key+1;
        }
        $this->assign('fastitem', $fastitem);
        $this->assign('arr', $arr);
        return $this->fetch();
    }

    //service	        HLTopic.hl_create_topic	                            非空
    //appKey	        用户唯一AppKey ID	                                非空
    //name	            主题名	                                            非空	限定判定图名称不能包含的字符
    //type	            判定图类型 1 常用单对象、10 模版类型常用单对象	    非空
    //template_id	    判定图模板id	                                    可空	备注：创建非模板主题时，不传该参数。
    //customized_nodes	模板主题自定义节点信息	                            可空	备注：创建非模板主题时，不传该参数. 详细格式请参考下面的注解


    //创建主题
    //$them_type 主题类型   0普通主题   1模板主题
    //创建主题
    public function establish_theme($them_type){
        $hland_url = 'http://api-v3.hylanda.com/api.php';
        $sign_view = Request::instance()->param('name');
        $paramAry = array(
            'service' => 'HLTopic.hl_create_topic',
            'appKey' => 'AppKey ID',
            'name' => 'them_name',
            'type' => '',
        );
        $sign = $this->get_sign($paramAry);
        if($sign !== $sign_view){//验证签名
            return false;
        }
        $paramAry['sign'] = $sign;
        //创建主题类型
        if($them_type==0){
            // 1、普通主题
            $paramAry['template_id'] = '';
            $paramAry['customized_nodes'] = '';
        }
        $params = urlencode($paramAry);
        $result =$this->http_curl($params,'get',$hland_url);
        return $result;
//        Db::execute('insert into table (id, name) values (?, ?)',[8,'thinkphp']);


    }


    public function establish_theme1(){
//        $date = Db::query('select * from them_test where id=?',[1]);
//        Db::execute('insert into them_test (id, them_id) values (?, ?)',[8,123]);
//        print_r($date);
        try{
            Db::execute('insert into t_test (id, them_id) values (?, ?)',[2,123]);
        } catch (\Exception $e) {
            print_r($e);
        }
    }

    //发布
    public function publish(){
        if (Request::instance()->isPost())
        {
            $params = Request::instance()->post();
            //调用接口实现发布功能
            $post_data = array(
                'id' => $params['id'],
                'title' => $params['title'],
                'imgKey' => $params['imgKey'],
                'summary' => $params['summary']
            );
            $header[] = 'Content-Type:application/x-www-form-urlencoded';
            $mm=    curl('http://dev.api.stockalert.cn/tmpInfos/createTopInfo',$header,$post_data);
            return $mm;
        }
        else
        {
            //获取跳转的id
            $id = Request::instance()->get('id');
            //初始化
            $dd = file_get_contents("http://dev.api.stockalert.cn/tmpInfos/tmpInfo?id=$id");
            //解析json数据
            $data = json_decode($dd,true);

            $this->assign('arr',$data['payload']);
            return $this->fetch();
        }
    }


   public function http_curl($data ,$type='get',$url){
       //初始化
       $curl = curl_init();
       //设置抓取的url
       curl_setopt($curl, CURLOPT_URL, $url);
       //设置头文件的信息作为数据流输出
       curl_setopt($curl, CURLOPT_HEADER, 1);
       //设置获取的信息以文件流的形式返回，而不是直接输出。
       curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
       if($type == 'post'){
            //设置post方式提交
           curl_setopt($curl, CURLOPT_POST, 1);
           //设置post数据
           curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
       }
       //执行命令
       $data = curl_exec($curl);
       //关闭URL请求
       curl_close($curl);
       //显示获得的数据
//       print_r($data);
       return $data;
   }

}
