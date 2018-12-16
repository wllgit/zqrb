<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/6/5
 * Time: 10:24
 */
namespace app\admin\model;
use think\Model;
use think\Db;

class Column extends Model{
    protected $table = 'scs_column';
    //保存数据
    public function saveColumn($param){
        $result = Db::table('scs_column')->insert($param);
        $userId = Db::name('column')->getLastInsID();
        $return['result'] = $result;
        $return['userId'] = $userId;
        return $return;
    }


    //查询数据
    public function getColumns($where = '',$field= '*', $order = 'c.id desc', $offset = '', $limit = 10){
        return $this->alias('c')->join('scs_admin a','c.admin_id = a.id')->where($where)->field($field)->order($order)->paginate($limit);
    }

    //单条数据查询
    public function getSingleColumn($where = [], $field = '*'){
        return $this->where($where)->field($field)->find();
    }


    //保存多条数据
    public function moreInsert($data){
        $result = Db::name('column')->insertAll($data);
        return $result;
    }


    //更新栏目
    public function updateColumn($where,$data){
        $result = Db::name('column')->where($where)->update($data);
        return $result;
    }


    //删除栏目
    public function delColumn($where){
        return $this->where($where)->delete();
    }


    //权重表
    //保存权重
    public function saveWeight($param){
        $result = Db::table('scs_weight')->insert($param);
        $userId = Db::name('weight')->getLastInsID();
        $return['result'] = $result;
        $return['weightId'] = $userId;
        return $return;
    }

    //查询权重
    public function getWeight($where = ''){
        return Db::name('config')->where($where)->find();
    }

    //更新权重
    public function updateWeight($where,$data){
        $result = Db::name('config')->where($where)->update($data);
        return $result;
    }

    //栏目和广告关联表scs_column_adver
    public function saveColumnAd($param){
        return Db::name('column_adver')->insert($param);
    }

    //获取数据
    public function getColumnAd($where = ''){
        return Db::name('column_adver')->where($where)->select();
    }

    //更新栏目和广告关联表scs_column_adver数据
    public function updateColumnAd($where = '',$param){
        return Db::name('column_adver')->where($where)->update($param);
    }

    //删除栏目和广告关联表scs_column_adver数据
    public function delColumnAd($where){
        return Db::name('column_adver')->where($where)->delete();
    }
}