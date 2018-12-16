<?php
namespace app\api\logic;
use think\Model;
use think\Loader;
/**
 * @ClassName:    User 
 * @Description:  Detail 
 * @Created by:   [villager] 
 * @DateTime:     2018-05-16T15:16:16+0800
 */
Class User extends Model 
{
	protected $start = 0;
	protected $offset = 10;
	/**
	 * [initialize:  description] 
	 * @Created by:   [villager] 
	 * @DateTime:     2018-05-16T15:16:16+0800
	 */
	protected function initialize() {
        parent::initialize();
        $this->model = model('User');
    }
	/**
	 * 用户列表
	 * @author    [villager]
	 * @return    [type]     [description]
	 * @DateTime  2018-05-16T15:16:16+0800
	*/
	public function userList()
	{
		$where['is_delete'] = ['eq',0];
		$query['where'] = $where;
        $query['start'] = $this->start;
        $query['offset']   = $this->offset;
        $query['field'] = 'uid,name,phone';
        $query['order'] = 'uid asc';
        $list = $this->model -> index('userList',$query);
        return $list;
	}
	public function register($param)
	{
		
	}
}