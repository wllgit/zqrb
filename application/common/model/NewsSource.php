<?php
namespace app\common\model;
use think\Model;
/**
 * @ClassName:    Name 
 * @Description:  Detail 
 * @Created by:   [villager] 
 * @DateTime:     2018-05-28T14:15:10+0800
 */
Class NewsSource extends Model 
{
	protected $start = 0; //查询起始位置
    protected $num = 10;   //查询条数
    protected $where; //查询条件
    protected $self_field = '*'; //查询字段
    protected $order = 'id desc'; //排序字段
	use \app\common\traits\Head;
	/**
	 * [sourceInfo 资源详情]
	 * @AuthorHTL villager
	 * @DateTime  2018-06-08T16:17:30+0800
	 * @return    [type]                   [description]
	 */
	private function sourceInfo() {
		 $info = $this -> get(function($query){
            $query -> where($this->where) -> field($this->self_field);
        });
        return $info;
	}
	/**
	 * [sourceAdd 添加资源]
	 * @AuthorHTL
	 * @DateTime  2018-07-04T18:36:18+0800
	 * @return    [type]                   [description]
	 */
	private function sourceAdd() {
		$result = $this -> saveAll($this->data);
        return $result;
	}
}