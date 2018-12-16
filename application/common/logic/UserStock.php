<?php
namespace app\common\logic;
use think\Cache;
use app\common\logic\Base;
use app\common\model\UserStock as UserStockModel;
/**
 * @ClassName:    UserStock 
 * @Description:  用户自选股票类 
 * @Created by:   [villager] 
 * @DateTime:     2018-05-28T14:13:06+0800
 */
Class UserStock extends Base 
{
	protected $page = 1;
	protected $offset = 100;
	protected $field = '';
	/**
	 * [initialize:  初始化绑定UserStock模型] 
	 * @Created by:   [villager] 
	 * @DateTime:     2018-05-16T15:16:16+0800
	 */
	protected function initialize() {
        parent::initialize();
        $this->model = new UserStockModel;
    }
    /**
     * [userStockList 用户自选股票列表]
     * @AuthorHTL villager
     * @DateTime  2018-06-01T15:47:53+0800
     * @param     [array]                   $param [传参]
     * @return    [array]                          [用户自选股票列表]
     */
    public function userStockList($param)
    {
        $list = [];
        $header = $this->header;
        if(!isset($header['uid']) || empty($header['uid'])) return $list;
        if(!isset($header['authorization'])) return $list; //没有token，非法访问
        if(!Cache::has('user_token_' . $header['uid'])) return $list;//token失效，重新登录
        $token = Cache::get('user_token_' . $header['uid']);
        if($token != $header['authorization']) return $list;//token错误，重新登录
        $where = ['user_id' => $header['uid'],'is_delete'=>0];
        $field = 'id,user_id,code,is_top';
        $offset = isset($param['offset']) ? $param['offset'] : $this->offset; // 偏移量
        $start  = ((isset($param['page']) ? $param['page']   : $this->page) - 1) * $offset;
        $order  = 'is_top desc,id desc';
        $query  = compact('where','field','offset','start','order');
        $list   = $this->model->index('userStockList',$query);// code自选股编码
        $codes = [];
        foreach ($list as $key => $value) {
            $codes[] = $value['code'];
        }
        $raw_data['codes'] = $codes;
        $raw_data = json_encode($raw_data);//生成自选股编码json格式，用于获取对应的媒体热度
        if(!empty($list)) {
            $token = ['expire_time' => 0];
            if(file_exists('token.txt')) {
                //读取文件中token中的信息
                $info = file_get_contents('token.txt');
                $token = json_decode($info,true);
            }
            //如果token过期，重新请求并写入文件
            if($token['expire_time'] <= time()) {
                //获取token
                $url = 'http://node.isuperstock.com/auth/getTokenByDeviceId/admin';
                $info = curl($url);
                $info = json_decode($info);
                $expire_time = time() + 30 * 24 * 60 * 3600;//token 过期时间 30天
                $token = ['value'=>$info->payload,'expire_time'=>$expire_time];
                $token_json = json_encode($token);
                file_put_contents('token.txt', $token_json);//将token写入文件
            }
            //获取媒体热度
            $media_url = 'http://node.isuperstock.com/mediaHot/getSome';
            $curl_header = ['Content-Type: application/json','Authorization:'.$token['value']];
            $media_info = curl($media_url,$curl_header,$raw_data);
            $media_info = json_decode($media_info);
            $code_list = $media_info->payload;
        }
        foreach ($list as $key => $value) {
            foreach ($code_list as $k => $val) {
                if($value['code'] == $val->code) {
                    $list[$key]['mediaHot'] = $val->mediaHot;
                }
            }
        }
        return $list;
    }
    /**
     * [userStockAdd 新增用户自选股票]
     * @AuthorHTL villager
     * @DateTime  2018-06-01T16:14:54+0800
     * @return    [type]                   [description]
     */
	public function userStockUpdate($param)
	{
		$user_id   = $this->header['uid'];
        $stockList = array_reverse($param['stockList']);
        if(!isset($param['stockList']) || !isset($param['is_delete'])) apiSend(['code'=>LACK_CODE,'msg'=>LACK_PARAM,'status'=>FAIL_STATUS],'json',true); //缺少参数
        foreach ($stockList as $key => $value) {
            $stockList[$key]['user_code'] = $user_id . $value['code']; 
            $stockList[$key]['user_id']   = $user_id; 
        }
        //apiSend(['code'=>LACK_CODE,'msg'=>LACK_PARAM,'status'=>FAIL_STATUS,'data'=>$stockList],'json',true);
        if($param['is_delete'] == 0) { //添加股票
            $code = $stockList[0]['code'];
            $user_code = $user_id . $code; 
            $where = ['user_code' => $user_code,'is_delete' => 0];
            $field = 'id,user_code';
            $info_query = compact('where','field');
            $info = $this->model->index('userStockInfo',$info_query);
            if(empty($info)) {
                $query['data'] = $stockList;
                $result['status'] = $this->model->index('userStockAddList',$query);
                return $result;
            }
            apiSend(['code'=>STOCK_EXISTS_CODE,'msg'=>STOCK_EXISTS_MSG,'status'=>FAIL_STATUS],'json',true);    
        }else { //编辑、删除股票
            $result['status'] = true;
            $where = ['user_id' => $user_id,'is_delete' => 0];
            $query['where'] = $where;
            $list = $this->model->index('userStockList',$query);
            if(!empty($list)) {
                $result['status'] = $this->model->index('userStockDelete',$query);
                if(!$result['status']) return $result;
            }
            if(!empty($stockList)) {
                $query['data'] = $stockList;
                $result['status'] = $this->model->index('userStockAddList',$query);
            }
            
        }
        return $result;
	}
    /**
     * [userStockDelete 删除用户自选股]
     * @AuthorHTL
     * @DateTime  2018-06-15T14:35:02+0800
     * @param     [type]                   $param [description]
     * @return    [type]                          [description]
     */
    public function userStockDelete($param)
    {
        $user_id = $this->header['uid'];
        $code = $param['id'];
        $where = ['user_id' => $user_id,'code' => $code,'is_delete' => 0];
        $query['where'] = $where;
        $result['status'] = $this->model->index('userStockDelete',$query);
        return $result;
    }
    /**
     * [userStockDetail 个选股详情]
     * @AuthorHTL
     * @DateTime  2018-06-15T15:23:32+0800
     * @param     [type]                   $param [description]
     * @return    [type]                          [description]
     */
    public function userStockDetail($param)
    {
        $code = $param['id'];
        $count = $param['count'];
        $url = 'https://pdtapi.sogukz.com/opinions/effects/v2?rid='.$code.'&count='.$count;
        $info = curl($url);
        $info = json_decode($info);
        $list = $info->payload;
        return $list;
    }
	
}