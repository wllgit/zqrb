<?php
namespace app\common\logic;
use think\Model;
use think\Cache;
use think\Request;
/**
 * @ClassName:    Base 
 * @Description:  回复类 
 * @Created by:   [villager] 
 * @DateTime:     2018-05-28T14:13:06+0800
 */
Class Base extends Model 
{
	protected $page = 1;
	protected $offset = 5;
	protected $field = '';
    protected $header;
	/**
	 * [initialize:  初始化绑定news模型] 
	 * @Created by:   [villager] 
	 * @DateTime:     2018-05-16T15:16:16+0800
	 */
	protected function initialize() {
        parent::initialize();
        $this->header = Request::instance()->header();
    }
}