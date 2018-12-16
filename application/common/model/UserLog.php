<?php
namespace app\common\model;
use think\Model;
/**
 * @ClassName:    Name 
 * @Description:  Detail 
 * @Created by:   [villager] 
 * @DateTime:     2018-05-28T14:15:10+0800
 */
Class UserLog extends Model 
{
	
    protected $start = 0; //查询起始位置
    protected $num = 10;   //查询条数
    protected $where; //查询条件
    protected $self_field = '*'; //查询字段
    protected $order = 'id desc'; //排序字段
	protected $autoWriteTimestamp = true;//自动写入时间戳
	use \app\common\traits\Head;
	/**
	 * [userLogAdd 添加用户访问]
	 * @AuthorHTL villager
	 * @DateTime  2018-05-29T14:52:09+0800
	 * @return    [type]                   [description]
	 */
    private function userLogAdd() {
    	$result = $this -> save($this->data);
    	return $result;
    }
    /**
     * [userInfo 用户信息]
     * @AuthorHTL villager
     * @DateTime  2018-05-29T16:57:46+0800
     * @return    [type]                   [description]
     */
    private function userLogAddInfo() {
    	$info = $this -> get(function($query){
            $query -> where($this->where) -> field($this->self_field) -> with('lastMessage');
        });
        return $info;
    }
    /**
     * [userInfo 用户信息]
     * @AuthorHTL villager
     * @DateTime  2018-05-29T16:57:46+0800
     * @return    [type]                   [description]
     */
    private function userLogAddList() {
        $info = $this -> all(function($query){
            $query -> where($this->where) -> field($this->self_field);
        });
        return $info;
    }
    /**
     * [userUpdate 用户更新]
     * @AuthorHTL villager
     * @DateTime  2018-06-05T14:38:44+0800
     * @return    [type]                   [description]
     */
    private function userLogAddUpdate() {
        $result = $this -> where($this->where) -> update($this->data);
        return $result;
    }
}