<?php
namespace app\admin\controller;
use think\Request;
class Fastrise extends Base
{
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
   public function delfast(){
      $id = Request::instance()->get('id');
      $post_data = array(
         'id' => $id,
       );
      $header[] = 'Content-Type:application/x-www-form-urlencoded';
      $data=    curl('http://dev.api.stockalert.cn/topInfos/remove',$header,$post_data);
      return $data;
    }
    public function delete(){
      $id = Request::instance()->get('id');
      //调用接口实现发布功能
      $post_data = array(
          'id' => $id,
       );
       $url = "http://dev.api.stockalert.cn/tmpInfos/removeTopic";
      return send_post($url, $post_data);
    }

    public function check()
    {
              if (Request::instance()->isPost())
            {
                    $params = Request::instance()->post();


                    $id = Request::instance()->get('id');
                   if(array_key_exists('stocks', $params)){
                     $sotcks = $params['stocks'];
                    }else {
                      $sotcks = array();
                    }
                    if(array_key_exists('themes', $params)){
                      $themes = $params['themes'];
                     }else {
                       $themes = array();
                     }
                   $stocksadd =preg_replace("/\s/","",$params['stocksadd']);
                   $themesadd =preg_replace("/\s/","", $params['themesadd']);
                   if (!empty($stocksadd)) {
                     $stocksadd=explode("/",$stocksadd);
                     foreach ($stocksadd as $key => $value) {
                       Array_push($sotcks,$value);
                     }
                   }
                   if (!empty($themesadd)) {
                     $themesadd=explode("/",$themesadd);
                     foreach ($themesadd as $key => $value) {
                       Array_push($themes,$value);
                     }
                   }
                  $themes=implode(",",$themes);
                  $sotcks=implode(",",$sotcks);

                  // dump($sotcks);
                  // exit;

                  $post_data = array(
                     'id' => $id,
                     'title' => $params['title'],
                     'summary' => $params['summary'],
                     'themes' => $themes,
                     'stocks' => $sotcks,
                   );
                  $header[] = 'Content-Type:application/x-www-form-urlencoded';
                  $data=    curl('http://dev.api.stockalert.cn/topInfos/update',$header,$post_data);
                  return $data;
            }else {
              //获取跳转的id
              $id = Request::instance()->get('id');
              $dd = file_get_contents("http://dev.api.stockalert.cn/topInfos/detail?id=$id");
                //解析json数据
               $data = json_decode($dd,true);
               if (!array_key_exists("summary",$data['payload'])){
                    $data['payload']['summary'] = '';
                }
                //  dump($data);
                //   exit;
               $arr = $data['payload'];
               $stocks = $arr['stocks'];
               $themes = $arr['themes'];
               foreach ($stocks as $key => &$value) {
                 $efect = round($value['effect'], 5)*100;
                 $value['nameadd'] = $value['name'] . " " . $efect.'%';
               }
               foreach ($themes as $key => &$value) {
                 $efect = round($value['effect'], 5)*100;
                 $value['nameadd'] = $value['name'] . " " . $efect.'%';
               }
               $this->assign('arr',$arr);
               $this->assign('id',$id);
               $this->assign('stocks',$stocks);
               $this->assign('themes',$themes);
              //  $this->assign('force',$force);
               return $this->fetch();
            }
    }
    public function detail(){
       //获取新闻的详情页面
       $id = Request::instance()->get('id');
       $dd = file_get_contents("http://dev.api.stockalert.cn/tmpInfos/tmpInfo?id=$id");
        //解析json数据
       $data = json_decode($dd,true);
       $arr = $data['payload'];
       $arr['publishedAt']  =  date("Y-m-d H:i:s",strtotime($arr['publishedAt']));
        return $arr;
    }
    public function fastdetail(){
       //获取新闻的详情页面
       $id = Request::instance()->get('id');
       $dd = file_get_contents("http://dev.api.stockalert.cn/topInfos/detail?id=$id");
       $data = json_decode($dd,true);
       $arr = $data['payload'];
       $arr['createdAt']  =  date("Y-m-d H:i:s",strtotime($arr['createdAt']));
       $arr['updatedAt']  =  date("Y-m-d H:i:s",strtotime($arr['updatedAt']));
        return $arr;
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
  }
