<?php
namespace app\common\logic;
use think\Cache;
use app\common\model\News;
use app\common\logic\Base;
use app\common\model\UserPraise as UserPraiseModel;
/**
 * @ClassName:    UserPraise 
 * @Description:  用户点赞类 
 * @Created by:   [villager] 
 * @DateTime:     2018-05-28T14:13:06+0800
 */
Class UserPraise extends Base 
{
	protected $page = 1;
	protected $offset = 5;
	protected $field = '';
	/**
	 * [initialize:  初始化绑定UserPraise模型] 
	 * @Created by:   [villager] 
	 * @DateTime:     2018-05-16T15:16:16+0800
	 */
	protected function initialize() {
        parent::initialize();
        $this->model = new UserPraiseModel;
    }
    /**
     * [praiseAdd 点赞或取消赞]
     * @AuthorHTL villager
     * @DateTime  2018-06-01T16:14:54+0800
     * @return    [type]                   [description]
     */
	public function praiseUpdate($param)
	{
        $newsModel = new News();
        $status = $param['status'] == 0 ? 1: 0; //用户点赞状态 1 取消赞 0 赞; 数据库状态 0 取消赞 1 赞
        $user_id = $this->header['uid']; // 用户id
        $news_id = $param['news_id'];  // 新闻id
        $where = ['user_id' => $user_id,'news_id' => $news_id,'is_delete' => 0];
        $field = 'id,user_id,news_id';
        $query = compact('where','field');
        $list = $this->model->index('praiseList',$query);
        if(empty($list)) {     //首次点赞新增数据
            $data = compact('status','user_id','news_id');
            $query = compact('where','data');
            $praiseResult = $this->model->index('praiseAdd',$query);
        }else {                //更新点赞状态
            $update_time  = time();
            $data = compact('status','user_id','news_id','update_time');
            $query = compact('where','data');
            $praiseResult = $this->model->index('praiseUpdate',$query);
        }
        $where = ['id' => $news_id];
        $field = 'praise_num';
        $query = compact('where','field');
        if($status == 0) {
            $newsResult = $newsModel->index('decrease',$query);//新闻点赞量减一
        }else if($status == 1) {
            $newsResult = $newsModel->index('increase',$query);//新闻点赞量加一
        }
        $result['status'] = false;
        if($newsResult && $praiseResult) {
            $result['status'] = true;
        }
        $send = [
            'news_id' => $news_id,
            'type'    => 1
        ];
        swoole_client($send);//异步计算排行
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
        if(!isset($header['uid']) || empty($header['uid'])) return $is_praise;
        if(!isset($header['authorization'])) return $is_praise; //没有token，非法访问
        if(!Cache::has('user_token_' . $header['uid'])) return $is_praise;//token失效，重新登录
        $token = Cache::get('user_token_' . $header['uid']);
        if($token != $header['authorization']) return $is_praise;//token错误，重新登录
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