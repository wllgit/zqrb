<?php
namespace app\common\model;
use think\Model;
/**
 * @ClassName:    Column 
 * @Description:  Detail 
 * @Created by:   [villager] 
 * @DateTime:     2018-05-28T14:15:10+0800
 */
Class Column extends Model 
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
	 * [userAdd 添加栏目]
	 * @AuthorHTL villager
	 * @DateTime  2018-05-29T14:52:09+0800
	 * @return    [type]                   [description]
	 */
    private function columnAdd() {
    	$result = $this -> save($this->data);
    	return $result;
    }
    /**
     * [userInfo 栏目信息]
     * @AuthorHTL villager
     * @DateTime  2018-05-29T16:57:46+0800
     * @return    [type]                   [description]
     */
    private function columnInfo() {
    	$info = $this -> get(function($query){
            $query -> where($this->where) -> field($this->self_field);
        });
        return $info;
    }
    /**
     * [columnList 栏目列表]
     * @AuthorHTL villager
     * @DateTime  2018-06-01T16:08:08+0800
     * @return    [type]                   [description]
     */
    private function columnList() {
    	$list = $this -> all(function($query){
            if($this->whereOr){
               $query -> where($this->where) -> where(function($q) {$q->whereOr($this->whereOr);}) -> field($this->self_field) -> order($this->order) -> limit($this->start,$this->offset);
            }else{
               $query -> where($this->where) -> field($this->self_field) -> order($this->order) -> limit($this->start,$this->offset);
            }
        });
        return $list;
    }
}