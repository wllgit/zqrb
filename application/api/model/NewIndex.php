<?php
/**
 * Created by PhpStorm.
 * User: sogu
 * Date: 2018/6/7
 * Time: 下午2:15
 */

namespace app\api\model;

use think\Db;
use think\Model;

class NewIndex extends Model{
    /**
     * banner列表
     */
    public function bannerList(){

        $where['a.is_delete'] = 0;
        $where['a.is_show'] = 1;
        $where['b.is_delete'] = 0;
        $where['b.is_show'] = 1;

        //获取banner显示几张（含banner广告）
        $banner_num = Db::table('scs_config')->field('banner_num')->where('is_delete',0)->find();//print_r($banner_num);exit;

        //获取banner有几条广告
        $where2 = "a.posi_type = 2 AND a.circle_time_start < unix_timestamp() AND unix_timestamp() < a.circle_time_end";
        $adver_num = Db::table('scs_advertisements')
                    ->alias('a')
                    ->join('scs_advertisementpics b','a.id = b.ad_id')
                    ->field('a.id')
                    ->where($where)
                    ->where($where2)
                    ->count();

        //banner显示几张banner图片
        $num = $banner_num['banner_num'] - $adver_num;

        $where1['a.is_banner'] = 1;
        $where1['b.type'] = 0;

        //新闻表是主表，banner表存图片
        $bannerList = Db::table('scs_news')
                    ->alias('a')
                    ->join('scs_banner b','a.id = b.out_id')
                    ->field('a.id,b.out_id,b.url,b.picture_path,b.type,b.sort,a.title')
                    ->where($where)
                    ->where($where1)
                    ->order('b.sort')
                    ->limit($num)
                    ->select();

        if(isset($bannerList)){
            return $bannerList;
        }else{
            return false;
        }

    }
    /**
     * 广告列表
     * posi_type位置类型 1：起始页；4：新闻详情
     */
    public function adverList($request_info){

        //根据位置获取随机一条数据
        $posi_type = intval($request_info['posi_type']);

        $where = "is_show = 1 AND is_delete = 0 AND posi_type = $posi_type 
        AND circle_time_start < unix_timestamp() AND unix_timestamp() < circle_time_end";

        $res = Db::table('scs_advertisements')
            ->field('id,title,position,out_url,source_type,update_time,state')
            ->where($where)
            ->order('rand()')
            ->limit(1)
            ->select();

        if(isset($res) && $res){
            foreach ($res as &$v){
                $v['pic'] = array();
                //获取图片地址
                $v['pic'] = $this->adverPic($v['id'],$type=1);
                if($request_info['posi_type'] == 1){
                    $v['pic_time'] = 3;
                }
                //显示时间
                switch ($timediff = intval(time() - $v['update_time']))
                {
                    case intval($timediff / 86400) >= 1:
                        $v['time'] = date('Y-m-d',$v['update_time']);
                        break;
                    case intval($timediff / 3600) >= 1:
                        $v['time'] = intval($timediff / 3600).'小时前';
                        break;
                    case intval($timediff / 60) >= 1:
                        $v['time'] = intval($timediff / 60).'分钟前';
                        break;
                    case intval($timediff / 60) < 1:
                        $v['time'] = '刚刚';
                        break;
                }
            }
        }
        if(isset($res)){
            return $res;
        }else{
            return false;
        }

    }

    /**
     * 获取广告图片
     * $ad_id 广告id
     */
    public function adverPic($ad_id,$source_type){

        $where_pic['ad_id'] = $ad_id;
        $where_pic['is_show'] = 1;
        $where_pic['is_delete'] = 0;

        if($source_type == 1){
            $where_pic['type'] = 1;
        }else{
            $where_pic['type'] = 2;
        }

        $result = Db::table('scs_advertisementpics')->field('pic_path')->where($where_pic)->select();
        return $result;
    }
    /**
     * banner列广告
     */
    public function bannerAdver(){

        $res = Db::table('scs_banner_adver')->field('position,adver_id')->where('is_show',1)->select();
        return $res;
    }
    /**
     * 获取相关栏目的位置和是否随机和具体广告id
     * column_id 栏目id
     */
    public function adverNewsList($column_id){

        $res = Db::table('scs_column_adver')->field('column_id,position,adver_id')->where(['column_id'=>$column_id,'is_show'=>1])->select();
        return $res;
    }

