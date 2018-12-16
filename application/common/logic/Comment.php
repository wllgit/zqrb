<?php
namespace app\common\logic;
use app\common\model\User;
use app\common\logic\Base;
use app\common\model\News;
use app\common\model\CommentReply;
use app\common\model\Comment as CommentModel;
/**
 * @ClassName:    Comment 
 * @Description:  评论类 
 * @Created by:   [villager] 
 * @DateTime:     2018-05-28T14:13:06+0800
 */
Class Comment extends Base 
{
    protected $page = 1;
    protected $offset = 3;
    protected $field = '';
    protected $commentList = [];
    /**
     * [initialize:  初始化绑定Comment模型] 
     * @Created by:   [villager] 
     * @DateTime:     2018-05-16T15:16:16+0800
     */
    protected function initialize() {
        parent::initialize();
        $this->model = new CommentModel;
        $this->validate = validate('Comment');
    }
    /**
     * [commentList 评论列表]
     * @AuthorHTL villager
     * @DateTime  2018-06-01T15:47:53+0800
     * @param     [array]                   $param [传参]
     * @return    [array]                          [评论列表]
     */
    public function commentList($param)
    {
        if(!isset($param['news_id'])) apiSend(['code'=>LACK_CODE,'msg'=>LACK_PARAM,'status'=>FAIL_STATUS],'json',true); //新闻id
        $offset = !empty($param['offset']) ? $param['offset'] : $this->offset; // 偏移量
        $start  = ((!empty($param['page']) ? $param['page']   : $this->page) - 1) * $offset;
        $where = ['is_delete' => 0,'is_show' => 1,'news_id' => $param['news_id']]; //查询条件
        if(!empty($param['min_comments_id'])) { // 当前页最小的评论id ,防止数据重复
            $min_comments_id = $param['min_comments_id'];
            $where['id'] = ['lt',"$min_comments_id"];
        }
        $field = 'id,comment,praise_num,create_time,user_id,reply_num';
        $query = compact('where','field','start','offset');
        $list = $this->model->index('commentList',$query);
        $user_ids = '';//用户id集合
        $commentReplyModel = new CommentReply();
        if(!empty($list)) { // 如果list不为空重组数据
            foreach ($list as $key => $value) {
                $arr = [];
                $comment_reply = [];
                $is_praise = empty($list[$key]['commentPraise']) ? 0 : 1; //用户是否对该评论点赞
                if(!empty($value['commentReply'])) {
                    foreach ($value['commentReply'] as $k => $val) {
                        $user_info = [
                            'user_id'  => $val['userInfo'][0]['id'],
                            'nickname' => $val['userInfo'][0]['nickname'],
                            'avatar'   => $val['userInfo'][0]['avatar'],
                        ];
                        $comment_reply[] = [
                            'reply_id' => $val['reply_id'],
                            'reply'    => $val['reply'],
                            'userInfo' => $user_info
                        ];
                        if($k >= 1) break;
                        
                    }
                }
                // 计算回复的总数
                $comment_reply_query['where'] = ['comment_id' => $value['id'],'is_show' => 1,'is_delete' => 0];
                $reply_num = $commentReplyModel -> index('count',$comment_reply_query);
                $this->commentList[] = [  //将评论列表重新赋值
                    'comment_id'   => $value['id'],
                    'comment'      => $value['comment'],
                    'praise_num'   => $value['praise_num'],
                    'create_time'  => $value['create_time'],
                    'user_id'      => $value['user_id'],
                    'reply_num'    => $reply_num,
                    'is_praise'    => $is_praise,
                    'commentReply' => $comment_reply
                ];
                //拼接用户id
                $user_ids .= $value['user_id'] . ',';
            }
            $user_ids = rtrim($user_ids,',');
            $user_query['where'] = ['id' => ['in',$user_ids]];
            $user_query['field'] = 'id as user_id,nickname,avatar';
            $userModel = new User();
            //查询用户
            $user_list = $userModel->index('userList',$user_query);
            //匹配各个评论对应的用户
            foreach ($this->commentList as $key => $value) {
                foreach ($user_list as $k => $val) {
                    if($value['user_id'] == $val['user_id']) {
                        $this->commentList[$key]['userInfo'] = $val;
                    }
                }
            }
        }   
        return $this->commentList;
    }
    /**
     * [commentAdd 新增评论]
     * @AuthorHTL villager
     * @DateTime  2018-06-01T16:14:54+0800
     * @return    [type]                   [description]
     */
    public function commentAdd($param)
    {
        if(!isset($param['news_id'])) apiSend(['code'=>LACK_CODE,'msg'=>LACK_PARAM,'status'=>FAIL_STATUS],'json',true); //新闻id
        if(!isset($param['comment'])) apiSend(['code'=>LACK_CODE,'msg'=>LACK_PARAM,'status'=>FAIL_STATUS],'json',true); //新闻评论
        if(!$this->validate->check($param)){
            $error_info = $this->validate->getError();
            apiSend(['code'=>$error_info['code'],'msg'=>$error_info['msg'],'status'=>FAIL_STATUS],'json',true);
        }
        $news_id = $param['news_id'];
        $comment = $param['comment'];
        $user_id = $this->header['uid'];
        $query['data'] = compact('news_id','comment','user_id');
        $result['status'] = $this->model->index('commentAdd',$query);
        $result['data']['id'] = $this->model->id;
        // if($result['status']) {
        //     //评论量量(点击量) + 1
        //     $newsModel = new News();
        //     $increase_query['where'] = ['id'=>$news_id];
        //     $increase_query['field'] = 'comments_num';
        //     $result['status'] = $newsModel->index('increase',$increase_query);
        // }
        return $result;
    }
    
}