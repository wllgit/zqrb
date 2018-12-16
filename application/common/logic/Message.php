<?php
namespace app\common\logic;
use think\Cache;
use app\common\logic\Base;
use app\common\model\Comment;
use app\common\model\CommentReply as CommentReplyModel;
/**
 * @ClassName:    Message 
 * @Description:  消息类 
 * @Created by:   [villager] 
 * @DateTime:     2018-05-28T14:13:06+0800
 */
Class Message extends Base 
{
	protected $page = 1;
	protected $offset = 20;
	protected $field = '';
    protected $userId = 0; //用户id
    protected $messageList = []; //消息列表
    protected $userInfo = [
        //用户id
        'id' =>0,
        //用户昵称
        'nickname' => null,
        //用户头像
        'avatar' => null
    ];
    protected $replyList = []; //回复列表
    protected $min_reply_id; //当页最小回复id
	/**
	 * [initialize:  初始化绑定news模型] 
	 * @Created by:   [villager] 
	 * @DateTime:     2018-05-16T15:16:16+0800
	 */
	protected function initialize() 
    {
        parent::initialize();
        $this->model = new CommentReplyModel;    
    }
    
    /**
     * [userMessageList 用户消息列表]
     * @AuthorHTL
     * @DateTime  2018-06-21T11:13:11+0800
     * @return    [type]                   [description]
     */
    public function messageList($param)
    {
        $header = $this->header;// header 请求头信息
        $offset = isset($param['offset']) ? $param['offset'] : $this->offset;
        if(!isset($header['uid']) || empty($header['uid'])) return $this->replyList;
        if(!isset($header['authorization'])) return $this->replyList;//没有token，非法访问
        if(!Cache::has('user_token_' . $header['uid'])) return $this->replyList;//token失效，重新登录
        $token = Cache::get('user_token_' . $header['uid']);
        if($token != $header['authorization']) return $this->replyList;//token错误，重新登录
        $user_id = $header['uid'];
        $where = ['commented_user_id' => $user_id,'is_delete' => 0,'is_show' => 1];
        $order = 'is_read asc,id desc';
        if(isset($param['min_reply_id'])) {// 上一页最小reply_id
            $min_reply_id = $param['min_reply_id'];
            $where['id'] = ['lt',"$min_reply_id"];
        }
        //查询字段
        $field = 'id as reply_id,reply,user_id,commented_user_id,create_time,news_id';
        $query = compact('where','field','offset','order');
        $commentReplyModel = new CommentReplyModel();
        $reply_list = $commentReplyModel->index('messageList',$query);
        $tmpArr = [];
        $news_info = [];
        $news_souce = [];
        $reply_ids = []; // 当前页reply_id集合
        if(!empty($reply_list)) {
            foreach ($reply_list as $key => $value) {
                $reply_ids[] = $value['reply_id'];
                if(!empty($value['newsInfo'])) {
                    $news_info = $value['newsInfo'][0];
                    $news_info['source'] = null;
                    if(!empty($news_info->newsSource)) {
                        $news_souce = $news_info->newsSource;
                        $news_info['source'] = $news_souce[0];
                    }
                    unset($news_info['newsSource']);
                    $tmpArr[] = [
                        'reply_id' => $value['reply_id'],
                        'reply'=>$value['reply'],
                        'create_time'=>$value['create_time'],
                        'user_info' => $value['userInfo'][0],
                        'news_info' => $news_info
                    ];
                }
            }
            //将当前页的回复更改为已读状态
            $reply_ids = implode(',', $reply_ids);
            $update_query['where'] = ['id' => ['in',$reply_ids]];
            $update_query['data']  = ['is_read' => 1,'update_time'=>time()];
            $result = $commentReplyModel->index('commentReplyUpdate',$update_query);
            if($result){
                $this->replyList = $tmpArr;
            }
        }
        return $this->replyList;
    }
    /**
     * [messageDelete 删除消息]
     * @AuthorHTL villager
     * @DateTime  2018-06-21T17:18:37+0800
     * @return    [type]                   [description]
     */
    public function messageDelete($param)
    {
        $reply_id = $param['id'];
        $query['where'] = ['id' => $reply_id];
        $query['data'] = ['is_show' => 0,'update_time'=>time()];
        $commentReplyModel = new CommentReplyModel();
        $result['status'] = $commentReplyModel->index('commentReplyUpdate',$query);
        return $result;
    }
	
}