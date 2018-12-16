<?php
/**
 * Created by PhpStorm.
 * User: sogu
 * Date: 2018/6/7
 * Time: 下午1:48
 */
namespace app\api\controller;

use think\Cache;
use think\Db;
use think\Request;
use think\controller;
use app\api\controller\Api;
use app\api\model\NewIndex as NewIndexModel;
use app\cron\controller\NewsPaper;

use app\common\model\News as NewsModel;


class NewIndex extends Api{
    /**
     * 获取header信息
     */
    protected function __header(){
        $request = Request::instance()->header();
        if(isset($request)){
            return $request;
        }
    }
    /**
     * 判断token
     */
    protected function __isToken(){

        $header = $this->__header();

        if(!isset($header['uid']) || empty($header['uid']))
            apiSend(['code'=>NOT_LOGIN,'msg'=>NOT_LOGIN_MSG,'status'=>FAIL_STATUS],'json',true); //没有uid，非法访问

        if(!isset($header['authorization']) || !$header['authorization'])
            apiSend(['code'=>TOKEN_EXPIRED_CODE,'msg'=>TOKEN_EXPIRED,'status'=>FAIL_STATUS],'json',true); //没有token，非法访问

        if(!Cache::has('user_token_' . $header['uid']))
            apiSend(['code'=>TOKEN_EXPIRED_CODE,'msg'=>TOKEN_EXPIRED,'status'=>FAIL_STATUS],'json',true);//token失效，重新登录

        $token = Cache::get('user_token_' . $header['uid']);
        if($token != $header['authorization'])
            apiSend(['code'=>TOKEN_EXPIRED_CODE,'msg'=>TOKEN_ERROR,'status'=>FAIL_STATUS],'json',true);//token错误，重新登录
    }
    /**
     * banner列表
     */
    public function bannerList(){

        $newIndexModel = new NewIndexModel();
        $bannerList = $newIndexModel->bannerList();

        if(isset($bannerList) && $bannerList){
            foreach ($bannerList as &$v){
                $v['sort'] = $v['sort']-1;
            }
        }

        if($bannerList){
            return apiSend(['code'=>SUCCESS_CODE,'msg'=>SUCCESS_MESSAGE,'status'=>SUCCESS_STATUS,'data'=>$bannerList],'json');
        }else{
            return apiSend(['code'=>SUCCESS_CODE,'msg'=>NULL_MESSAGE,'status'=>SUCCESS_STATUS],'json');
        }
    }
    /**
     * 广告列表
     * posi_type位置类型 1：起始页；4：新闻详情
     */
    public function adverList(){

        $request_info = get_input();

        if(!isset($request_info['posi_type']) || !$request_info['posi_type']){
            return apiSend(['code'=>FAIL_CODE,'msg'=>LACK_PARAM,'status'=>FAIL_STATUS],'json');
        }

        $model = new NewIndexModel();
        $advList = $model->adverList($request_info);

        if($advList){
            return apiSend(['code'=>SUCCESS_CODE,'msg'=>SUCCESS_MESSAGE,'status'=>SUCCESS_STATUS,'data'=>$advList],'json');
        }else{
            return apiSend(['code'=>SUCCESS_CODE,'msg'=>NULL_MESSAGE,'status'=>SUCCESS_STATUS],'json');
        }
    }

