<?php
namespace app\common\logic;
use think\Cache;
use app\common\logic\Base;
use app\common\model\UserCollection as UserCollectionModel;
/**
 * @ClassName:    News 
 * @Description:  新闻类 
 * @Created by:   [villager] 
 * @DateTime:     2018-05-28T14:13:06+0800
 */
Class UserCollection extends Base 
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
        $this->model = new UserCollectionModel();
    }
    /**
     * [isCollection 用户收藏状态]
     * @AuthorHTL
     * @DateTime  2018-06-11T11:03:05+0800
     * @param     [type]                   $param [description]
     * @return    boolean                         [description]
     */
	public function isCollection($param)
    {
        $is_collection = 0; //默认用户收藏状态 0 未收藏 1 已收藏
        $header = $this->header;
        if(!isset($header['uid']) || empty($this->header['uid'])) return $is_collection;
        if(!isset($header['authorization'])) return $is_collection; //没有token，非法访问
        if(isset($header['uid']) && !empty($header['uid'])) {
            $uid = $header['uid'];
        }else {
            return $is_collection;
        }
        if(!Cache::has('user_token_' . $header['uid'])) return $is_collection;//token失效，重新登录
        $token = Cache::get('user_token_' . $header['uid']);
        if($token != $header['authorization']) return $is_collection;//token错误，重新登录
        $where = ['user_id' => $uid,'news_id' => $param['id'],'status' => 1,'type' => 0];
        $field = 'id';
        $query = compact('where','field');
        $list  = $this->model->index('collectionList',$query);
        if(!empty($list)) {
            $is_collection = 1;
        }
        return $is_collection;
    }
}