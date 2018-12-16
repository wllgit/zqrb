<?php
namespace app\cron\controller;

use think\Controller;
use app\cron\model\cron;
use think\Db;

class Index extends Controller
{
    //抓取数据url
    private $py_url = 'http://192.168.1.107:5000';
    /**
     * 获取最近时间
     * @author magic
     */
    public function get_tianyan_time()
    {

        $cronModel = new cron();
        $data = $cronModel->alias('cr')->field("cr.id,cr.company_id,sc.tianyan_id,sc.update_time")->join('sg_company sc','sc.id=cr.company_id')
            ->where(['sc.status'=>1,'cr.get_time_status'=>0,'sc.tianyan_id'=>array('neq','')])->order("get_time_time asc")->limit(2)->select();

        dump($cronModel->getlastsql());
        $cookie = Db::table('sg_cookie')->where(['status'=>1])->order("time asc")->value('cookie');
        dump(Db::table('sg_cookie')->getLastSql());exit;
        $url = $this->py_url."/updatetime";
        dump($url);
        //dump($cookie);exit;
        foreach($data as $key=>$val){

            $postFields=[];
            $postFields['cid'] = $val['tianyan_id'];
            $postFields['Cookie'] = $cookie;
            $result = curl($url,[],$postFields);
            dump($result);
            $re = json_decode($result,true);
            //dump($re);exit;
            if(isset($re['message']) && $re['message'] == 'ok'){
                if($val['update_time']!=$re['updatetime']){
                    //调用数据接口
                    $url_data = $this->py_url."/data";
                    dump($url_data);
                    $result = curl($url_data,[],$postFields);
                    dump($result);
                    insert_history($val['company_id'],$result);
                }
                Db::table('sg_company')->where('id',$val['company_id'])->update(['update_time'=>$re['updatetime']]);
            }
            $cronModel->where("id",$val['id'])->update(['get_time_time'=>time(),'get_time_status'=>2]);
            //exit;
        }

    }



    /**
     * 请求处理队列
     */
    public function accept()
    {
        $url = "pe.local.com";
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url.'/cron/index/get_tianyan_interface');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, "");
        curl_setopt($ch, CURLOPT_TIMEOUT, 1);
        $response = curl_exec($ch);
        if(curl_errno($ch)){
            $result = 0;
        }else{
            $result = 1;
        }
        curl_close($ch);
        return $result;
    }

    /**
     * 获取天眼查接口数据
     * @author magic
     */
    public function get_tianyan_interface(){

        $where['cron_count'] = array('gt',0);//取大于0的数据轮询
        $where['ci.status'] = 0;
        $where['ci.type'] = 1;
        $where['c.status'] = 1;
        $where['ci.interface_id'] = 51;
        $where['ci.company_id'] = 2;
        $data = Db::table("sg_company_interface")
            ->alias('ci')
            ->join('sg_table t','ci.interface_id = t.id')
            ->join('sg_company c','ci.company_id = c.id')
            ->field("ci.*,t.name,t.tableName,c.name as company_name,c.tianyan_id,t.ty_count")
            ->where($where)->limit(20)->order("ci.update_time asc")->select();
        foreach($data as $key=>$val){
            //抓取的个数大于一页的数量就调用接口
            if($val['cron_count']>$val['ty_count']){
                //获取轮询数据改状态 进行中
                //Db::table('sg_company_interface')->where(['id'=>$val['id']])->update(['status'=>1]);
                $table_name = str_replace(['sg_','ty_','qc_'],'',$val['tableName']);
                $table = model($table_name);
                //dump($table);
                //调用天眼查接口
                $interface = $table->get_tianyan_data($val['tianyan_id'],$val['company_name']);
                //dump($interface);
                if(isset($interface['error']) && $interface['error']){
                    insert_error_info($interface['msg'],'get_tianyan_interface',$val['company_id'],2);
                }else{
                    //获取接口数据插入数据库中
                    $table->save_tianyan_data($val['company_id'],$interface);
                }
            }

            $time = date('Y-m-d H:i:s',time());
            //本次轮询完成
            $re = Db::table('sg_company_interface')->where(['id'=>$val['id']])->update(['status'=>2,'update_time'=>$time]);
            dump($re);
        }

    }

    /**
     * 获取抓取天眼查数据更新到各个表
     * @author magic
     */
    public function get_tianyan_data(){


        $table = Db::table("sg_table")->where(['type'=>['in',[1,3]],'interfaceName'=>['neq','']])->select();
        //dump(Db::table("sg_table")->getLastSql());
        //dump($table);
        $where['status'] = 0;
        $where['type'] = 1;
        //$where['id'] = 21;
        $data = Db::table("sg_history")->where($where)->limit(1)->order("time asc")->select();
        dump($data);
        foreach($data as $key=>$val){
            $time = date('Y-m-d H:i:s',time());
            //获取轮询数据改状态 进行中
            //Db::table('sg_history')->where(['id'=>$val['id']])->update(['status'=>1]);
            $content = json_decode($val['content'],true);
            if($content){
                $count_info = $content['count_info'];
                $count_info['baseinfo'] = 1;//基本信息
                //若是上市公司需要加信息
                if(isset($content['baseinfo']['is_volatility']) && $content['baseinfo']['is_volatility']==1){
                    $count_info['volatility'] = 1;//股票行情
                    $count_info['companyInfo'] = 1;//企业简介
                    $count_info['issueRelated'] = 1;//发行相关
                    $count_info['allotment_info'] = 1;//配股情况
                }
                foreach($table as $kk=>$vv){
                    $table_name = str_replace(['sg_','ty_','qc_'],'',$vv['tableName']);
                    $interfaceName = $vv['interfaceName'];
                    $tables = model($table_name);
                    if(array_key_exists($interfaceName,$count_info) && $count_info[$interfaceName]>0){
                        //更新调用接口轮询表
                        Db::table("sg_company_interface")->where(['company_id'=>$val['company_id'],'interface_id'=>$vv['id'],'type'=>1])->update(['cron_count'=>$count_info[$interfaceName]]);
                        //更新数据
                        $tables->save_data($val['company_id'],$content[$interfaceName],1);
                    }
                }
            }

            //本次轮询完成
            $re = Db::table('sg_history')->where(['id'=>$val['id']])->update(['status'=>2,'time'=>$time]);
        }

    }




}