    /**
     * 根据时间戳获取时间语句
     */
    protected function __showTime($time){

        switch ($timediff = intval(time() - $time))
        {
            case intval($timediff / 86400) >= 1:
                $data = date('Y-m-d',$time);
                break;
            case intval($timediff / 3600) >= 1:
                $data = intval($timediff / 3600).'小时前';
                break;
            case intval($timediff / 60) >= 1:
                $data = intval($timediff / 60).'分钟前';
                break;
            case intval($timediff / 60) < 1:
                $data = '刚刚';
                break;
        }
        return $data;
    }
    /**
     * banner列广告
     */
    public function bannerAdver(){

        $model = new NewIndexModel();
        $list = $model->bannerAdver();

        if($list){
            foreach ($list as $k=>&$v){
                $v['position'] = $v['position']-1;
                //当adver_id是0随机取一条
                if($v['adver_id'] == 0){
                    $v['posi_type'] = 2;
                    $detail = $model->findAdverNews($v);
                    unset($v['posi_type']);
                    if(isset($detail)){
                        $v = array_merge($detail,$v);
                    }else{
                        unset($list[$k]);
                    }
                }else{
                    //存在id根据id查询新闻广告
                    $detail = $model->findAdverNews($v);
                    if(isset($detail)){
                        $v = array_merge($detail,$v);
                    }else{
                        unset($list[$k]);
                    }
                }
            }
        }

        $list = array_values($list);
        if($list){
            return apiSend(['code'=>SUCCESS_CODE,'msg'=>SUCCESS_MESSAGE,'status'=>SUCCESS_STATUS,'data'=>$list],'json');
        }else{
            return apiSend(['code'=>SUCCESS_CODE,'msg'=>NULL_MESSAGE,'status'=>SUCCESS_STATUS],'json');
        }
    }

