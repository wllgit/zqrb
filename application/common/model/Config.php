<?php
namespace app\common\model;
use think\Model;
use think\DB;
/**
 * @ClassName:    Config 
 * @Description:  配置表 
 * @Created by:   [villager] 
 * @DateTime:     2018-05-28T14:15:10+0800
 */
Class Config extends Model 
{
	
    protected $start = 0; //查询起始位置
    protected $num = 10;   //查询条数
    protected $where; //查询条件
    protected $self_field = '*'; //查询字段
    protected $order = 'id desc'; //排序字段
	protected $autoWriteTimestamp = true;//自动写入时间戳
	use \app\common\traits\Head;
	/**
	 * [userAdd 新增配置]
	 * @AuthorHTL villager
	 * @DateTime  2018-05-29T14:52:09+0800
	 * @return    [type]                   [description]
	 */
    private function configAdd() {
    	$result = $this -> save($this->data);
    	return $result;
    }
    /**
     * [configList 配置列表]
     * @AuthorHTL villager
     * @DateTime  2018-06-01T16:08:08+0800
     * @return    [type]                   [description]
     */
    private function configInfo() {
        $list = $this -> get(function($query){
            $query -> where($this->where) -> field($this->self_field);
        });
        return $list;
    }
    /**
     * [configList 配置列表]
     * @AuthorHTL villager
     * @DateTime  2018-06-01T16:08:08+0800
     * @return    [type]                   [description]
     */
    private function configList() {
    	$list = $this -> all(function($query){
        	$query -> where($this->where) -> field($this->self_field) -> order($this->order);
        });
        return $list;
    }
    /**
     * [configUpdate 更新配置状态]
     * @AuthorHTL
     * @DateTime  2018-06-08T19:19:54+0800
     * @return    [type]                   [description]
     */
    private function configUpdate() {
        $result = $this -> where($this->where) -> update($this->data);
        return $result;
    }
}