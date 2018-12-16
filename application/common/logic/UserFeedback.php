<?php
namespace app\common\logic;
use think\Cache;
use app\common\model\News;
use app\common\logic\Base;
use app\common\model\UserFeedback as UserFeedbackModel;
/**
 * @ClassName:    UserFeedback 
 * @Description:  新闻类 
 * @Created by:   [villager] 
 * @DateTime:     2018-05-28T14:13:06+0800
 */
Class UserFeedback extends Base 
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
        $this->model = new UserFeedbackModel;
    }
    /**
     * [feedbackAdd 点赞或取消赞]
     * @AuthorHTL villager
     * @DateTime  2018-06-01T16:14:54+0800
     * @return    [type]                   [description]
     */
	public function feedbackSave($param)
	{
        $header = $this->header;
        $data['user_id'] = $header['uid'];
        $data['feedback'] = $param['feedback'];
        $query['data'] = $data;
        $result['status'] = $this->model->index('feedbackAdd',$query);
        return $result;
	}
}