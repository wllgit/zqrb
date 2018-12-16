<?php
/**
 * Author: magic
 * Date: 2017/7/17
 * Time: 下午3:36
 */
namespace app\cron\model;
use think\Model;

class cron extends Model
{
    protected $pk = 'id';
    protected  $table='ty_cron';
     public function get_time_data(){
         $data = $this->alias('cr')->join('sg_company sc','sc.id=cr.company_id')
             ->where(['sc.status'=>1,'cr.get_time_status'=>0])->limit(5)->select();
         return $data;
     }







}
