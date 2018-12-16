<?php
namespace app\api\logic;
use think\cache\driver\Redis;
use think\Loader;
use think\Request;
use think\Model;
/**
 * @ClassName:    Index 
 * @Description:  Detail 
 * @Created by:   [villager] 
 * @DateTime:     2018-05-24T13:35:20+0800
 */
Class Index extends Model 
{
	protected $model;
	protected $page = 1;
	protected $offset = 10;
	/**
	 * [__construct:  description] 
	 * @Created by:   [villager] 
	 * @DateTime:     2018-05-24T13:35:20+0800
	 */
	public function _initialize()
	{
		parent::_initialize();
		$this->model = Loader::model('Interfaces');
	}
	/**
	 * description
	 * @author    [villager]
	 * @param     [type]     $param [description]
	 * @return    [type]     [description]
	 * @DateTime  2018-05-24T13:35:20+0800
	*/
	public function interfaceList($param)
	{
		if(!empty($param['page'])) {
			$this->page = $param['page'];
		}
		if(!empty($param['offset'])) {
			$this->offset = $param['offset'];
		}
		$redis = new Redis();
		$key = 'interfaceListPage_'.$this->page.'Offset_'.$this->offset;
		if($redis->has($key)) {
			return $redis->get($key);
		}
		$start = ($this->page - 1) * $this->offset;
		$query['start'] = $start;
		$query['offset'] = $this->offset;
		$query['field ']= 'id,interface_name,note';
		$list = $this->model->index('interfaceList',$query);
		$redis->set($key,$list);
		return $list;
	}
	/**
	 * description
	 * @author    [villager]
	 * @param     [type]     $param1 [description]
	 * @param     [type]     $param2 [description]
	 * @return    [type]     [description]
	 * @DateTime  2018-05-24T13:35:20+0800
	*/
	public function funcName2($param1,$param2)
	{

	}
}