<?php
namespace app\common\model;
use think\Db;
use think\Model;
use think\Request;
use app\common\model\User;
/**
 * @ClassName:    Name 
 * @Description:  Detail 
 * @Created by:   [villager] 
 * @DateTime:     2018-05-28T14:15:10+0800
 */
Class UserStock extends Model 
{
	
    protected $start = 0; //查询起始位置
    protected $offset = 20;   //查询条数
    protected $where; //查询条件
    protected $whereOr = false; //查询条件
    protected $self_field = '*'; //查询字段
    protected $order = 'id desc'; //排序字段
	protected $autoWriteTimestamp = true;//自动写入时间戳
	use \app\common\traits\Head;
	/**
	 * [userStockUpdate 更新用户自选股]
	 * @AuthorHTL villager
	 * @DateTime  2018-05-29T14:52:09+0800
	 * @return    [type]                   [description]
	 */
    private function userStockUpdate() {
    	$sql = "replace into scs_user_stock(user_id,user_code,code,is_top,create_time,update_time) VALUES ".$this->data;
        $result = Db::execute($sql);
    	return $result;
    }
    /**
     * [userStockAdd 添加自选股]
     * @AuthorHTL
     * @DateTime  2018-06-27T15:22:48+0800
     * @return    [type]                   [description]
     */
    private function userStockAdd() {
        $result = $this -> save($this->data);
        return $result;
    }
    /**
     * [userStockAddList 批量添加自选股]
     * @AuthorHTL
     * @DateTime  2018-06-27T15:22:48+0800
     * @return    [type]                   [description]
     */
    private function userStockAddList() {
        $result = $this -> saveAll($this->data);
        return $result;
    }
    /**
     * [userStockInfo 用户自选股信息]
     * @AuthorHTL villager
     * @DateTime  2018-05-29T16:57:46+0800
     * @return    [type]                   [description]
     */
    private function userStockInfo() {
    	$info = $this -> get(function($query){
            $query -> where($this->where) -> field($this->self_field);
        });
        return $info;
    }
    /**
     * [userStockList 用户自选股列表]
     * @AuthorHTL villager
     * @DateTime  2018-06-01T16:08:08+0800
     * @return    [type]                   [description]
     */
    private function userStockList() {
    	$list = $this -> all(function($query){
            $query -> where($this->where) -> field($this->self_field) -> order($this->order) -> limit($this->start,$this->offset);
        });
        return $list;
    }
    /**
     * [userStockDelete 软删除-用户自选股]
     * @AuthorHTL
     * @DateTime  2018-06-15T14:38:16+0800
     * @return    [type]                   [description]
     */
    private function userStockDelete() {
        $result = $this -> where($this->where) -> delete();
        return $result;
    }
}