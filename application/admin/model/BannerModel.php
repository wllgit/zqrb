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

class BannerModel extends Model{
    protected $table = 'scs_banner';

    //保存数据
    public function saveBanner($param){
//        return $this->save($param);
        return Db::name('banner')->insert($param);
    }


    //查询单条数据
    public function getSingleBanners($where = [], $field = '*'){
        return $this->where($where)->field($field)->find();
    }


    //查询多条数据
    public function getBanners($where = '', $order = 'id desc'){
        return $this->where($where)->order($order)->select();
    }


    //删除banner
    public function delBanner($where){
        return $this->where($where)->delete();
    }

    //更新banner
    public function updateBane($where = '',$param = ''){
        return $this->where($where)->update($param);
    }


    //更新banner
    public function updateBanner($param,$id){
        $result = $this->save($param, ['id' => $id]);
        return $result;
    }

    public function updateBanners($param,$where = ''){
        return $this->where($where)->update($param);
    }


    //保存 Banner广告对应的位置 banner_adver表
    public function saveBannerAdver($param){
        return Db::name('banner_adver')->insert($param);
    }
    //获取banner广告对应的位置  banner_adver表
    public function getBannersAdver($where = '',$fields = '*'){
        return Db::name('banner_adver')->field($fields)->where($where)->select();
    }
    //更新banner_adver表
    public function updateBannersAdver($where = '',$param = ''){
        return Db::name('banner_adver')->where($where)->update($param);
    }


    //保存 Banner数量至scs_config 表
    public function saveBannerNum($param){
        return Db::name('config')->insert($param);
    }


    //更新banner配置    scs_config表
    public function updateBannersConf($param,$where = ''){
        return Db::name('config')->where($where)->update($param);
    }

    //获取banner配置  scs_config表
    public function getBannersConf($where = ''){
        return Db::name('config')->where($where)->field('*')->find();
    }

}