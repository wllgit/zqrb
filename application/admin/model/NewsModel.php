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

class NewsModel extends Model{
    protected $table = 'scs_news';

    //保存数据
    public function saveNews($param){
        $result = $this->save($param);
        $userId = Db::name('news')->getLastInsID();
        $return['result'] = $result;
        $return['newsId'] = $userId;
        return $return;
    }

    //查询数据
    public function getNews($where = '', $field= '*', $offset = '', $limit = ''){
        return $this->where($where)->alias('n')->join('scs_admin a','n.admin_id = a.id')->field($field)->limit($offset, $limit)->order('n.id desc')->paginate(10,false,['query' => request()->param()]);
    }
    //查询数据
    public function getNewsDetail($where = '', $field= '*', $offset = '', $limit = ''){
        return $this->where($where)->field($field)->limit($offset, $limit)->order('id desc')->paginate(10,false,['query' => request()->param()]);
    }

    //查询数据
    public function getNewsNoPage($where = '', $field= '*', $offset = '', $limit = ''){
        return $this->where($where)->field($field)->limit($offset, $limit)->order('id desc')->select();
    }

    //删除News
    public function delNews($where){
        return $this->where($where)->delete();
    }


    //更新
    public function updateNews($where,$param,$table){
        return db($table)->where($where)->update($param);
    }


    //新闻热点表
    public function getNewsHot($where = '', $field= '*', $order = 'id desc'){
        return Db::name('hot')->where($where)->field($field)->order($order)->select();
    }


}