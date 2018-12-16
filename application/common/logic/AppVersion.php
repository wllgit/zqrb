<?php
namespace app\common\logic;
use app\common\logic\Base;
use app\common\model\AppVersion as AppVersionModel;
/**
 * @ClassName:    AppVersion 
 * @Description:  新闻类 
 * @Created by:   [villager] 
 * @DateTime:     2018-05-28T14:13:06+0800
 */
Class AppVersion extends Base 
{
	protected $page = 1;
	protected $offset = 10;
	protected $field = '';
    protected $update_info = [
            'is_update'  => null,      //是否更新 0 否 1 是
            'version_id' => null,      //大版本号
            'version_mini' => null,    //小版本号
            'description'  => '暂无适用于该设备的APP',   //更新描述
            'publish_time' => null   //发布时间
    ];//更新信息
	/**
	 * [initialize:  初始化绑定news模型] 
	 * @Created by:   [villager] 
	 * @DateTime:     2018-05-16T15:16:16+0800
	 */
	protected function initialize() {
        parent::initialize();
        $this->model = new AppVersionModel;
    }
    /**
     * [checkVersion 检查APP版本]
     * @AuthorHTL
     * @DateTime  2018-07-02T15:09:25+0800
     * @param     [type]                   $param [description]
     * @return    [type]                          [description]
     */
    public function checkVersion($param)
    {
        $app_id  = $param['app_id'];//客户端设备类型 0:安卓手机, 1:安卓pad,2:iphone,3ipad
        $version_id   = $param['version_id'];//APP大版本号
        $version_mini = $param['version_mini'];//小版本号
        $where = ['app_id' => $app_id,'is_delete' => 0];
        $field = 'version_id,version_mini,version_code,description,type,publish_time';
        $order = 'id desc';
        $query = compact('where','field','order');
        $version_list = $this->model->index('appVersionList',$query);
        if(!empty($version_list)) {
            $version_info = $version_list[0];
            $is_update = 0;
            if($version_mini < $version_info['version_mini'] && $version_id <= $version_info['version_id'] && $version_info['type'] !=0) {
                $is_update = 1;
            }
            $this->update_info = [
                'is_update'  => $is_update,                       //是否更新 0 否 1 是
                'version_id' => $version_info['version_id'],      //大版本号
                'version_mini' => $version_info['version_mini'],  //小版本号
                'description'  => $version_info['description'],   //更新描述
                'publish_time' => $version_info['publish_time']   //发布时间
            ];
        }
        return $this->update_info;
    }
    /**
     * [AppVersionAdd 新增app版本]
     * @AuthorHTL villager
     * @DateTime  2018-06-01T16:14:54+0800
     * @return    [type]                   [description]
     */
	public function appVersionAdd($param)
	{
		$parent_id = isset($param['parent_id']) ? $param['parent_id'] : 0;
		if(!isset($param['title'])) apiSend(['code'=>LACK_CODE,'msg'=>LACK_PARAM,'status'=>FAIL_STATUS],'json',true);
		$title = $param['title'];
		$query['data'] = compact('parent_id','title');
		$result['status'] = $this->model->index('AppVersionAdd',$query);
		$result['data']['id'] = $this->model->id;
		return $result;
	}
	
}