    /**
     * 根据广告位获取广告
     * $parm['adver_id']广告id 如果是0随机取，其他的根据id查
     */
    public function findAdverNews($parm){

        $where['is_show'] = 1;
        $where['is_delete'] = 0;
        $where1 = 'circle_time_start < unix_timestamp() AND unix_timestamp() < circle_time_end';

        if($parm['adver_id'] == 0){
            $where['posi_type'] = $parm['posi_type'];
            $res = Db::table('scs_advertisements')
                ->field('id,title,out_url,source_type,update_time,state')
                ->where($where)->where($where1)->order('rand()')->find();
        }else{
            $where['id'] = $parm['adver_id'];
            $res = Db::table('scs_advertisements')
                ->field('id,title,out_url,source_type,update_time,state')
                ->where($where)->where($where1)->find();
        }
        if(isset($res)){
            $res['pic'] = $this->adverPic($res['id'],$type=1);
        }
        return $res;
    }
    /**
     * 广告详情
     * id广告id
     */
    public function advertisements($request_info){

        $where['id'] = intval($request_info['id']);
        $where['is_show'] = 1;
        $where['is_delete'] = 0;

        $res = Db::table('scs_advertisements')->where($where)->find();

        return $res;
    }
    /**
     * 用户收藏接口
     */
    public function collection($data){
        if(!isset($data)){
            return false;
        }

        $res = Db::table('scs_user_collection')->insert($data);

        if($res){
            return $res;
        }else{
            return false;
        }
    }
    /**
     *取消收藏
     */
    public function noCollection($requestion_info){
        if(!isset($requestion_info)){
            return false;
        }

        if($requestion_info['type'] == 0){
            $where['news_id'] = $requestion_info['id'];
        }elseif($requestion_info['type'] == 1){
            $where['flash_id'] = $requestion_info['id'];
        }
        $where['user_id'] = $requestion_info['user_id'];

        $data['status'] = $requestion_info['isCollection'];
        $data['update_time'] = time();

        $res = Db::table('scs_user_collection')->where($where)->update($data);

        if($res){
            return $res;
        }else{
            return false;
        }
    }
    /**
     * 查看是否收藏
     */
    public function isCollection($data){
        if(!isset($data)){
            return false;
        }
        if($data['type'] == 0 && isset($data['news_id'])){
            $where['news_id'] = $data['news_id'];
        }elseif($data['type'] == 1 && isset($data['flash_id'])){
            $where['flash_id'] = $data['flash_id'];
        }
        $where['user_id'] = $data['user_id'];


        $isCollection = Db::table('scs_user_collection')->where($where)->find();

        if($isCollection){
            return $isCollection;
        }else{
            return false;
        }
    }
    /**
     * 收藏列表
     * $user_id 用户ID
     * $page
     * $pageSize
     */
    public function collectionList($user_id,$page,$pageSize){
        $where['user_id'] = $user_id;
        $where['status'] = 1;
        $where['is_delete'] = 0;
        $res = Db::table('scs_user_collection')
            ->field('id,news_id,type,flash_id,status')
            ->where($where)
            ->order('update_time desc')
            ->limit((($page - 1) * $pageSize),$pageSize)
            ->select();
        if(isset($res)){
            return $res;
        }else{
            return false;
        }
    }
    /**
     * 根据新闻id获取标签内容
     */
    public function columnDetail($data){
        $res = Db::table('scs_news')->field('column_ids')->where("id=$data")->find();

        $result = Db::table('scs_column')->field('title')->where('id','in',$res['column_ids'])->limit(2)->select();

        return $result;
    }
    /**
     * 收藏列表新闻，快讯详情
     */
    public function collectionListDetail($id,$type){
        $where['is_show'] = 1;
        $where['is_delete'] = 0;
        if($type == 0){
            $res = Db::table('scs_news')->field('id,title,detail,summary,update_time,source_type,detail_type')->where($where)->where('id','in',$id)->find();
        }elseif ($type == 1){
            $res = Db::table('scs_news_flash')->field('id,type,title,summary,update_time')->where($where)->where('id','in',$id)->select();
        }
        if(isset($res)){
            return $res;
        }else{
            return false;
        }

    }
    /**
     * 增加/减少新闻收藏量
     */
    public function addCollection($request_info){

        if($request_info['status'] == 0){
            Db::table('scs_news')->where('id',$request_info['id'])->setInc('collect_num');
        }elseif ($request_info['status'] == 1){
            Db::table('scs_news')->where('id',$request_info['id'])->setDec('collect_num');
        }

    }
    /**
     * 快讯接口
     */
    public function newsFlash($page,$pageSize){

        $res = Db::table('scs_news_flash')
            ->where('is_delete=0')
            ->order('create_time desc')
            ->limit((($page - 1) * $pageSize),$pageSize)
            ->select();

        if($res){
            return $res;
        }else{
            return false;
        }
    }

    /**
     * @param $data 日期Y-m-d
     * @return false|\PDOStatement|string|\think\Collection
     */
    public function eNewsPaper($data)
    {
        $res = Db::table('scs_news_paper')->field('head,date,pic_url')->where('date',$data)->order('id')->group('head')->select();

        if(isset($res)){
            return $res;
        }else{
            false;
        }
    }
    /**
     * 电子报标题
     */
    public function ePaperDetail($data)
    {
        $where['head'] = $data['head'];
        $where['date'] = $data['date'];
        $where['is_show'] = 1;
        $res = Db::table('scs_news_paper')->where($where)->select();

        if(isset($res)){
            return $res;
        }else{
            false;
        }
    }



































}











