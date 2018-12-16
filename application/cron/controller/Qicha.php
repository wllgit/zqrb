<?php
/**
 * Author: magic
 * Date: 2017/8/4
 * Time: 下午3:50
 */
namespace app\cron\controller;
use think\Controller;
use think\Db;

/**
 * 企查查数据
 * @package app\cron\controller
 * @author Magic<huangct@sogukj@.com>
 */
class Qicha extends Controller{

    /**
     * 获取企查查接口数据
     * @author magic
     */
    public function get_qicha_interface(){

        $where['ci.status'] = 0;
        $where['ci.type'] = 2;
        $where['c.status'] = 1;
        $where['ci.interface_id'] =40;
        $where['ci.company_id'] = 1;
        $data = Db::table("sg_company_interface")
            ->alias('ci')
            ->join('sg_table t','ci.interface_id = t.id')
            ->join('sg_company c','ci.company_id = c.id')
            ->field("ci.*,t.name,t.tableName,c.name as company_name,c.qicha_id")
            ->where($where)->limit(20)->order("ci.update_time asc")->select();
        foreach($data as $key=>$val){
            //获取轮询数据改状态 进行中
            //Db::table('sg_company_interface')->where(['id'=>$val['id']])->update(['status'=>1]);
            $table_name = str_replace(['sg_','ty_','qc_'],'',$val['tableName']);
            $table = model($table_name);
            //dump($table);
            //调用企查查接口
            $interface = $table->get_qicha_data($val['qicha_id'],$val['company_name']);
            if(isset($interface['error']) && $interface['error']){
                insert_error_info($interface['msg'],'get_qicha_interface',$val['company_id'],3);
            }else{
                //获取接口数据插入数据库中
                $table->save_qicha_data($val['company_id'],$interface);
            }
            $time = date('Y-m-d H:i:s',time());
            //本次轮询完成
            $re = Db::table('sg_company_interface')->where(['id'=>$val['id']])->update(['status'=>2,'update_time'=>$time]);
            dump($re);exit;
        }

    }



}