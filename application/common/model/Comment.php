<?php
namespace app\common\model;
use think\Model;
use think\Request;
use app\common\model\User;
/**
 * @ClassName:    Name 
 * @Description:  Detail 
 * @Created by:   [villager] 
 * @DateTime:     2018-05-28T14:15:10+0800
 */
Class Comment extends Model 
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
     * [userInfo 联查评论用户]
     * @AuthorHTL
     * @DateTime  2018-06-11T20:22:58+0800
     * @return    [type]                   [description]
     */
    public function oneUserInfo() {
        $field = 'id as user_id,nickname,avatar';
        $userInfo = $this->hasOne('User','id','user_id') -> field($field);
        return $userInfo;
    }
    /**
     * [userInfo 联查评论用户]
     * @AuthorHTL
     * @DateTime  2018-06-11T20:22:58+0800
     * @return    [type]                   [description]
     */
    public function userInfo() {
        $field = 'id,nickname,avatar';
        $userInfo = $this->hasMany('User','id','user_id','left') -> field($field);
        return $userInfo;
    }
    /**
     * [commentReply 评论回复表]
     * @AuthorHTL
     * @DateTime  2018-06-12T16:53:02+0800
     * @return    [type]                   [description]
     */
    public function commentReply() {
        $where = ['is_delete' => 0,'is_show'=>1];
        $field = 'id as reply_id,comment_id,user_id,reply';
        return $this->hasMany('CommentReply','comment_id','id','left') -> field($field) -> where($where) -> with('userInfo');
    }
    /**
     * [commentPraise 联查用户点赞]
     * @AuthorHTL
     * @DateTime  2018-06-12T10:28:26+0800
     * @return    [type]                   [description]
     */
    public function commentPraise() {
        $header = Request::instance() -> header();
        $uid = isset($header['uid']) ? $header['uid'] : 0;
        $where = ['user_id' => $uid,'status' => 1,'is_delete' => 0];
        return $this->hasMany('UserCommentPraise','comment_id','id','left') -> where($where) -> field('id,user_id,comment_id');
    }
    /**
     * [commentInfoPraise 联查用户点赞]
     * @AuthorHTL
     * @DateTime  2018-06-12T10:28:26+0800
     * @return    [type]                   [description]
     */
    public function commentInfoPraise() {
        $header = Request::instance() -> header();
        $uid = isset($header['uid']) ? $header['uid'] : 0;
        $where = ['user_id' => $uid,'status' => 1,'is_delete' => 0];
        return $this->hasMany('UserCommentPraise','comment_id','comment_id') -> where($where) -> field('id,user_id,comment_id');
    }
	/**
	 * [userAdd 添加评论]
	 * @AuthorHTL villager
	 * @DateTime  2018-05-29T14:52:09+0800
	 * @return    [type]                   [description]
	 */
    private function commentAdd() {
    	$result = $this -> save($this->data);
    	return $result;
    }
    /**
     * [userInfo 评论信息]
     * @AuthorHTL villager
     * @DateTime  2018-05-29T16:57:46+0800
     * @return    [type]                   [description]
     */
    private function commentInfo() {
    	$info = $this -> get(function($query){
            $query -> where($this->where) -> field($this->self_field);
        });
        return $info;
    }
    /**
     * [commentList 评论列表]
     * @AuthorHTL villager
     * @DateTime  2018-06-01T16:08:08+0800
     * @return    [type]                   [description]
     */
    private function commentList() {
    	$list = $this -> all(function($query){
            $query -> where($this->where) -> field($this->self_field) -> order($this->order) -> limit($this->start,$this->offset) -> with('commentPraise,commentReply');
        });
        return $list;
    }
    /**
     * [increase 自增处理]
     * @AuthorHTL
     * @DateTime  2018-06-08T19:56:23+0800
     * @return    [type]                   [description]
     */
    private function increase() {
        $info = $this -> where($this->where) -> setInc($this->self_field);
        return $info;
    }
    /**
     * [decrease 自减处理]
     * @AuthorHTL
     * @DateTime  2018-06-08T19:56:48+0800
     * @return    [type]                   [description]
     */
    private function decrease() {
        $info = $this -> where($this->where) -> setDec($this->self_field);
        return $info;
    }
    /**
     * [count 计算数量]
     * @AuthorHTL
     * @DateTime  2018-06-08T19:56:48+0800
     * @return    [type]                   [description]
     */
    private function count() {
        $info = $this -> where($this->where) -> count();
        return $info;
    }
}