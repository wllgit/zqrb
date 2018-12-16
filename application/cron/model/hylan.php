<?php
/**
 * Author: magic
 * Date: 2017/7/19
 * Time: 上午10:42
 */
namespace app\cron\model;
use think\Model;
use think\Db;
class hylan extends Model
{
    /**
     * @param 获取sign
     * @return string
     * @author magic
     */
    public function get_sign($paramAry){
        if(isset($paramAry['sign']))
            unset($paramAry['sign']);
        ksort($paramAry);
        $paramsTmp = array();
        foreach ($paramAry as $k => $v) {
            $paramsTmp[] = "$k=$v";
        }
        return md5(implode("&", $paramsTmp).'hylanda');
    }

    /*
     * 获取海量实时任务数据
     * @param $task_id 海量实时任务ID
     * @param $offset_id 上一次获取数据返回的最大数据 偏移量
     * @author magic
     */
    public function get_hy_data($task_id,$offset_id,$num=100){

        $param['appKey'] = config('hy_appkey');
        $param['service'] = "HLTopic.hl_get_data";
        $param['offset_id'] = $offset_id;
        $param['num'] = $num;
        $param['task_id'] = $task_id;
        //dump($param);

//        $param['appKey'] = config('hy_appkey');
//        $param['service'] = "HLTopic.hl_get_data";
//        $param['offset_id'] = "2017_08_010";
//        $param['num'] = $num;
//        $param['task_id'] = "1421630339262835";

        $result = $this->get_hy_api($param);
        return $result;

    }

    /*
     * 创建海量主题
     * @param $name 主题名
     * @param $forceWords 强制词
     * @return mixed  'res': 0, data:主题id、实时任务id、判定图id, 'msg': '创建模板主题成功'
     * @author magic
     */
    public function hl_create_topic($name,$forceWords){
        //组装模板主题自定义节点信息
        $customized_nodes = $this->get_customized_nodes($forceWords);

        $param['appKey'] = config('hy_appkey');
        $param['service'] = "HLTopic.hl_create_topic";
        $param['customized_nodes'] = $customized_nodes;
        $param['name'] = $name;
        $param['type'] = 1;
        $param['template_id'] = config('hy_template_id');
        $result = $this->get_hy_api($param);
        return $result;
    }

    /*
  * 删除海量实时任务数据
  * @param $topic_id 海量主题id
  * @author magic
  * @return  {res :0 ,data :[],msg:"删除成功"}
  */
    public function delete_hy_topic($topic_id,$task_id){
        //关闭任务
        $re = $this->hl_modify_task_status($task_id);
        if($re['error']){
            return ['error'=>true,'msg'=>$re['msg']];
        }

        $param['appKey'] = config('hy_appkey');
        $param['service'] = "HLTopic.hl_delete_topic";
        $param['hl_topic_id'] = $topic_id;
        $result = $this->get_hy_api($param);
        if($result['res']===0){
            //创建成功
            $re = Db::table('hy_cron')->where(['topic_id'=>$topic_id])->delete();
            if(!$re){
                return ['error'=>true,'msg'=>'删除主题成功，更新数据库失败，请联系管理员'];
            }
            return ['error'=>false];
        }else{
            return ['error'=>true,'msg'=>$result['msg']];
        }
        //return $result;
    }

    /*
     * 调用海量api接口
     * @param $param 调用海量传递参数
     * @return mixed
     * @author magic
     */
    public function get_hy_api($param){
        $sign = $this->get_sign($param);
        foreach($param as $key=>$val){
            $param[$key] = urlencode($val);
        }
        $param['sign'] = $sign;
        $url = config('hy_url');
        $result = curl($url,[],$param);
        $result = json_decode($result,true);
        return $result;
    }

    /**
     * 创建海量主题
     * @param $name 主题名称
     * @param $forcewords 强制词
     * @param $hy_id hy_cron表主键
     * @return array 'error'=>true失败 'msg':失败内容
     * @author magic
     */
    public function create_topic($name,$forcewords,$hy_id){
        if(!$name || !$forcewords || !$hy_id){
            return ['error'=>true,'msg'=>'参数错误'];
        }
        $result = $this->hl_create_topic(trim($name),$forcewords);
        //dump($result);
        if($result['res']===0){
            //创建成功
            $data = [];
            $data = $result['data'];
            $data['offset_id'] = date('Y_m_d')."0";
            $data['update_time'] = time();
            $re = Db::table('hy_cron')->where(['id'=>$hy_id])->update($data);
            if(!$re){
                return ['error'=>true,'msg'=>'创建主题成功，更新数据库失败，请联系管理员'];
            }
            return ['error'=>false];
        }else{
            return ['error'=>true,'msg'=>$result['msg']];
        }
    }

    /**
     * 修改海量主题
     * @param $topic_id 海量主题号
     * @param $forceWords 强制词
     * @return array
     * @author magic
     */
    public function edit_hy_topic($topic_id,$forceWords){

        //组装模板主题自定义节点信息
        $customized_nodes = $this->get_customized_nodes($forceWords);

        $param['appKey'] = config('hy_appkey');
        $param['service'] = "HLTopic.hl_set_template_topic_info";
        $param['customized_nodes'] = $customized_nodes;
        $param['hl_topic_id'] = $topic_id;
        $result = $this->get_hy_api($param);
        if($result['res']===0){
            //修改成功
            $re = Db::table('hy_cron')->where(['topic_id'=>$topic_id])->update(['forceWords'=>$forceWords]);
            if($re===false){
                return ['error'=>true,'msg'=>'修改主题成功，更新数据库失败，请联系管理员'];
            }
            return ['error'=>false];
        }else{
            return ['error'=>true,'msg'=>$result['msg']];
        }
        //return $result;
    }

    /**
     * 节点信息json串
     * @param $forceWords
     * @return string
     * @author magic
     */
    public function get_customized_nodes($forceWords){
        $forceWords = explode('#',$forceWords);
        $template_info = config('template_info');
        $template_arr = json_decode($template_info,true);
        foreach($template_arr['nodes'] as $key=>&$val){
            foreach($val['properties'] as $kk=>&$vv){
                if($vv['propertyName'] == 'feawords'){
                    $vv['value'] = isset($forceWords[$key])?$forceWords[$key]:$forceWords[0];;//特征词
                }
                if($vv['propertyName'] == 'ambiguwords'){
                    $vv['value'] = "";//歧义词
                }
            }
        }
        $customized_nodes = json_encode($template_arr);
        return $customized_nodes;
    }

    /**
     * 开启/关闭主题的实时任务
     * @param $task_id 海量实时任务ID
     * @param int $status 任务状态码, 0为关闭, 1为开启.默认为0
     * @author magic
     */
    public function hl_modify_task_status($task_id,$status=0){
        $param['appKey'] = config('hy_appkey');
        $param['service'] = "HLTopic.hl_modify_task_status";
        $param['task_id'] = $task_id;
        $param['status'] = $status;
        $result = $this->get_hy_api($param);
        if($result['res']===0){
            return ['error'=>false];
        }else{
            return ['error'=>true,'msg'=>$result['msg']];
        }
    }

}