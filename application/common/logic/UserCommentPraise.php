<?php
namespace app\common\logic;
use think\Cache;
use app\common\logic\Base;
use app\common\model\Comment;
use app\common\model\UserCommentPraise as UserCommentPraiseModel;
/**
 * @ClassName:    News 
 * @Description:  新闻类 
 * @Created by:   [villager] 
 * @DateTime:     2018-05-28T14:13:06+0800
 */
Class UserCommentPraise extends Base 
{
	protected $page = 1;
	protected $offset = 5;
	protected $field = '';
	/**
	 * [initialize:  初始化绑定news模型] 
	 * @Created by:   [villager] 
	 * @DateTime:     2018-05-16T15:16:16+0800
	 */
	protected function initialize() {
        parent::initialize();
        $this->model = new UserCommentPraiseModel();
    }
    /**
     * [praiseAdd 点赞或取消赞]
     * @AuthorHTL villager
     * @DateTime  2018-06-01T16:14:54+0800
     * @return    [type]                   [description]
     */
	public function commentPraiseUpdate($param)
	{
        $commentModel = new Comment();
        $status = $param['status'] == 0 ? 1: 0; //用户点赞状态 1 取消赞 0 赞; 数据库状态 0 取消赞 1 赞
        if(!isset($this->header['uid']) || empty($this->header['uid'])) apiSend(['code'=>NOT_LOGIN,'msg'=>NOT_LOGIN_MSG,'status'=>FAIL_STATUS],'json',true); //没有uid，非法访问;//token失效，重新登录
        $user_id = $this->header['uid']; // 用户id
        $comment_id = $param['comment_id'];  // 新闻id
        $where = ['user_id' => $user_id,'comment_id' => $comment_id,'is_delete' => 0];
        $field = 'id,user_id,comment_id';
        $query = compact('where','field');
        $list = $this->model->index('commentPraiseList',$query);
        $commentPraiseResult = false;
        if(empty($list) && $status == 1) {     //首次点赞新增数据
            $data = compact('status','user_id','comment_id');
            $query['data'] = $data;
            $commentPraiseResult = $this->model->index('commentPraiseAdd',$query);
        }else if(!empty($list)){                //更新点赞状态
            $where = ['user_id' => $user_id,'comment_id' => $comment_id,'is_delete' => 0,'status' => $status];
            $field = 'id,user_id,comment_id';
            $query = compact('where','field');
            $info = $this->model->index('commentPraiseDetail',$query);
            if(!empty($info)){ // 不能连续点赞或取消赞
                $result['status'] = false;
                return $result;
            }
            $where = ['user_id' => $user_id,'comment_id' => $comment_id,'is_delete' => 0];
            $update_time  = time();
            $data = compact('status','user_id','comment_id','update_time');
            $query = compact('where','data');
            $commentPraiseResult = $this->model->index('commentPraiseUpdate',$query);
        }
        //更新评论点赞数目
        $commentResult = false;
        $where = ['id' => $comment_id];
        $field = 'praise_num';
        $query = compact('where','field');
        if($status == 0 && $commentPraiseResult) { //取消餐
            $commentResult = $commentModel->index('decrease',$query);//评论点赞量减一
        }else if($status == 1 && $commentPraiseResult) { // 点赞
            $commentResult = $commentModel->index('increase',$query);//评论点赞量加一
        }
        $result['status'] = false;
        if($commentResult && $commentPraiseResult) {
            $result['status'] = true;
        }
        return $result;
	}
    /**
     * [isPraise 用户点赞状态]
     * @AuthorHTL
     * @DateTime  2018-06-11T11:03:05+0800
     * @param     [type]                   $param [description]
     * @return    boolean                         [description]
     */
	public function isPraise($param)
    {
        $is_praise = 0; //默认点赞状态 0 未点赞 2 已点赞
        $header = $this->header;
        if(!isset($header['uid']) || empty($this->header['uid'])) return $is_praise;
        if(!isset($header['authorization'])) apiSend(['code'=>FAIL_CODE,'msg'=>LACK_TOKEN,'status'=>FAIL_STATUS],'json',true); //没有token，非法访问
        if(!Cache::has('user_token_' . $header['uid'])) apiSend(['code'=>TOKEN_EXPIRED_CODE,'msg'=>TOKEN_EXPIRED,'status'=>FAIL_STATUS],'json',true);//token失效，重新登录
        $token = Cache::get('user_token_' . $header['uid']);
        if($token != $header['authorization']) apiSend(['code'=>FAIL_CODE,'msg'=>TOKEN_ERROR,'status'=>FAIL_STATUS],'json',true);//token错误，重新登录
        $uid = $header['uid'];
        $where = ['user_id' => $uid,'news_id' => $param['id'],'status' => 1];
        $field = 'id';
        $query = compact('where','field');
        $list  = $this->model->index('praiseList',$query);
        if(!empty($list)) {  //如果不为空则表示已经点赞
            $is_praise = 1;
        }
        return $is_praise;
    }
}