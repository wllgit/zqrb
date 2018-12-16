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

class NewsSource extends Model{
    protected $table = 'scs_news_source';

    //新闻资源关联表
    public function saveNewsSource($param){
        return $this->save($param);
    }

    //
    public function getNewsSource($where = '', $field = '*', $order = 'id desc'){
        return $this->where($where)->field($field)->order($order)->select();
    }

    //保存广告资源信息
    public function insertSource($param){
        return Db::table('scs_news_source')->insert($param);
//        return $this->save($param);
    }

    //保存多条数据
    public function moreInsertSource($data){
        $result = Db::name('news_source')->insertAll($data);
        return $result;
    }


    //更新
    public function updateNewsSource($where,$param,$table){
        return db($table)->where($where)->update($param);
    }

    //更新多条
    public function updateAll($list){
//        $list = [
//            ['id'=>1, 'name'=>'thinkphp', 'email'=>'thinkphp@qq.com'],
//            ['id'=>2, 'name'=>'onethink', 'email'=>'onethink@qq.com']
//        ];
        $this->saveAll($list);
    }

    //删除新闻资源
    public function delSource($where){
        return $this->where($where)->delete();
    }


}