    /**
     * 新闻列广告
     * column_id 栏目id
     */
    public function adverNewsList(){
        $request_info = get_input();

        if(!isset($request_info['column_id']) || !$request_info['column_id']){
            return apiSend(['code'=>FAIL_CODE,'msg'=>LACK_PARAM,'status'=>FAIL_STATUS],'json');
        }

        $model = new NewIndexModel();
        $list = $model->adverNewsList($request_info['column_id']);

        if($list){
            foreach ($list as $k=>&$v){
                $v['position'] = $v['position']-1;
                //当adver_id是0随机取一条
                if($v['adver_id'] == 0){
                    $v['posi_type'] = 3;
                    $detail = $model->findAdverNews($v);
                    unset($v['posi_type']);
                    if(isset($detail)){
                        $detail['time'] = $this->__showTime($detail['update_time']);
                        $v = array_merge($detail,$v);
                    }else{
                        unset($list[$k]);
                    }
                }else{
                    //存在id根据id查询新闻广告
                    $detail = $model->findAdverNews($v);
                    if(isset($detail)){
                        $detail['time'] = $this->__showTime($detail['update_time']);
                        $v = array_merge($detail,$v);
                    }else{
                        unset($list[$k]);
                    }
                }
            }
        }

        $list = array_values($list);
        if($list){
            return apiSend(['code'=>SUCCESS_CODE,'msg'=>SUCCESS_MESSAGE,'status'=>SUCCESS_STATUS,'data'=>$list],'json');
        }else{
            return apiSend(['code'=>SUCCESS_CODE,'msg'=>NULL_MESSAGE,'status'=>SUCCESS_STATUS],'json');
        }

    }
    /**
     * 广告详情
     * id 广告id
     */
    public function advertisements(){
        $request_info = get_input();

        if(!isset($request_info['id']) || !$request_info['id']){
            return apiSend(['code'=>FAIL_CODE,'msg'=>LACK_PARAM,'status'=>FAIL_STATUS],'json');
        }

        $model = new NewIndexModel();
        $advDetail = $model->advertisements($request_info);

        if($advDetail['source_type'] == 4){
            $video_pic = $model->adverPic($advDetail['id'],$type=2);
            $top_pic = $model->adverPic($advDetail['id'],$type=1);
        }else{
            $top_pic = $model->adverPic($advDetail['id'],$type=1);
            $video_pic[0]['pic_path'] = '';
        }

        $returnData = array();

        $returnData['id'] = $advDetail['id'];
        $returnData['title'] = $advDetail['title'];
        $returnData['auther'] = $advDetail['auther'];
        $returnData['source'] = $advDetail['source'];
        $returnData['detail'] = $advDetail['description'];
        $returnData['publish_time'] = date('Y-m-d h:i',$advDetail['create_time']);
        $returnData['source_type'] = $advDetail['source_type'];
        $returnData['top_pic'] = $top_pic[0]['pic_path'];
        $returnData['video'] = $video_pic[0]['pic_path'];

        if($advDetail){
            return apiSend(['code'=>SUCCESS_CODE,'msg'=>SUCCESS_MESSAGE,'status'=>SUCCESS_STATUS,'data'=>$returnData],'json');
        }else{
            return apiSend(['code'=>FAIL_CODE,'msg'=>OPERATE_FAIL,'status'=>FAIL_STATUS],'json');
        }
    }
    /**
     * 用户收藏/取消收藏接口
     * status 收藏状态 0：未收藏；1：以收藏  (传0收藏，传1取消收藏)
     * user_id 用户ID
     * news_id 新闻id
     * type 收藏类型 0:普通新闻,1:快讯
     * flash_id 快讯id
     */
    public function collection(){

        $request_info = get_input();
        $request = array();
        //判断登陆token
        $this->__isToken();
        //获取header头信息
        $request = $this->__header();
        $request_info['user_id'] = $request['uid'];

        if(!isset($request_info['type'])){
            return apiSend(['code'=>FAIL_CODE,'msg'=>LACK_PARAM,'status'=>FAIL_STATUS],'json');
        }
        if(!isset($request_info['status'])){
            return apiSend(['code'=>FAIL_CODE,'msg'=>LACK_PARAM,'status'=>FAIL_STATUS],'json');
        }
        if(!isset($request_info['id'])){
            return apiSend(['code'=>FAIL_CODE,'msg'=>LACK_PARAM,'status'=>FAIL_STATUS],'json');
        }
        //收藏
        if($request_info['status'] == 0){
            if($request_info['type'] == 0){
                $data = [
                    'user_id'   => intval($request_info['user_id']),
                    'news_id'   => intval($request_info['id']),
                    'type'      => intval($request_info['type']),
                    'create_time'   => time(),
                    'update_time'   => time(),
                    'status'    => 1,
                ];
            }elseif($request_info['type'] == 1){
                $data = [
                    'user_id'   => intval($request_info['user_id']),
                    'flash_id'  => intval($request_info['id']),
                    'type'      => intval($request_info['type']),
                    'create_time'   => time(),
                    'update_time'   => time(),
                    'status'    => 1,
                ];
            }


            $model = new NewIndexModel();

            $isCollection = $model->isCollection($data);

            if($isCollection){
                //存在记录收藏
                $request_info['isCollection'] = 1;
                $Collection = $model->noCollection($request_info);
            }else{
                //添加记录收藏
                $Collection = $model->collection($data);
            }

            if($request_info['type'] == 1){
                //增加收藏量
                $model->addCollection($request_info);
                $send = [
                    'news_id' => $request_info['id'],
                    'type'    => 1
                ];
                swoole_client($send);
            }


            if($Collection){
                return apiSend(['code'=>SUCCESS_CODE,'msg'=>COLLECTION_SUCCESS,'status'=>SUCCESS_STATUS],'json');
            }else{
                return apiSend(['code'=>FAIL_CODE,'msg'=>COLLECTION_ERROR,'status'=>FAIL_STATUS],'json');
            }
        //取消收藏
        }elseif ($request_info['status'] == 1){

            $model = new NewIndexModel();

            $request_info['isCollection'] = 0;
            $isCollection = $model->noCollection($request_info);

            //减少收藏量
            if($request_info['type'] == 1){
                $model->addCollection($request_info);
                $send = [
                    'news_id' => $request_info['id'],
                    'type'    => 1
                ];
                swoole_client($send);
            }

            if($isCollection){
                return apiSend(['code'=>SUCCESS_CODE,'msg'=>NOCOLLECTION_SUCCESS,'status'=>SUCCESS_STATUS],'json');
            }else{
                return apiSend(['code'=>FAIL_CODE,'msg'=>NOCOLLECTION_ERROR,'status'=>FAIL_STATUS],'json');
            }
        }
    }
    /**
     * 收藏列表
     * header uid token
     * page 第几页
     * pageSize 一页显示数量
     */
    public function collectionList()
    {
        $request_info = get_input();

        $page = isset($request_info['page']) ? $request_info['page'] : 1;
        $pageSize = isset($request_info['pageSize']) ? $request_info['pageSize'] : 10;

        //判断登陆token
        $this->__isToken();
        //获取header头信息
        $request = $this->__header();
        $user_id = $request['uid'];

        //获取用户收藏信息
        $model = new NewIndexModel();
        $collectionList = $model->collectionList($user_id,$page,$pageSize);

        $col_list = '';
        if(isset($collectionList) && $collectionList){

            //新闻id和快讯id分开
            $news_arr = array();
            $flash_arr = array();

            foreach ($collectionList as $k=>$v){
                if($v['type'] == 0){
                    $news_arr[] = $v['news_id'];
                }else{
                    $flash_arr[] = $v['flash_id'];
                }
            }

            if($news_arr){
                //调用新闻model，获得新闻列表的相关信息
                $newsModel = new NewsModel();

                $field = 'id as news_id,title,summary,comments_num,publish_time,source_type,column_ids,detail_type,source';
                $where = ['is_delete'=>0,'is_show'=>1,'id'=>['in',$news_arr]];

                $query = compact('where','field');
                $list = $newsModel->index('newsList',$query);
            }

            if($flash_arr){
                //获取快讯相关信息
                $detail = $model->collectionListDetail($flash_arr,$type=1);
            }

            if(isset($list) || isset($detail)){
                //排序
                $col_list = array();
                foreach ($collectionList as $key=>$val){
                    if($val['type'] ==0){

                        if($list){
                            foreach ($list as $k1=>$v1){
                                if($val['news_id'] == $v1['news_id']){
                                    $v1['new_fla'] = 0;//新闻
                                    $col_list[] = $v1;
                                }
                            }
                        }

                    }elseif($val['type'] == 1){

                        if($detail){
                            foreach ($detail as $k2=>$v2){
                                if($val['flash_id'] == $v2['id']){
                                    $v2['new_fla'] = 1;//快讯
                                    $v2['date'] = date('Y-m-d H:i',$v2['update_time']);

                                    switch ($v2['type']){
                                        case 11:
                                            $v2['news_type'] =[
                                                [
                                                    'title' => '微信'
                                                ]
                                            ];
                                            break;
                                        case 4:
                                            $v2['news_type'] =[
                                                [
                                                    'title' => '微博'
                                                ]
                                            ];
                                            break;
                                        case 2:
                                            $v2['news_type'] =[
                                                [
                                                    'title' => '股吧'
                                                ]
                                            ];
                                            break;
                                        case 0:
                                            $v2['news_type'] =[
                                                [
                                                    'title' => '消息'
                                                ]
                                            ];
                                            break;
                                        case 101:
                                            $v2['news_type'] =[
                                                [
                                                    'title' => '新闻'
                                                ]
                                            ];
                                            break;
                                        case 102:
                                            $v2['news_type'] =[
                                                [
                                                    'title' => '研报'
                                                ]
                                            ];
                                            break;
                                        case 103:
                                            $v2['news_type'] =[
                                                [
                                                    'title' => '公告'
                                                ]
                                            ];
                                            break;
                                        case 104:
                                            $v2['news_type'] =[
                                                [
                                                    'title' => '电报'
                                                ]
                                            ];
                                            break;
                                    }

                                    $col_list[] = $v2;
                                }
                            }
                        }

                    }
                }
            }

        }


        if($col_list){
            return apiSend(['code'=>SUCCESS_CODE,'msg'=>SUCCESS_MESSAGE,'status'=>SUCCESS_STATUS,'data'=>$col_list],'json');
        }else{
            return apiSend(['code'=>SUCCESS_CODE,'msg'=>NULL_MESSAGE,'status'=>SUCCESS_STATUS],'json');
        }
    }
    /**
     * 快讯接口
     * header uid token
     * page 第几页
     * pageSize 一页显示数量
     */
    public function newsFlash(){

        $request_info = get_input();

        $page = isset($request_info['page']) ? $request_info['page'] : 1;
        $pageSize = isset($request_info['pageSize']) ? $request_info['pageSize'] : 10;

        //获取header头信息
        $header = $this->__header();

        if(isset($header['uid']) && $header['uid']){
            //判断登陆token
            $this->__isToken();

            $uid = $header['uid'];
        }

        $model = new NewIndexModel();
        $res = $model->newsFlash($page,$pageSize);

        if(isset($res) && $res){
            foreach ($res as &$v){
                //判断用户是否收藏
                $data['flash_id'] = $v['id'];
                if(!isset($uid)){
                    $v['status'] = 0;
                }else{
                    $data['user_id'] = intval($uid);
                    $data['type'] = 1;
                    $isCollection = $model->isCollection($data);
                    if($isCollection['status'] == 1){
                        $v['status'] = 1;//已收藏
                    }else{
                        $v['status'] = 0;//未收藏
                    }
                }
                switch ($v['type']){
                    case 11:
                        $v['type'] = '微信';
                        break;
                    case 4:
                        $v['type'] = '微博';
                        break;
                    case 2:
                        $v['type'] = '股吧';
                        break;
                    case 0:
                        $v['type'] = '消息';
                        break;
                    case 101:
                        $v['type'] = '新闻';
                        break;
                    case 102:
                        $v['type'] = '研报';
                        break;
                    case 103:
                        $v['type'] = '公告';
                        break;
                    case 104:
                        $v['type'] = '电报';
                        break;
                }
            }
        }

        if($res){
            return apiSend(['code'=>SUCCESS_CODE,'msg'=>SUCCESS_MESSAGE,'status'=>SUCCESS_STATUS,'data'=>$res],'json');
        }else{
            return apiSend(['code'=>SUCCESS_CODE,'msg'=>NULL_MESSAGE,'status'=>SUCCESS_STATUS],'json');
        }
    }
    /**
     * 电子报列表
     * date Y-m-d
     */
    public function eNewsPaper(){

        $request_info = get_input();

        if(!isset($request_info['date'])){
            return apiSend(['code'=>FAIL_CODE,'msg'=>LACK_PARAM,'status'=>FAIL_STATUS],'json');
        }

        $model = new NewIndexModel();
        $res = $model->eNewsPaper($request_info['date']);

        if(!$res){
            $cron = new NewsPaper();
            $exec = $cron->eNewsPaper($request_info['date']);

            if(!isset($exec)){
                return apiSend(['code'=>SUCCESS_CODE,'msg'=>SUCCESS_MESSAGE,'status'=>SUCCESS_STATUS,'data'=>[]],'json');
            }
            $res = $model->eNewsPaper($request_info['date']);
        }

        return apiSend(['code'=>SUCCESS_CODE,'msg'=>SUCCESS_MESSAGE,'status'=>SUCCESS_STATUS,'data'=>$res],'json');

    }

    /**
     * 电子报标题
     * @return \think\response\Json|\think\response\Jsonp|\think\response\Xml
     */
    public function ePaperDetail()
    {
        $request_info = get_input();

        if(!isset($request_info['date'])){
            return apiSend(['code'=>FAIL_CODE,'msg'=>LACK_PARAM,'status'=>FAIL_STATUS],'json');
        }
        if(!isset($request_info['head'])){
            return apiSend(['code'=>FAIL_CODE,'msg'=>LACK_PARAM,'status'=>FAIL_STATUS],'json');
        }

        $model = new NewIndexModel();
        $res = $model->ePaperDetail($request_info);

        if(isset($res) && $res){
            foreach ($res as $k=>&$v){
                $v['title'] = str_replace('<BR/>','',$v['title']);
            }
        }

        if($res){
            return apiSend(['code'=>SUCCESS_CODE,'msg'=>SUCCESS_MESSAGE,'status'=>SUCCESS_STATUS,'data'=>$res],'json');
        }else{
            return apiSend(['code'=>FAIL_CODE,'msg'=>OPERATE_FAIL,'status'=>FAIL_STATUS],'json');
        }
    }



















}
































