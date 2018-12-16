<?php
namespace app\common\logic;
use think\Cache;
use app\common\logic\Base;
use app\common\model\Column;
use app\common\model\UserColumn as UserColumnModel;
/**
 * @ClassName:    News 
 * @Description:  新闻类 
 * @Created by:   [villager] 
 * @DateTime:     2018-05-28T14:13:06+0800
 */
Class UserColumn extends Base 
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
        $this->model = new UserColumnModel;
    }
    /**
     * [columnList 用户栏目列表和固定栏目列表]
     * @AuthorHTL villager
     * @DateTime  2018-06-01T15:47:53+0800
     * @param     [array]                   $param [传参]
     * @return    [array]                          [栏目列表]
     */
    public function columnList($param)
    {
        //接口位置 index:首页 more:更多
        if(!isset($param['action'])) apiSend(['code'=>FAIL_CODE,'msg'=>LACK_PARAM,'status'=>FAIL_STATUS],'json',true);
        $action = $param['action'];
        $columnModel = new Column();
        //所有栏目列表
        $allList = [];
        $where = ['is_delete' => 0,'is_show' => 1];
        $field = 'id as column_id,title,is_fixed';
        $order = 'column_id asc';
        $query = compact('where','field','order');
        //查询全部栏目
        $allList  = $columnModel->index('columnList',$query);
        $uid = 0;
        //用户是否登录
        if(isset($this->header['uid']) && !empty($this->header['uid'])) {
            $uid = $this->header['uid'];
        }
        if($uid != 0) {
            if(!isset($this->header['authorization'])) $uid = 0; //没有token，非法访问
            if(!Cache::has('user_token_' . $this->header['uid'])) $uid = 0;//token失效，重新登录
            $token = Cache::get('user_token_' . $this->header['uid']);
            if($token != $this->header['authorization']) $uid = 0;//token错误，重新登录
        }
        $where = ['is_delete' => 0,'is_show' => 1,'user_id' => $uid];
        $field = 'id,user_id,column_ids';
        $query = compact('where','field','order');
        //查询用户自定义栏目
        $user_columns = $this->model->index('columnInfo',$query);
        if(empty($user_columns)) {           //用户未登录或首次请求栏目接口
            $where = ['is_delete' => 0,'is_show' => 1,'is_fixed' => 1];
            $field = 'id as column_id,title,is_fixed';
            $order = 'is_fixed desc';
            $start = 0;
            $offset = $this->offset;
            $query = compact('where','field','order','start','offset');
            //查询包含固定栏目的五条数据
            $topList  = $columnModel->index('columnList',$query);
            $column['topList']  = $topList;
        }else {                             // 用户登录-请求用户自定义栏目
            $column_ids_str = $user_columns['column_ids'];
            $column_ids_arr = explode(',', $user_columns['column_ids']);
            //固定栏目查询条件
            $where = ['is_delete' => 0,'is_show' => 1,'id'=> ['in',"$column_ids_str"]];
            $field = 'id as column_id,title,is_fixed';
            $query = compact('where','whereOr','field');
            //查询固定栏目和用户自定义栏目
            $topList = [];
            //对用户栏目按照用户自定义进行排序
            $columnList  = $columnModel->index('columnList',$query);
            foreach ($column_ids_arr as $key => $value) {
                foreach ($columnList as $k => $val) {
                    if($value == $val['column_id']) {
                        $topList[] = $val;
                    }
                }
            }
        }
        //更多模块栏目列表
        if($action == 'more'){
            $leftList = [];
            //全部栏目与用户自定义的栏目的差集
            $diffList = array_diff($allList, $topList);
            foreach ($diffList as $key => $value) {
                $leftList[] = $value;
            }
            $column['leftList'] = $leftList;
        }
        $column['topList']  = $topList;
        return $column;
    }
    /**
     * [columnUpdate 新增栏目]
     * @AuthorHTL villager
     * @DateTime  2018-06-01T16:14:54+0800
     * @return    [type]                   [description]
     */
	public function columnUpdate($param)
	{
        //获取header头信息
        //if(!isset($this->header['uid'])) apiSend(['code'=>LACK_CODE,'msg'=>LACK_PARAM,'status'=>FAIL_STATUS],'json',true);//缺少用户id
        if(!isset($param['column_ids'])) apiSend(['code'=>LACK_CODE,'msg'=>LACK_PARAM,'status'=>FAIL_STATUS],'json',true);//缺少参数
        $where = ['user_id' => $this->header['uid'],'is_delete' => 0];
        $field = 'id,column_ids';
        $query = compact('where','field','order');
        //查询用户自定义栏目
        $list = $this->model->index('columnList',$query);
        if(empty($list)) {       //如果为空为新增操作
            $user_id = $this->header['uid'];
            $column_ids = $param['column_ids'];
            $query['data'] = compact('user_id','column_ids');
            $result['status'] = $this->model->index('columnAdd',$query);
            $result['data']['id'] = $this->model->id;
            return $result;
        }
        //不为空为更新操作
        $user_id = $this->header['uid'];
        $column_ids = $param['column_ids'];
        $query['where'] = $where;
        $update_time = time();
        $query['data'] = compact('user_id','column_ids','update_time');
		$result['status'] = $this->model->index('columnUpdate',$query);
		return $result;
	}
	
}