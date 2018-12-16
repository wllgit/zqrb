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

class Comment extends Model{
    protected $table = 'scs_comment';

    //保存数据
    public function saveAdvertise($param){
        $result = $this->save($param);
        $userId = Db::name('news')->getLastInsID();
        $return['result'] = $result;
        $return['adId'] = $userId;
        return $return;
    }

    public function getReplyCom($where,$order){
        $result = Db::name('comment_reply')->field('user_id,reply,create_time,id')
            ->where($where)
            ->order($order)
            ->paginate(10,false,['query' => request()->param()]);

        return $result;
    }

    public function user(){
        return $this->hasOne('User','id')->field('nickname');
    }


    //查询数据
    public function getComment($where = '', $field= '*',$order = 'id desc', $offset = '', $limit = ''){
        return $this->where($where)->field($field)->limit($offset, $limit)->order($order)->paginate(10,false,['query' => request()->param()]);
    }

    //查询数据
    public function getAdvertiseNoPage($where = '', $field= '*',$order = 'id desc', $offset = '', $limit = ''){
        return $this->where($where)->field($field)->limit($offset, $limit)->order($order)->select();
    }


    //删除广告
    public function delAdvertise($where){
        return $this->where($where)->delete();
    }


    //更新
    public function updateNews($where,$param,$table){
        return db($table)->where($where)->update($param);
    }


    //保存广告资源信息
    public function saveAdverSource($param){
        return Db::table('scs_advertisementpics')->insert($param);
    }

    //查询广告资源
    public function getSource($where = ''){
        return Db::table('scs_advertisementpics')->where($where)->select();
    }

    //更新广告资源数据
    public function updateSource($where,$param){
        return Db::table('scs_advertisementpics')->where($where)->update($param);
    }

    //删除广告资源数据
    public function delSource($where){
        return Db::table('scs_advertisementpics')->where($where)->delete();
    }


}