<?php
/**
 * Created by PhpStorm.
 * User: sogu
 * Date: 2018/6/15
 * Time: 下午2:26
 */

namespace app\cron\controller;

use think\Controller;
use think\Db;

class NewsPaper extends Controller{
    /**
     * 电子报
     *
     */
    public function eNewsPaper($param=''){

        $date = date('Y-m',time());
        $day = date('d',time());

        if(isset($param)){
            $date = substr($param,0,7);
            $day = substr($param,-2);
        }

        //获取页面
        $url = "http://epaper.zqrb.cn/html/".$date."/".$day."/node_2.htm";
        $cont = @file_get_contents($url);

        //获取手机端的数据
        $url3 = "http://m.epaper.zqrb.cn/html/".$date."/".$day."/node_2.htm";
        $cont3 = @file_get_contents($url3);

        if(!$cont3 || !$cont){
            return false;
        }

        //匹配所有头部 例：A1头版
        $preg='/<span\s+class=\"STYLE1\">(.*)<\/span>/i';
        preg_match_all($preg,$cont,$title);

        //循环匹配标题和链接
        if(isset($title['1']) && $title['1']){
            foreach ($title['1'] as $v){

                //匹配一个头的内容
                $preg1 = '%>'.$v.'([^$]*?)height=\"4\"%';
                preg_match($preg1,$cont,$arr);

                //根据一个头的内容匹配标题和链接
                $preg2 = '%href=([^"]*)\s+class[^?]*">([^"]*)<\/a>%';
                preg_match_all($preg2,$arr['0'],$arr1);

                //链接是转成k，标题转成值
                $result = array_combine($arr1['1'],$arr1['2']);

                //循环存入库中

                if(isset($result) && $result){
                    foreach ($result as $z=>$x){
                        $data =[
                            'head'  =>  str_replace(' ','',$v),
                            'title' =>  trim($x),
                            'url'   =>  'http://m.epaper.zqrb.cn/html/'.$date.'/'.$day.'/'.trim($z),
                            'create_time'   => time(),
                            'date'  =>  $date.'-'.$day,
                        ];
                        $is_exist = Db::table('scs_news_paper')->where('url',$data['url'])->find();
                        //存在记录更新，不存在插入
                        if(isset($is_exist)){
                            $res = Db::table('scs_news_paper')->where('id',$is_exist['id'])->update($data);
                        }else{
                            $res = Db::table('scs_news_paper')->insert($data);
                        }
                    }
                }

            }
        }

        //处理手机端数据
        $preg3 = '%id=\"page-1\"([^S]*?)<\/div>%';
        preg_match($preg3,$cont3,$pic3);

        //获取head和url
        $preg_pic = '%href=([^S]*?)>([^S]*?)<\/a>%';
        preg_match_all($preg_pic,$pic3['1'],$pic);
        $result3 = array_combine($pic['1'],$pic['2']);

        if(isset($result3) && $result3){
            foreach ($result3 as $k=>$v){
                $data3['pic_url'] = 'http://m.epaper.zqrb.cn/html/'.$date.'/'.$day.'/'.$k;
                $where_3['head'] = str_replace(' ','',$v);
                $where_3['date'] = $date.'-'.$day;
                //根据head头和日期存入库中
                $res2 = Db::table('scs_news_paper')->where($where_3)->update($data3);
            }
        }

        if(isset($param)){
            return true;
        }
        echo json_encode(array('code'=>0,'msg'=>'success'));
    }
    /**
     * 启动swoole异步task服务进程监听任务
     */
    public function swoole(){

        $serv = new \swoole_server("0.0.0.0", 9501);
        echo 'connect successful......';

        $serv->on('receive', function($serv, $fd, $from_id, $data) {
            $mes = json_decode($data,true);
            //echo $mes['news_id'];
            //计算热点
            if(isset($mes['news_id']) && $mes['type'] == 1){

                //传入新闻id计算sort排序字段并修改
                $config = Db::table('scs_config')->field('click_weight,transmit_weight,colletion_weight,praise_weight')
                    ->where('is_delete',0)
                    ->find();
                $news = Db::table('scs_news')->field('click_num,collect_num,praise_num,transmit_num,publish_time,title')
                    ->where('id',$mes['news_id'])
                    ->find();
                $hour = ceil((time() - $news['publish_time'])/3600);
                if(72-$hour <= 0){
                    $data = 0;
                }else{
                    $data = (($config['click_weight'] / 100 * $news['click_num']) + ($config['transmit_weight'] / 100 * $news['transmit_num'])
                            + ($config['colletion_weight'] / 100 * $news['collect_num']) + ($config['praise_weight'] / 100 * $news['praise_num']))
                        *(3 * 24 - $hour);
                }
                $res = Db::table('scs_news')->where('id',$mes['news_id'])->update(['sort' => intval($data)]);
                echo $res;

                //添加log记录
                $log_data = array(
                    'news_id' => $mes['news_id'],
                    'title'   => $news['title'],
                    'result'  => $res,
                    'date'    => date('Y-m-d H:i:s',time())
                );
                //$log = Db::table('scs_swoole_log')->insert(['content'=>json_encode($log_data,JSON_UNESCAPED_UNICODE)]);

                //修改热点表的新闻id
                $day = Db::table('scs_column')->field('news_num,day_num')->where(['title'=>'热点','is_show'=>1,'is_delete'=>0])->find();
                $time = time() - $day['day_num'] * 86400;//print_r($time);exit;
                $res = Db::table('scs_news')
                    ->field('id')
                    ->where('publish_time','>=',$time)
                    ->where(['is_show'=>1,'is_delete'=>0])
                    ->order('is_hot desc,sort desc')
                    ->limit($day['news_num'])
                    ->select();
                $data = implode(',',array_column($res,'id'));//print_r($data);
                $result = Db::table('scs_hot')->where('is_delete',0)->update(['news_ids'=>$data]);
            }
            //分词
            if($mes['type'] == 2){
                $this->aboutNows($mes['title'],$mes['news_id']);
            }
        });
        // 开始
        $serv->start();
    }
    /**
     * 分词，相关新闻
     * @param $data 标题
     * @param $news_id 新闻id
     */
    protected function aboutNows($data,$news_id){
        /*$request = input();
        $data = $request['data'];//标题
        $news_id = $request['news_id'];//添加的新闻id*/

        $url = 'https://zqrb.stockalert.cn/scws';
        $post_data = array(
            'keywords'    =>  $data
        );

        //三方接口把标题进行分词
        $curl = curl($url,[],$post_data);
        $words = json_decode($curl,true);
        $word = array_column($words['words'],'word');
        $arr = array();

        if(isset($word) && $word){
            foreach ($word as $k=>$v){
                $res = Db::table('scs_news')
                    ->field('id')
                    ->where(['is_show'=>1,'is_delete'=>0])
                    ->where('id','<>',$news_id)
                    ->where("MATCH(title,detail,summary,keywords,source)AGAINST('$v')")
                    ->select();
                $arr[] = $res;
            }
        }

        //三维数组转二维数组
        $newArr = array();
        if(isset($arr) && $arr){
            foreach($arr as $key=>$val){
                foreach($val as $k1=>$v1){
                    $newArr[] = $v1;
                }
            }
        }

        //取出现次数最多的id，和转字符串格式
        $id = array_column($newArr,'id');
        $count = array_count_values($id);
        arsort($count);
        $value = array_slice($count,0,4,true);
        $key_id = array_keys($value);//数组关联id
        $is_id = implode(',',$key_id);//字符串关联id
        $count_id = count($key_id);

        //匹配数量不够4的情况下，缺几个，随机取几个
        if($count_id < 4){
            $round = Db::table('scs_news')->field('id')->where(['is_show'=>1,'is_delete'=>0])->where('id','<>',$news_id)->order('rand()')->limit(4)->select();
            $slice_id = array_slice(array_column($round,'id'),0,4-$count_id);
            $arr_id = array_merge($key_id,$slice_id);
            $is_id = implode(',',$arr_id);
        }

        $data = [
            'news_id' => $news_id,
            'related_ids' => $is_id,
            'create_time' => time(),
            'update_time' => time()
        ];
        Db::table('scs_related')->insert($data);

        //根据相似的id去用这个新闻id更新相似的老得id

        if(isset($key_id) && $key_id){
            foreach ($key_id as $a=>$s){

                $old_id = Db::table('scs_related')->field('related_ids')->where(['news_id'=>$s,'is_delete'=>0])->find();
                $arr3 = explode(',',$old_id['related_ids']);

                //在数组头加一个，在数组末尾去掉一个
                array_unshift($arr3,$news_id);
                array_pop($arr3);
                $data3 = implode(',',$arr3);
                $new_id = Db::table('scs_related')->where('news_id',$s)->update(['related_ids'=>$data3,'update_time'=>time()]);
            }
        }

    }
    /**
     * 测试swoole
     */
    public function client(){
        $req = input();

        if($req['type'] == 1){
            $data = [
                'type' => 1,
                'news_id' => $req['news_id']
            ];
        }
        if($req['type'] == 2){
            $data = [
                'type' => 2,
                'news_id' => $req['news_id'],
                'title'   => $req['title']
            ];

        }
        swoole_client($data);
    }
















}