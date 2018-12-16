<?php
namespace app\common\logic;
use app\common\model\News;
use app\common\logic\Base;
use app\common\model\Config as ConfigModel;
/**
 * @ClassName:    Config 
 * @Description:  配置类 
 * @Created by:   [villager] 
 * @DateTime:     2018-05-28T14:13:06+0800
 */
Class Config extends Base 
{
	protected $page = 1;
	protected $offset = 5;
	protected $field = '';
	/**
	 * [initialize:  初始化绑定config模型] 
	 * @Created by:   [villager] 
	 * @DateTime:     2018-05-16T15:16:16+0800
	 */
	protected function initialize() {
        parent::initialize();
        $this->model = new ConfigModel;
    }
    /**
     * [aboutUs 关于我们]
     * @AuthorHTL villager
     * @DateTime  2018-06-01T16:14:54+0800
     * @return    [type]                   [description]
     */
	public function aboutUs($param)
	{
        $query['field'] = 'id,about_us';
        $query['where'] = ['id' => 1];
        $config_info = $this->model->index('configInfo',$query);
        return $config_info;
	}
}