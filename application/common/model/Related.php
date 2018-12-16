<?php
namespace app\common\model;
use think\Model;
use app\common\model\News;
/**
 * @ClassName:    Name 
 * @Description:  Detail 
 * @Created by:   [villager] 
 * @DateTime:     2018-05-28T14:15:10+0800
 */
Class Related extends Model 
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
	 * [relatedAdd 添加相关新闻]
	 * @AuthorHTL villager
	 * @DateTime  2018-05-29T14:52:09+0800
	 * @return    [type]                   [description]
	 */
    private function relatedAdd() {
    	$result = $this -> save($this->data);
    	return $result;
    }
    /**
     * [relatedInfo 相关新闻信息]
     * @AuthorHTL villager
     * @DateTime  2018-05-29T16:57:46+0800
     * @return    [type]                   [description]
     */
    private function relatedInfo() {
    	$info = $this -> get(function($query){
            $query -> where($this->where) -> field($this->self_field);
        });
        return $info;
    }
    /**
     * [relatedList 相关新闻列表]
     * @AuthorHTL villager
     * @DateTime  2018-06-01T16:08:08+0800
     * @return    [type]                   [description]
     */
    private function relatedList() {
    	$list = $this -> all(function($query){
            $query -> where($this->where) -> field($this->self_field) -> order($this->order) -> limit($this->start,$this->offset);
        });
        return $list;
    }
}