<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/6/9
 * Time: 18:41
 */
namespace app\admin\model;
use \think\Model;

class Start extends Model{

    //保存数据
    public function saveStartpage($param){
        try{
            $this->save($param);
            $code = 1; $data = 'success';
        }catch( PDOException $e){
            $code = 0; $data = $e->getMessage();
        }
        return ['code' => $code, 'data' => $data];
    }

    //查询数据
    public function getStartpage($id=''){
        if(!empty($id)){
            $info = $this->where('id', $id)->find();
        }else{
            $info = $this->db('start')->select();
        }

        return $info;
    }

    //更新数据
    public function updateStartpage($param,$where){
//        $this->save($param,$where);
        try{
           db('start')->where($where)->update($param);
            $code = 1; $data = 'success';
        }catch( PDOException $e){
            $code = 0; $data = $e->getMessage();
        }
        return ['code' => $code, 'data' => $data];
    }
    //删除数据
    public function delStartpage($where){

        try{
            $this->db('start')->where($where)->delete();
            $code = 1; $data = 'success';
        }catch( PDOException $e){
            $code = 0; $data = $e->getMessage();
        }
        return ['code' => $code, 'data' => $data];
    }


}