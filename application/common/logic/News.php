<?php
namespace app\common\logic;
use app\common\logic\Base;
use app\common\model\Hot;
use app\common\model\Column;
use app\common\model\Related;
use app\common\model\NewsSource;
use app\common\model\Comment;
use app\common\model\News as NewsModel;
use app\common\logic\UserPraise as UserPraiseLogic;
use app\common\logic\UserCollection as UserCollectionLogic;
/**
 * @ClassName:    News 
 * @Description:  新闻类 
 * @Created by:   [villager] 
 * @DateTime:     2018-05-28T14:13:06+0800
 */
Class News extends Base 
{
	protected $page = 1;
	protected $offset = 10;
	protected $field = 'id as news_id,title,summary,comments_num,publish_time,source_type,column_ids,detail_type,source';
	protected $newsDetail;
	/**
	 * [initialize:  初始化绑定news模型] 
	 * @Created by:   [villager] 
	 * @DateTime:     2018-05-16T15:16:16+0800
	 */
	protected function initialize() {
        parent::initialize();
        $this->model = new NewsModel();
        $this->newsSourceModel = new NewsSource();
        $this->validate = validate('News');
    }
	/**
	 * 新闻列表或二级栏目列表
	 * @author    [villager]
	 * @param     [type]     $param [值]
	 * @return    [type]     [description]
	 * @DateTime  2018-05-28T14:13:06+0800
	*/
	public function newsList($param)
	{
		$offset = !empty($param['offset']) ? $param['offset'] : $this->offset; // 偏移量
		$field  = !empty($param['field'])  ? $param['field']  : $this->field;//查询字段
		$start  = ((!empty($param['page']) ? $param['page']   : $this->page) - 1) * $offset;
		$order  = !empty($param['order']) ? $param['order'] : 'id desc'; //排序方式
		$column_id = !empty($param['column_id']) ? $param['column_id'] : 1;//栏目id
		//查询是否有二级栏目
		$query['where'] = ['is_delete' => 0,'parent_id' => $column_id,'is_show' => 1];
		$query['field'] = 'id as news_id,title,pic_path,sort,column_url';
		$query['order'] = 'sort desc';
		$query['start'] = $start;
		$query['offset'] = $offset;
		$columnModel = new Column();
		$list  = $columnModel->index('columnList',$query); //二级栏目列表
		if(!empty($list)){
			foreach ($list as $key => $value) {
				$list[$key]['detail'] = null;
				$list[$key]['comments_num'] = 0;
				$list[$key]['publish_time'] = null;
				$list[$key]['source_type'] = 0; //如果是二级栏目列表 source_type = 0
				$list[$key]['listSource'] = [['source_path'=>$value['pic_path']]];
				unset($list[$key]['pic_path']);
			}
			return $list;
		}
		//查询该栏目下总共可显示的条数、新闻显示的天数
		$column_query['where'] = ['id' => $column_id];
		$column_query['field'] = 'id,day_num,news_num';
		$column_info = $columnModel -> index('columnInfo',$column_query);
		if($column_info['news_num'] != 0) { //如果显示的条数为0，不做限制
			if($start > $column_info['news_num']) {
				$list = [];
				return $list;
			}
		}
		//查询热点表
		// $hot = [];
		// $hot_query['where'] = ['is_delete' => 0];
		// $hot_query['field'] = ['id,news_ids'];
		// $hotModel = new Hot();
		// $hot = $hotModel->index('hotInfo',$hot_query);
		// $hot_ids = explode(',', $hot['news_ids']); //热点新闻id数组
		//查询新闻列表
		$where = ['is_delete'=>0,'is_show'=>1,'is_banner' => 0];//默认查询条件
		$where[] = ['exp',"FIND_IN_SET($column_id,column_ids)"];
		if(isset($param['min_news_id'])) { // 当前页最小的新闻id ,防止数据重复
			$min_news_id = $param['min_news_id'];
			$where['id'] = ['lt',"$min_news_id"];
		}
		if($column_info['day_num'] != 0) { //如果新闻显示的天数为0，不做限制
			$expired_time = time() - $column_info['day_num'] * 24 * 3600;
			$where['publish_time'] = ['gt',"$expired_time"];
		}
		$query = compact('order','where','field','offset');
		$list = $this->model->index('newsList',$query);
		// if(!empty($list)) { //如果列表不为空，判断每条新闻是否是热点
		// 	foreach ($list as $key => $value) {
		// 		$columns = [];
		// 		$new_columns = [];
		// 		//排除热点标签
		// 		if(!empty($value['column_ids'])) {
		// 			foreach ($value['column_ids'] as $k => $val) {
		// 				if($val['title'] != '热点') {
		// 					$columns[] = ['id' => $val['id'],'title' => $val['title'],'parent_id'=>$val['parent_id']];
		// 				}
		// 			}
		// 			//判断是否是热点新闻
		// 			if(in_array($value['news_id'], $hot_ids)){ //如果该新闻是热点，拼接上热点
		// 				array_unshift($columns, ['id' => 0,'title' => '热点','parent_id' => 0]);
		// 			}
		// 			//排除本身标签
		// 			$length = count($columns);
		// 			if($length > 2) {
		// 				$i = 0;
		// 				foreach ($columns as $k => $val) {
		// 					if($val['id'] != $column_id && $i < 2) {
		// 						$new_columns[] = $columns[$k];
		// 						$i++;
		// 					}
		// 				}
		// 			}else {
		// 				$new_columns = $columns;
		// 			}
		// 		}
		// 		$list[$key]['columns'] = $new_columns;
		// 		unset($list[$key]['column_ids']);
		// 	}
		// }
		return $list;
	}
	/**
	 * 新闻详情
	 * @author    [villager]
	 * @param     [type]     $param [值]
	 * @return    [type]     [description]
	 * @DateTime  2018-05-28T14:13:06+0800
	*/
	public function newsDetail($param)
	{
		//如果id不存在，返回错误信息
		if(!isset($param['id'])) apiSend(['code'=>FAIL_CODE,'status'=>ERROR_STATUS,'msg'=>LACK_PARAM],'json',true);
		$news_id = $param['id'];//新闻id
		//查询的字段
		$field = 'id as news_id,title,author,source,publish_time,summary,detail,praise_num,praise_num,comments_num,collect_num,allow_comment,allow_transmit,allow_ad,source_type,detail_type,is_recommend,audio';
		$where = ['id'=>$news_id]; //查询条件
		$query = compact('field','where');
		//新闻详情
		$info = $this->model->index('newsDetail',$query);//执行查询
		$send = [
		    'news_id' => $news_id,
		    'type'    => 1
		];
        //阅读量(点击量) + 1
        $increase_query['where'] = $where;
        $increase_query['field'] = 'click_num';
        $result = $this->model->index('increase',$increase_query);
        swoole_client($send);//异步计算排行
		$userPraiseLogic        = new UserPraiseLogic();
		$info['is_praise']      = $userPraiseLogic -> isPraise($param);         //新闻是否点赞状态
		$userCollectionLogic    = new UserCollectionLogic();
		$info['is_collection']  = $userCollectionLogic -> isCollection($param); //新闻是否收藏状态
		$info['top_pic']        = null;                                         // 新闻详情顶部图片
		$info['sourceList'] = [];                                           //新闻资源列表及描述
		$info['related_news']   = [];                                           // 推荐相关新闻
		$commentModel = new Comment();
		$comment_query['where'] = ['news_id'=>$news_id,'is_show' => 1,'is_delete' => 0];
		$info['comments_num'] = $commentModel -> index('count',$comment_query);
		$info['detail'] = '<html><head><meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no"><style>img{max-width: 100%; width:auto; height:auto;} .reduce-font p{ line-height:26px!important;font-size:17px!important;}</style></head><body class="reduce-font" style="color: RGBA(78, 78, 78, 1);font-size: 0.16rem;">'.$info['detail'].'</div></body></html>';
		if($info['is_recommend'] != 0) { // 推荐相关新闻查询
			$relatedModel = new Related();
			$query['where'] = ['news_id' => $news_id];
			$query['field'] = ['id,related_ids'];
			$query['variables']  = $info['is_recommend'];
			$related_info = $relatedModel->index('relatedInfo',$query);
			$related_news = [];
			if(!empty($related_info['related_ids'])) {
				$related_ids = explode(',', $related_info['related_ids']);
				// 取推荐的新闻条数
				$related_ids = array_slice($related_ids, 0,$info['is_recommend']);
		        $where = ['id' => ['in',$related_ids],'is_delete' =>0,'is_show' => 1];
		        $field = ['id as news_id,title,summary,comments_num,publish_time,source_type,column_ids,source'];
		        $query = compact('start','offset','where','field');
		        $list = $this->model->index('newsList',$query);
		        if(!empty($list)) { //如果列表不为空，判断每条新闻是否是热点
		            foreach ($list as $key => $value) {
		                $columns = [];
		                //排除热点标签
		                if(!empty($value['column_ids'])) {
		                    //排除本身标签
		                    $columns = $value['column_ids'];
		                }
		                $list[$key]['columns'] = $columns;
		                unset($list[$key]['column_ids']);
		            }
		        }
		        $related_news = $list;
			}
			$info['related_news'] = $related_news;
		}
		//获取新闻资源(图片或视频)
		if($result) {
			if($info['source_type'] == 2) { //如果是资源类型2或4，即单图形式或视频，则在详情页顶部显示此单图
				$info->infoSource;
				$info['top_pic'] = $info['infoSource']['source_path'];
				unset($info['infoSource']);
			}else if($info['source_type'] == 4) {
				$info->infoSource;
				$info['top_pic'] = $info['infoSource']['source_path'];
				$info->videoSource;
				$info['video'] = $info['videoSource']['source_path'];
				unset($info['infoSource']);
				unset($info['videoSource']);
			}
			//新闻详情为横向滚动文本流时
			if($info['detail_type'] == 2) {
				$info->infoSourceList;
				$source_list = [];
				foreach ($info['infoSourceList'] as $key => $value) {
					$source_list[] = [
						'source_id' => $value['id'],
						'source_path' => $value['source_path'], //图片路径
						'detail'      => $value['detail']       //描述
					];	
				}
				$info['sourceList'] = $source_list;
				unset($info['infoSourceList']);
			}
			return $info;
		}
		apiSend(['code'=>FAIL_CODE,'status'=>ERROR_STATUS,'msg'=>OPERATE_FAIL],'json',true);
	}
	/**
	 * [hotNews 热点新闻]
	 * @AuthorHTL
	 * @DateTime  2018-06-13T17:05:49+0800
	 * @param     [type]                   $param [description]
	 * @return    [type]                          [description]
	 */
	public function hotNews($param)
	{
		$offset = !empty($param['offset']) ? $param['offset'] : $this->offset; // 偏移量
		$field  = !empty($param['field'])  ? $param['field']  : $this->field;//查询字段
		$start  = ((!empty($param['page']) ? $param['page']   : $this->page) - 1) * $offset;
		$order  = !empty($param['order']) ? $param['order'] : 'id desc'; //排序方式
		//查询热点表
		$hot = [];
		$hot_query['where'] = ['is_delete' => 0];
		$hot_query['field'] = ['id,news_ids'];
		$hotModel = new Hot();
		$hot = $hotModel->index('hotInfo',$hot_query);
		$hot_ids = $hot['news_ids']; //热点新闻id数组
		$where = ['id' => ['in',"$hot_ids"],'is_delete' => 0,'is_show' => 1];
		if(!empty($param['min_news_id'])) { // 当前页最大的新闻id ,防止数据重复
			$min_news_id = $param['min_news_id'];
			$where['id'] = ['lt',"$min_news_id"];
		}
		$query = compact('order','where','field','start','offset');
		$list = $this->model->index('newsList',$query);
		$columns = [];
		$hot_columns = ['title' => '热点'];
		if(!empty($list)) { //如果列表不为空，排除热点标签,否则取出原来栏目
			foreach ($list as $key => $value) {
				$columns = $value['column_ids'];
				foreach ($columns as $k => $val) {
					if($val['title'] == '热点') {
						unset($columns[$k]);
					}
				}
				$list[$key]['columns'] = array_values($columns);
				unset($list[$key]['column_ids']);
			}
		}
		return $list;
	}
	/**
	 * [newsSearch 新闻搜索]
	 * @AuthorHTL
	 * @DateTime  2018-06-07T16:56:41+0800
	 * @return    [type]                   [description]
	 */
	public function newsSearch($param)
	{
		if(!isset($param['keywords'])) apiSend(['code'=>FAIL_CODE,'status'=>ERROR_STATUS,'msg'=>LACK_PARAM],'json',true); //缺少关键字
		$offset = isset($param['offset']) ? $param['offset'] : $this->offset; // 偏移量
		$start  = ((isset($param['page']) ? $param['page']   : $this->page) - 1) * $offset; //起始位置
		$where = ['is_delete'=>0,'is_show'=>1];//默认查询条件
		$keywords = $param['keywords'];// 关键字
		$field = $this->field;
		$where[] = ['exp',"match(title,detail,summary,keywords,source) against ('$keywords')"];
		if(isset($param['min_news_id'])) { // 当前页最小的新闻id ,防止数据重复
			$min_news_id = $param['min_news_id'];
			$where['id'] = ['lt',"$min_news_id"];
		}
		$query = compact('where','field','start','offset');
		$list = $this->model->index('newsList',$query);
		if(empty($list)) {
			$where = ['is_delete'=>0,'is_show'=>1,'title|detail|summary|keywords|source' => ['like','%'.$keywords.'%']];//模糊查询条件;
			if(isset($param['min_news_id'])) { // 当前页最小的新闻id ,防止数据重复
				$min_news_id = $param['min_news_id'];
				$where['id'] = ['lt',"$min_news_id"];
			}
			$query = compact('where','field','start','offset');
			$list = $this->model->index('newsList',$query);
		}
		return $list;
	}
	/**
	 * [newsTransmit 新闻转发]
	 * @AuthorHTL
	 * @DateTime  2018-06-28T10:32:21+0800
	 * @param     [type]                   $param [description]
	 * @return    [type]                          [description]
	 */
	public function newsTransmit($param)
	{
		$result['status'] = true;
		$query['where']   = ['id' => $param['news_id']];
		$query['field']   = 'transmit_num';
		$result['status'] = $this->model->index('increase',$query);
		$send = [
		    'news_id' => $param['news_id'],
		    'type'    => 1
		];
        swoole_client($send);//异步计算排行
		if($result['status']) {
			$result['data']['url'] = config('zqrb')['domain'].'/newsDetail/'.$param['news_id'];
		}
		return $result;
	}
	/**
	 * [newsAdd 添加新闻]
	 * @AuthorHTL
	 * @DateTime  2018-06-28T17:45:28+0800
	 * @return    [type]                   [description]
	 */
	public function newsAdd($param)
	{
		//验证添加新闻参数
		if(!$this->validate->check($param)){
			$error_info = $this->validate->getError();
			apiSend(['code'=>$error_info['code'],'msg'=>$error_info['msg'],'status'=>FAIL_STATUS],'json',true);
		}
		$where = ['origin_id' => $param['origin_id'],'is_delete' => 0];
		$field = 'id,origin_id';
		$query = compact('where','field');
		$info  = $this->model->index('newsList',$query);
		if(!empty($info)) { //不能重复添加同一条新闻
			apiSend(['code'=>NEWS_EXIST_CODE,'msg'=>NEWS_EXIST_MSG,'status'=>FAIL_STATUS],'json',true);
		}
		$detail = null;
		$detail_type = isset($param['detail_type']) ? $param['detail_type']   : 1;
		if($detail_type == 1) {
			$detail = urldecode($param['detail']);
		}
		$data = [
			'origin_id'      => $param['origin_id'],//新闻源id
			'column_ids'     => '1', //栏目标签
			'title'          => urldecode($param['title']),    //新闻标题
			'author'         => urldecode($param['author']),   //新闻作者
			'keywords'       => urldecode($param['keywords']), //新闻关键词
			'summary'        => urldecode($param['summary']),  //新闻摘要
			'detail'         => html_entity_decode(html_entity_decode($detail)),                       //新闻详情
			'source'         => urldecode($param['source']),   //新闻来源
			'detail_type'    => $detail_type,                  //新闻详情文本类型 1 正常文本流类型 2 横向滚动文本流
			'click_num'      => isset($param['click_num'])     ? $param['click_num']     : 0, //新闻点击量
			'collect_num'    => isset($param['collect_num'])   ? $param['collect_num']   : 0, //新闻收藏量
			'praise_num'     => isset($param['praise_num'])    ? $param['praise_num']    : 0, //新闻点赞量
			'comments_num'   => isset($param['comments_num'])  ? $param['comments_num']  : 0, //新闻评论量
			'transmit_num'   => isset($param['transmit_num'])  ? $param['transmit_num']  : 0, //新闻转发量
			'allow_comment'  => isset($param['allow_comment']) ? $param['allow_comment'] : 0, //是否可评论 0 否 1 是
			'is_show'        => isset($param['is_show'])       ? $param['is_show']       : 0, //新闻是否直接显示 0 否 1 是
			'publish_time'   => $param['publish_time'],                                       //新闻发布时间
			'allow_ad'       => isset($param['is_ad'])         ? $param['is_ad']         : 0, //是否显示广告 0 否 1 是
			'allow_transmit' => isset($param['is_repost'])     ? $param['is_repost']     : 0, //是否可转发 0 否 1 是
			'is_recommend'   => isset($param['is_recommend'])  ? $param['is_recommend']  : 0, //是否推送相关新闻 0 否 1 是
			// 'is_banner'      => isset($param['is_banner'])     ? $param['is_banner']     : 0, //是否设为banner 0 否 1 是
			'is_banner'      => 0, //是否设为banner 0 否 1 是
			'create_time'    => time(),
			'update_time'    => time()
		];
		$query['data'] = $data;
		$result['status'] = $this->model->index('newsAdd',$query);
		$news_id = $this->model->id;//新增新闻id
		if($detail_type == 2) { // 新闻详情为横向滚动文本流
			$detail = urldecode($param['detail']);
			if(!is_array($detail)){
				$detail = json_decode($detail,true);
			}
			$news_source = [];
			foreach ($detail as $key => $value) {
				$news_source[] = [
					'news_id' => $news_id,
					'source_path' => $value['source_path'],
					'detail' => $value['description'],
					'type' => 3
				];
			}
			$source_query['data'] = $news_source;
			$this->newsSourceModel->index('sourceAdd',$source_query);
			
		}
		$data = [
		    'type' => 2,
		    'news_id' => $news_id,
		    'title'   => $param['title']
		];
		swoole_client($data);
		return $result;
	}
	/**
	 * [newsDelete description]
	 * @AuthorHTL
	 * @DateTime  2018-06-29T11:38:37+0800
	 * @param     [type]                   $param [description]
	 * @return    [type]                          [description]
	 */
	public function newsDelete($param)
	{
		if(!isset($param['origin_id']) || empty($param['origin_id'])) {
			apiSend(['code'=>LACK_ORIGINID_CODE,'msg'=>LACK_ORIGINID_MSG,'status'=>FAIL_STATUS],'json',true);
		}
		$where = ['origin_id' => $param['origin_id'],'is_delete' => 0];
		$field = 'id,origin_id';
		$query = compact('where','field');
		$info  = $this->model->index('newsList',$query);
		if(empty($info)) { //不能重复添加同一条新闻
			apiSend(['code'=>NEWS_NOT_EXIST_CODE,'msg'=>NEWS_NOT_EXIST_MSG,'status'=>FAIL_STATUS],'json',true);
		}
		$query = [];
		$query['where'] = ['origin_id' => $param['origin_id']];
		$result['status'] = $this->model->index('newsDelete',$query);
		return $result;
	}
}