<?php
namespace app\common\logic;
use app\common\logic\Base;
use app\common\model\Comment;
use app\common\model\CommentReply as CommentReplyModel;
/**
 * @ClassName:    CommentReply 
 * @Description:  回复类 
 * @Created by:   [villager] 
 * @DateTime:     2018-05-28T14:13:06+0800
 */
Class CommentReply extends Base 
{
	protected $page = 1;
	protected $offset = 5;
	protected $field = '';
    protected $commentReplyList = [];
	/**
	 * [initialize:  初始化绑定news模型] 
	 * @Created by:   [villager] 
	 * @DateTime:     2018-05-16T15:16:16+0800
	 */
	protected function initialize() {
        parent::initialize();
        $this->model = new CommentReplyModel;
    }
    /**
     * [commentReplyList 回复列表]
     * @AuthorHTL villager
     * @DateTime  2018-06-01T15:47:53+0800
     * @param     [array]                   $param [传参]
     * @return    [array]                          [回复列表]
     */
    public function commentReplyList($param)
    {
        if(!isset($param['comment_id'])) apiSend(['code'=>LACK_CODE,'msg'=>LACK_PARAM,'status'=>FAIL_STATUS],'json',true); //评论id
        $commentModel = new Comment();
        $comment['where'] = ['id' => $param['comment_id']];
        $comment['field'] = 'id as comment_id,comment,praise_num,create_time,user_id';
        $comment_info = $commentModel->index('commentInfo',$comment);
        $comment_detail = $comment_info;
        $comment_info->oneUserInfo;
        $comment_info->commentInfoPraise;
        $comment_info['userInfo'] = $comment_info['oneUserInfo'];
        $comment_info['is_praise'] = empty($comment_info['commentInfoPraise']) ? 0 : 1; //用户是否对该评论点赞
        unset($comment_info['oneUserInfo']);
        unset($comment_info['commentInfoPraise']);
        //$parent_id = isset($param['reply_id']) ? $param['reply_id'] : 0; // 父级回复id; 0 代表顶级id
        $offset = isset($param['offset']) ? $param['offset'] : $this->offset; // 偏移量
        $start  = ((isset($param['page']) ? $param['page']   : $this->page) - 1) * $offset;
    	$where = ['is_delete' => 0,'comment_id' => $param['comment_id'],'is_show' => 1]; //查询条件
        if(isset($param['min_reply_id'])) { // 当前页最小的回复id ,防止数据重复
            $min_reply_id = $param['min_reply_id'];
            $where['id'] = ['lt',"$min_reply_id"];
        }
    	$field = 'id as reply_id,reply,create_time,user_id,commented_user_id,parent_id'; // 查询字段
    	$query = compact('where','field');
    	$list = $this->model->index('commentReplyList',$query);
        $reply_list = [];
        if(!empty($list)) { //如果list不为空 重组数据
            foreach ($list as $key => $value) {
                $commented_user_info = null;
                $user_info = ['user_id'=>$value['userInfo'][0]['id'],'nickname'=>$value['userInfo'][0]['nickname'],'avatar'=>$value['userInfo'][0]['avatar']];
                if($value['parent_id'] != 0) {//如果是顶级回复，则不需要顶级评论的信息
                    $commented_user_info = ['user_id'=>$value['commentedUserInfo'][0]['id'],'nickname'=>$value['commentedUserInfo'][0]['nickname'],'avatar'=>$value['commentedUserInfo'][0]['avatar']];
                }
                if(!empty($value['commentedReply'])) {
                    $commented_user_info['reply'] = $value['commentedReply'][0]['reply'];
                }
                $this->commentReplyList[] = [
                    'comment_id' => $value['reply_id'],
                    'comment' => $value['reply'],
                    'create_time' => $value['create_time'],
                    'parent_id' => $value['parent_id'],
                    'userInfo' => $user_info,
                    'commentedUserInfo' => $commented_user_info
                ];
            }
        }
        $data['comment'] = $comment_info;
        $data['reply'] = $this->commentReplyList;
        return $data;
    }
    /**
     * [commentReplyAdd 新增回复]
     * @AuthorHTL villager
     * @DateTime  2018-06-01T16:14:54+0800
     * @return    [type]                   [description]
     */
	public function commentReplyAdd($param)
	{
		if(!isset($param['comment_id'])) apiSend(['code'=>LACK_CODE,'msg'=>LACK_PARAM,'status'=>FAIL_STATUS],'json',true); //评论id
        if(!isset($param['reply'])) apiSend(['code'=>LACK_CODE,'msg'=>LACK_PARAM,'status'=>FAIL_STATUS],'json',true); //评论回复
        if(!isset($param['commenteduserid'])) apiSend(['code'=>LACK_CODE,'msg'=>LACK_PARAM,'status'=>FAIL_STATUS],'json',true); //评论回复
        $parent_id = isset($param['reply_id']) ? $param['reply_id'] : 0; // 父级回复id; 0 代表顶级id
        $comment_id = $param['comment_id']; //评论id
		$reply = $param['reply']; //回复内容
        $user_id = $this->header['uid'];//回复人id
        $commented_user_id = $param['commenteduserid'];//被回复人id
        $is_read = 0;
        if($user_id == $commented_user_id) {
            $is_read = 1;
        }
        //获取新闻id
        $commentModel = new Comment();
        $comment_query['where'] = ['id'=>$comment_id];
        $comment_query['field'] = 'id,news_id';
        $comment_info = $commentModel->index('commentInfo',$comment_query);
        $news_id = $comment_info['news_id'];
		$query['data'] = compact('comment_id','reply','user_id','parent_id','commented_user_id','news_id','is_read');
		$replyResult = $this->model->index('commentReplyAdd',$query); // 新增回复结果
        if($replyResult) {  // 如果回复新增成功后，评论回复数+1
            $query['where'] = ['id' => $comment_id];
            $query['field'] = 'reply_num';
            $commentResult = $commentModel->index('increase',$query);
        }
        $result['status'] = false;
        if($replyResult && $commentResult) {
            $result['status'] = true;
        }
		$result['data']['id'] = $this->model->id; // 回复id
		return $result;
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