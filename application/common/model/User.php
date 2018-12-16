<?php
namespace app\common\model;
use think\Model;
/**
 * @ClassName:    Name 
 * @Description:  Detail 
 * @Created by:   [villager] 
 * @DateTime:     2018-05-28T14:15:10+0800
 */
Class User extends Model 
{
	
    protected $start = 0; //查询起始位置
    protected $num = 10;   //查询条数
    protected $where; //查询条件
    protected $self_field = '*'; //查询字段
    protected $order = 'id desc'; //排序字段
	protected $autoWriteTimestamp = true;//自动写入时间戳
	use \app\common\traits\Head;
    /**
     * [getSexAttr 获取性别并转换]
     * @AuthorHTL
     * @DateTime  2018-06-04T11:33:35+0800
     * @param     [type]                   $value [description]
     * @return    [type]                          [description]
     */
    public function getSexAttr($value) {
        $sex = [0 => '未知',1 => '男',2 => '女'];
        return $sex[$value];
    }
    /**
     * [setSexAttr 性别转话存储方式]
     * @AuthorHTL
     * @DateTime  2018-06-04T11:38:28+0800
     * @param     [type]                   $value [description]
     */
    public function setSexAttr($value) {
        if($value == '男') {
            $sex = 1;
        }else if($value == '女') {
            $sex = 2;
        }else {
            $sex = 0;
        }
        return $sex;
    }
    /**
     * [lastMessage 最新消息]
     * @AuthorHTL
     * @DateTime  2018-06-20T18:21:03+0800
     * @return    [type]                   [description]
     */
    public function lastMessage() {
        $where = ['is_read' => 0,'is_delete' => 0];
        $field = 'id,user_id,commented_user_id';
        return $this->hasMany('CommentReply','commented_user_id','user_id') -> where($where) -> field($field) -> order('id desc') -> limit(1) ;
    }
	/**
	 * [userAdd 添加用户]
	 * @AuthorHTL villager
	 * @DateTime  2018-05-29T14:52:09+0800
	 * @return    [type]                   [description]
	 */
    private function userAdd() {
    	$result = $this -> save($this->data);
    	return $result;
    }
    /**
     * [userInfo 用户信息]
     * @AuthorHTL villager
     * @DateTime  2018-05-29T16:57:46+0800
     * @return    [type]                   [description]
     */
    private function userInfo() {
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
    private function userList() {
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
    private function userUpdate() {
        $result = $this -> where($this->where) -> update($this->data);
        return $result;
    }
}