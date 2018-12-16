<?php
namespace app\common\model;
use think\Model;
use think\Request;
/**
 * @ClassName:    Name 
 * @Description:  Detail 
 * @Created by:   [villager] 
 * @DateTime:     2018-05-28T14:15:10+0800
 */
Class CommentReply extends Model 
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
     * [getPublishTimeAttr 发布时间获取器]
     * @AuthorHTL village
     * @DateTime  2018-06-07T15:16:26+0800
     * @return    [type]                   [description]
     */
    public function getCreateTimeAttr($value) {
        $interval = time() - $value; //新闻发布时间与当前时间的时间差
        if($interval / 86400 > 1) {  //如果发布时间与当前时间相差大于一天则返回完整时间格式
            return date('Y-m-d H:i:s',$value);
        }else if(1 <= $hour = intval($interval / 3600)){ //如果发布时间与当前时间相差小于一天大于一小时则返回相差小时数
            return $hour . '小时前';
        }else if(1 <= $second = intval($interval / 60)) { //如果发布时间与当前时间相差小于一小时大于一分钟则返回相差分钟数
            return $second . '分钟前';
        }else {
            return '刚刚'; //如果发布时间与当前时间相差小于一分钟则返回刚刚
        }
    }
    /**
     * [userInfo 联查回复用户]
     * @AuthorHTL
     * @DateTime  2018-06-11T20:22:58+0800
     * @return    [type]                   [description]
     */
    public function userInfo() {
        $field = 'id,nickname,avatar';
        $userInfo = $this->hasMany('User','id','user_id') -> field($field);
        return $userInfo;
    }
    /**
     * [commentedUserInfo 联查被评论的用户]
     * @AuthorHTL
     * @DateTime  2018-06-11T20:22:58+0800
     * @return    [type]                   [description]
     */
    public function commentedUserInfo() {
        $field = 'id,nickname,avatar';
        $userInfo = $this->hasMany('User','id','commented_user_id') -> field($field);
        return $userInfo;
    }
    /**
     * [commentedReply 联查上级回复]
     * @AuthorHTL
     * @DateTime  2018-06-11T20:22:58+0800
     * @return    [type]                   [description]
     */
    public function commentedReply() {
        $field = 'id,reply';
        $userInfo = $this->hasMany('CommentReply','id','parent_id') -> field($field);
        return $userInfo;
    }
    /**
     * [newsInfo 新闻详情]
     * @AuthorHTL
     * @DateTime  2018-06-21T14:32:40+0800
     * @return    [type]                   [description]
     */
    public function newsInfo() {
        $field = 'id,title,column_ids,source_type';
        $where = ['is_delete' => 0,'is_show' =>1];
        return $this->hasMany('News','id','news_id')-> where($where) -> field($field) -> with('newsSource');
    }
	/**
	 * [userAdd 添加回复]
	 * @AuthorHTL villager
	 * @DateTime  2018-05-29T14:52:09+0800
	 * @return    [type]                   [description]
	 */
    private function commentReplyAdd() {
    	$result = $this -> save($this->data);
    	return $result;
    }
    /**
     * [userInfo 回复信息]
     * @AuthorHTL villager
     * @DateTime  2018-05-29T16:57:46+0800
     * @return    [type]                   [description]
     */
    private function commentReplyInfo() {
    	$info = $this -> get(function($query){
            $query -> where($this->where) -> field($this->self_field);
        });
        return $info;
    }
    /**
     * [commentReplyList 回复列表]
     * @AuthorHTL villager
     * @DateTime  2018-06-01T16:08:08+0800
     * @return    [type]                   [description]
     */
    private function commentReplyList() {
    	$list = $this -> all(function($query){
            $query -> where($this->where) -> field($this->self_field) -> order($this->order) -> limit($this->start,$this->offset) -> with('userInfo,commentedUserInfo,commentedReply');
        });
        return $list;
    }
    /**
     * [commentReplyList 回复列表]
     * @AuthorHTL villager
     * @DateTime  2018-06-01T16:08:08+0800
     * @return    [type]                   [description]
     */
    private function messageList() {
        $list = $this -> all(function($query){
            $query -> where($this->where) -> field($this->self_field) -> order($this->order) -> limit($this->start,$this->offset) -> with('userInfo,newsInfo');
        });
        return $list;
    }
    /**
     * [count 计算条数]
     * @AuthorHTL
     * @DateTime  2018-06-20T18:49:42+0800
     * @return    [type]                   [description]
     */
    private function count() {
        $number = $this -> where($this->where) -> count();
        return $number;
    }
    /**
     * [commentReplyUpdate 更新回复]
     * @AuthorHTL
     * @DateTime  2018-06-21T15:12:15+0800
     * @return    [type]                   [description]
     */
    private function commentReplyUpdate() {
        $result = $this -> where($this->where) -> update($this->data);
        return $result;
    }
}