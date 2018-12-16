<?php
namespace app\common\model;
use think\Model;
use think\DB;
use app\common\model\Column;
/**
 * @ClassName:    Name 
 * @Description:  Detail 
 * @Created by:   [villager] 
 * @DateTime:     2018-05-28T14:15:10+0800
 */
Class News extends Model 
{
    protected $start = 0; //查询起始位置
    protected $offset = 10;   //查询条数
    protected $where; //查询条件
    protected $self_field = '*'; //查询字段
    protected $order = 'id desc'; //排序字段
	use \app\common\traits\Head;
    /**
     * [getPublishTimeAttr 发布时间获取器]
     * @AuthorHTL village
     * @DateTime  2018-06-07T15:16:26+0800
     * @return    [type]                   [description]
     */
    public function getPublishTimeAttr($value) {
        $interval = time() - $value; //新闻发布时间与当前时间的时间差
        if($interval / 86400 > 1) {  //如果发布时间与当前时间相差大于一天则返回完整时间格式
            return date('Y-m-d H:i',$value);
        }else if(1 <= $hour = intval($interval / 3600)){ //如果发布时间与当前时间相差小于一天大于一小时则返回相差小时数
            return $hour . '小时前';
        }else if(1 <= $second = intval($interval / 60)) { //如果发布时间与当前时间相差小于一小时大于一分钟则返回相差分钟数
            return $second . '分钟前';
        }else {
            return '刚刚'; //如果发布时间与当前时间相差小于一分钟则返回刚刚
        }
    }
    /**
     * [getPublishTimeAttr 发布时间获取器]
     * @AuthorHTL village
     * @DateTime  2018-06-07T15:16:26+0800
     * @return    [type]                   [description]
     */
    public function getDetailAttr($value) {
       $value = html_entity_decode(html_entity_decode($value));
       return $value;
    }
    /**
     * [getColumnIdsAttr 新闻栏目获取器,根据column_ids查找对应的栏目标题]
     * @AuthorHTL
     * @DateTime  2018-06-07T16:21:36+0800
     * @param     [type]                   $value [description]
     * @return    [type]                          [description]
     */
    public function getColumnIdsAttr($value){
        $columnModel = new Column(); // 栏目模型
        if(strlen($value) > 1) {     //计算字符串长度 有 1 或 1,2的情况
            $ids = explode(',', $value);
            $where = ['id'=> ['in',$value]];
        }else {
            $where = ['id' => $value];
        }
        $field = 'id,parent_id,title'; // 栏目标题
        $start = 0;
        $offset = 4;
        $order = 'id asc';
        $query = compact('field','where','start','offset','order');
        $list = $columnModel -> index('columnList',$query);
        foreach ($list as $key => $value) {
            $list[$key] = $this->findColumns($columnModel,$value);
        }
        $list = array_unique($list);
        return $list;
    }
    /**
     * [findColumns 递归查询顶级栏目]
     * @AuthorHTL
     * @DateTime  2018-06-13T19:09:36+0800
     * @param     [type]                   $model [description]
     * @param     [type]                   $value [description]
     * @return    [type]                          [description]
     */
    public function findColumns($model,$value) {
        if($value['parent_id'] != 0) {
            $where = ['id' => $value['parent_id']];
            $field = 'id,parent_id,title'; // 栏目标题
            $query = compact('field','where');
            $info = $model -> index('columnInfo',$query);
            $this->findColumns($model,$info);
        }
        return $value;
    }
    /**
    *新闻列表资源
    */
     public function listSource() {
        $where = ['is_delete' => 0,'type' => 1];
        $field = 'id,news_id,source_path,detail,type';
        return $this -> hasMany('NewsSource','news_id','news_id') -> where($where) -> field($field);
     }
     /**
    *新闻详情图片资源
    */
     public function infoSource() {
        $where = ['is_delete' => 0,'type' => 1];
        $field = 'id,news_id,source_path,detail,type';
        return $this -> hasOne('NewsSource','news_id','news_id') -> where($where) -> field($field);
     }
     /**
    *新闻详情视频资源
    */
     public function videoSource() {
        $where = ['is_delete' => 0,'type' => 4];
        $field = 'id,news_id,source_path,detail,type';
        return $this -> hasOne('NewsSource','news_id','news_id') -> where($where) -> field($field);
     }
     /**
    *新闻详情资源
    */
     public function newsSource() {
        $where = ['is_delete' => 0,'type' => 1];
        $field = 'id,news_id,source_path';
        return $this -> hasMany('NewsSource','news_id','id') -> where($where) -> field($field);
     }
     /**
    *新闻详情资源列表
    */
     public function infoSourceList() {
        $where = ['is_delete' => 0,'type' => 3];
        $field = 'id,news_id,source_path,detail,type';
        return $this -> hasMany('NewsSource','news_id','news_id') -> where($where) -> field($field);
     }
	/**
	 * 新闻列表
	 * @author    [villager]
	 * @return    [type]     [description]
	 * @DateTime  2018-05-16T14:51:05+0800
	*/
    private function newsList() {
        $list = $this -> all(function($query){
        	$query -> where($this->where) -> field($this->self_field) -> order($this->order) -> limit($this->start,$this->offset) -> with('listSource');
        });
        return $list;
    }
    /**
     * 新闻列表
     * @author    [villager]
     * @return    [type]     [description]
     * @DateTime  2018-05-16T14:51:05+0800
    */
    private function newsSearch() {
        $list = $this -> all(function($query){
            $query -> where($this->where)-> whereOr($this->where) -> field($this->self_field) -> order($this->order) -> limit($this->start,$this->offset) -> with('listSource');
        });
        return $list;
    }
    /**
     * 添加新闻
     * @author    [villager]
     * @return    [type]     [description]
     * @DateTime  2018-05-16T14:51:05+0800
    */
    private function newsAdd() {
        $result = $this -> save($this->data);
        return $result;
    }
    /**
	 * 新闻详情
	 * @author    [villager]
	 * @return    [type]     [description]
	 * @DateTime  2018-05-16T14:51:05+0800
	*/
    private function newsDetail() {
    	 $info = $this -> get(function($query){
            $query -> where($this->where) -> field($this->self_field);
        });
        return $info;
    }
    /**
     * [newsUpdate 新闻更新]
     * @AuthorHTL  villager
     * @DateTime  2018-06-08T15:03:57+0800
     * @return    [type]                   [description]
     */
    private function newsUpdate() {
        $info = $this -> where($this->where) -> update($this->data);
        return $info;
    }
    /**
     * [increase 自增处理]
     * @AuthorHTL
     * @DateTime  2018-06-08T19:56:23+0800
     * @return    [type]                   [description]
     */
    private function increase() {
        $info = $this -> where($this->where) -> setInc($this->self_field);
        return $info;
    }
    /**
     * [decrease 自减处理]
     * @AuthorHTL
     * @DateTime  2018-06-08T19:56:48+0800
     * @return    [type]                   [description]
     */
    private function decrease() {
        $info = $this -> where($this->where) -> setDec($this->self_field);
        return $info;
    }
    /**
     * [newsDelete 删除新闻]
     * @AuthorHTL
     * @DateTime  2018-06-29T11:49:51+0800
     * @return    [type]                   [description]
     */
    private function newsDelete() {
        $result = $this -> save(['is_delete' => 1],$this->where);
        return $result;
    }
}