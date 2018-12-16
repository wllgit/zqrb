<?php
namespace app\common\logic;
use app\common\logic\Base;
use app\common\model\Column as ColumnModel;
/**
 * @ClassName:    Column 
 * @Description:  新闻类 
 * @Created by:   [villager] 
 * @DateTime:     2018-05-28T14:13:06+0800
 */
Class Column extends Base 
{
	protected $page = 1;
	protected $offset = 10;
	protected $field = '';
	/**
	 * [initialize:  初始化绑定news模型] 
	 * @Created by:   [villager] 
	 * @DateTime:     2018-05-16T15:16:16+0800
	 */
	protected function initialize() {
        parent::initialize();
        $this->model = new ColumnModel;
    }
    /**
     * [columnList 栏目列表]
     * @AuthorHTL villager
     * @DateTime  2018-06-01T15:47:53+0800
     * @param     [array]                   $param [传参]
     * @return    [array]                          [栏目列表]
     */
    public function columnList($param)
    {
    	$where = ['is_delete' => 0,'is_show' => 1,'parent_id' => 0]; //查询条件
    	$field = 'id,title,is_fixed';
    	$query = compact('where','field');
    	return $this->model->index('columnList',$query);
    }
    /**
     * [columnAdd 新增栏目]
     * @AuthorHTL villager
     * @DateTime  2018-06-01T16:14:54+0800
     * @return    [type]                   [description]
     */
	public function columnAdd($param)
	{
		$parent_id = isset($param['parent_id']) ? $param['parent_id'] : 0;
		if(!isset($param['title'])) apiSend(['code'=>LACK_CODE,'msg'=>LACK_PARAM,'status'=>FAIL_STATUS],'json',true);
		$title = $param['title'];
		$query['data'] = compact('parent_id','title');
		$result['status'] = $this->model->index('columnAdd',$query);
		$result['data']['id'] = $this->model->id;
		return $result;
	}
	
}