<?php
/**
 * Author: magic
 * Date: 2017/7/20
 * Time: 下午6:57
 */
namespace app\cron\controller;
use think\Controller;
use app\cron\model\hylan;
use think\Db;
use app\common\model\Company;
use app\common\model;
use think\Exception;

/**
 * Class Hylanda 抓取海量数据
 * @package app\cron\controller
 * @author magic
 */
class Hylanda extends Controller{

//    public  function get_template_info(){
//        $arr['appKey'] = config('hy_appkey');
//        $arr['service'] = "HLTopic.omi_fetch_template_info";
//        $arr['templateCode'] = '10731110730063';
//        $hy = new hylan();
//        $sign = $hy->get_sign($arr);
//        $arr['sign'] = $sign;
//        $url = config('hy_url');
//        $data = curl($url,[],$arr);
//        $data = json_decode($data,true);
//        dump($data);exit;
//    }

    /**
     * 创建海量主题（只创建一次 可以设强制词时主动调取一次）
     * @author magic
     */
    public function create_topic(){

        $hy = new hylan();
        //获取有强制词且没有任务id的数据轮询
        $hy_data = Db::table('hy_cron')->field("id,company_id,forceWords")->where(['forceWords'=>array('<>',''),'task_id'=>''])->order("update_time asc")->limit(5)->select();
        foreach($hy_data as $kk=>$vv){
            $company = Company::get($vv['company_id']);
            $result = $hy->hl_create_topic(trim($company->name),$vv['forceWords']);
            dump($result);
            if($result['res']===0){
                //创建成功
                $data = [];
                $data = $result['data'];
                $data['offset_id'] = date('Y_m_d')."0";
                $data['update_time'] = time();
                $re = Db::table('hy_cron')->where(['id'=>$vv['id']])->update($data);
                if(!$re){
                    insert_error_info("更新海量创建主题信息失败",'create_topic',$vv['company_id'],1);
                }
            }else{
                //失败记录历史记录
                insert_error_info($result['msg'],'create_topic',$vv['company_id'],1);
            }
        }
    }

    /**
     * 获取海量实时任务数据 （不停调轮询获取数据 暂定每2小时获取一次）
     * @author magic
     */
    public function get_hy_data(){

        file_put_contents('hy_get_test.txt','1111');
        //获取状态为0且有任务id的数据轮询
        $hy_data = Db::table('hy_cron')->field("id,company_id,task_id,offset_id")->where(['status'=>0,'task_id'=>array('neq','')])->order("update_time asc")->limit(2)->select();
        if(!$hy_data){
            Db::table('hy_cron')->where(['status'=>2])->update(['status'=>0]);
            $hy_data = Db::table('hy_cron')->field("id,company_id,task_id,offset_id")->where(['status'=>0,'task_id'=>array('neq','')])->order("update_time asc")->limit(2)->select();
        }
        dump($hy_data);
        $hy = new hylan();
        $news = model('HyNews');
        foreach ($hy_data as $key=>$val){
            $company_id = $val['company_id'];
            $next_id = $val['offset_id'];
            //获取轮询数据改状态 进行中
            Db::table('hy_cron')->where(['id'=>$val['id']])->update(['status'=>1]);
            //调用获取数据接口
            $result = $hy->get_hy_data($val['task_id'],$val['offset_id'],10);
            //file_put_contents('hy_test2.txt',json_encode($result));
            //dump($result);
            try{
                // 启动事务
                Db::startTrans();
                if($result['res']===0){
                    //创建成功
                    $data = $result['data']['response'];
                    dump($data);
                    if(isset($data['resource']) && $data['resource']){
                        $list= $data['resource'];
                        foreach($list as $kk=>&$vv){
                            $is_exit = Db::table('hy_news')->field("id")->where(['title'=>$vv['title'],'company_id'=>$company_id])->find();
                            if($is_exit){
                                continue;
                            }
                            //dump($vv['related_info']);exit;
                            $vv['tags'] = $news->get_tags($vv['related_info']['item']);
                            $vv['related_info'] = json_encode($vv['related_info']);
                            $vv['company_id'] = $company_id;
                            $vv['ids'] = $vv['id'];
                            unset($vv['id']);
                            $data_id = Db::table('hy_news')->strict(false)->insertGetId($vv);
                            dump($data_id);
                            if(!$data_id){
                                throw new Exception('添加数据失败');
                            }
                        }
                        //成功后修改next_id偏移量字段
                        $next_id = $data['next_id'];
                    }

                }else{
                    throw new Exception($result['msg']);
                }

                //本次轮询完成
                $re = Db::table('hy_cron')->where(['id'=>$val['id']])->update(['status'=>2,'offset_id'=>$next_id]);
                if(!$re){
                    throw new Exception('更新数据失败');
                }
                dump('task_id:'.$val['task_id']."轮询完成");
                // 提交事务
                Db::commit();
                //exit;

            }catch (Exception $e){

                // 回滚事务
                Db::rollback();
                //失败记录历史记录
                $msg = $e->getMessage();
                dump($msg);
                insert_error_info($msg,'get_hy_data',$company_id,1);
            }

        }
    }


}