<?php
namespace app\common\model;
use think\Model;
/**
 * @ClassName:    Hot 
 * @Description:  Detail 
 * @Created by:   [villager] 
 * @DateTime:     2018-05-28T14:15:10+0800
 */
Class Hot extends Model 
{
	
    protected $start = 0; //查询起始位置
    protected $offset = 100;   //查询条数
    protected $where; //查询条件
    protected $whereOr = false; //查询条件
    protected $self_field = '*'; //查询字段
    protected $order = 'id desc'; //排序字段
	protected $autoWriteTimestamp = true;//自动写入时间戳
	use \app\common\traits\Head;
	/**
	 * [userAdd 添加热点]
	 * @AuthorHTL villager
	 * @DateTime  2018-05-29T14:52:09+0800
	 * @return    [type]                   [description]
	 */
    private function hotAdd() {
    	$result = $this -> save($this->data);
    	return $result;
    }
    /**
     * [userInfo 热点信息]
     * @AuthorHTL villager
     * @DateTime  2018-05-29T16:57:46+0800
     * @return    [type]                   [description]
     */
    private function hotInfo() {
    	$info = $this -> get(function($query){
            $query -> where($this->where) -> field($this->self_field);
        });
        return $info;
    }
    /**
     * [hotList 热点列表]
     * @AuthorHTL villager
     * @DateTime  2018-06-01T16:08:08+0800
     * @return    [type]                   [description]
     */
    private function hotList() {
    	$list = $this -> all(function($query){
            $query -> where($this->where) -> field($this->self_field) -> order($this->order) -> limit($this->start,$this->offset);
        });
        return $list;
    }
}