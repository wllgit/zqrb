<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/5/30
 * Time: 9:58
 */
namespace app\admin\controller;
use app\admin\model\Advertisements;
use app\admin\model\Column;
use app\admin\model\NewsModel;
use \think\Db;
use \think\Request;
use app\admin\model\BannerModel;
use app\admin\model\NewsSource;
use OSS\OssClient;
use OSS\Core\OssException;
use Aliyun\DySDKLite\SignatureHelper;

ini_set("display_errors", "on"); // 显示错误提示，仅用于测试时排查问题
// error_reporting(E_ALL); // 显示所有错误提示，仅用于测试时排查问题
set_time_limit(0); // 防止脚本超时，仅用于测试使用，生产环境请按实际情况设置
header("Content-Type: text/plain; charset=utf-8"); // 输出为utf-8的文本格式，仅用于测试
class Newsmanage extends Base{

//    //新闻列表
//    public function newslist(){
//        $new = new NewsModel();
//        $column = new Column();
//        $column_id = input('param.name');//新闻分类 name字段表示分类
//        $field  = 'n.id,title,column_ids,author,summary,sort,source_type,is_banner,is_show,n.update_time,allow_transmit,allow_ad,source,a.name';
//        //栏目列表
//        $c_list_arr = ['c.is_show'=>1,'c.is_delete'=>0,'parent_id'=>0];
//        $first_list = $column->getColumns($c_list_arr,'c.*');//所有栏目
//        $columnArr = [];
//        // 搜索条件
//        $param = input();
//        if(!empty($param['title'])) {
//            $news_where ['title'] = ['like','%'.$param['title'].'%'];
//            $this->assign('title',$param['title']);
//        }
//        if(!empty($param['source'])) {
//            $news_where ['source'] =['like','%'.$param['source'].'%'];
//            $this->assign('source',$param['source']);
//        }
//        if(!empty($param['author'])) {
//            $news_where['author'] = ['like','%'.$param['author'].'%'];
//            $this->assign('author',$param['author']);
//        }
//        foreach($first_list as $f_k=>$f_v){
//            if($f_v['title']=='深度'){//是否为二级栏目
//                $p_id = ['parent_id'=>$f_v['id']];
//                $second_list = $column->getColumns($p_id,'c.*');//获取二级栏目
//                foreach($second_list as $s_k=>$s_v){
//                    $columnArr[] = [$f_v['title'].'-->'.$s_v['title'],$s_v['id']];
//                }
//            }else{
//                $columnArr[] = [$f_v['title'],$f_v['id']];
//            }
//        }
//        if(isset($column_id)){//新闻列表的新闻分类
//            if($column_id==0 || $column_id=='0'){ //全部分类
//                $column_where = array('is_show'=>1);
//                $column_list = $new->getNews($column_where,$field);
//            }else{
//                $hot_where = ['id'=>$column_id];
//                $column_field = 'title';
//                $column_title = $column->getSingleColumn($hot_where,$column_field);
//                if($column_title['title']=='热点'){
//                    $hot_list = $new->getNewsHot();
//                    //获取热点
//                    $where_hot['n.id'] = ['in',$hot_list[0]['news_ids']];
//                    $column_list = $new->getNews($where_hot,$field);
//                }else{
//                    $column_where['n.is_delete'] = array('=',0);
//                    $column_where['is_show'] = array('=',1);
//                    $column_where[] = ['exp',"FIND_IN_SET($column_id,column_ids)"];
//                    $column_list = $new->getNews($column_where,$field);
//                }
//
//            }
//            foreach($column_list as $c_k=>$c_v){
//                $c_name = [];
//                $column_list[$c_k]['update_time'] = !empty($l_v['update_time'])?date('Y-m-d H:i:s',$l_v['update_time']):'';
//                if($c_v['is_banner']==1){//处理新闻列表的发布位置
//                    $column_list[$c_k]['position'] = 'banner';
//                }else{
//                    $column_ids['c.id'] = ['in',$c_v['column_ids']];
//                    $c_list = $column->getColumns($column_ids);//栏目列表
//                    foreach($c_list as $k=>$v){
//                        $c_name[] = $v['title'];
//                    }
//                    $column_list[$c_k]['position'] = implode(',',$c_name);
//                }
//                $column_list[$c_k]['aa'] = $c_v['column_ids'];//aa 字段没实际意义,仅仅用来表示栏目id
//                $column_list[$c_k]['allow_ad'] = str_replace(array(0,1),array('否','是'),$c_v['allow_ad']);//处理新闻列表的是否插入广告
//                $operate = [
//                    '编辑' => url('newsmanage/editNews', ['id' => $c_v['id']]),
//                    '下线' => "javascript:downline('".$c_v['id']."')",
//                ];
//                $column_list[$c_k]['operate'] = $operate;
//            }
//            $this->assign('list',$column_list);
//            $ad_name[0] = $column_id;
//            $this->assign('column',$ad_name);
//            $this->assign('column_info',$columnArr);
//        }else{
//            // var_dump('666');die;
//            $news_where['is_show']=1;
//            $news_where['n.is_delete'] = 0;
//            $news_where['is_banner']=0;
//            $list = $new->getNews($news_where,$field);//获取所有新闻
//            foreach($list as $l_k=>$l_v){
//                $c_name = [];
//                $list[$l_k]['update_time'] = !empty($l_v['update_time'])?date('Y-m-d H:i:s',$l_v['update_time']):'';
//                $column_where['c.id'] = ['in',$l_v['column_ids']];
//                $c_list = $column->getColumns($column_where);//栏目列表
//                foreach($c_list as $k=>$v){
//                    $c_name[] = $v['title'];
//                }
//                $list[$l_k]['position'] = implode(',',$c_name);
//                $list[$l_k]['aa'] = $l_v['column_ids'];//aa 字段没实际意义,仅仅用来表示栏目id
//                $list[$l_k]['allow_ad'] = str_replace(array(0,1),array('否','是'),$l_v['allow_ad']);
//                $operate = [
//                    '编辑' => url('newsmanage/editNews', ['id' => $l_v['id']]),
//                    '下线' => "javascript:downline('".$l_v['id']."')",
//                ];
//                $list[$l_k]['operate'] = $operate;
//            }
//            $this->assign('list',$list);
//            $ad_name[0] = 0;
//            $this->assign('column',$ad_name);
//            $this->assign('column_info',$columnArr);
//        }
//        return $this->fetch();
//    }
//
//
//    //编辑新闻  暂时没有用到
//    public function editNews()
//    {
//        $new = new NewsModel();
//        $column = new Column();
//        $banner = new BannerModel();
//        $news_source = new NewsSource();
//        $nid = input('param.id');
//        $news_id = ['id'=>$nid];
//        $news_list = $new->getNewsDetail($news_id);
//        $flag_arr = [6,7,8,9];
//        $child_zt_arr = [];
//        $child_zt = [];
//        foreach($news_list as $new_k=>$new_v){
//            if($new_v['column_ids']==0){
//                $flag_arr[0] = 0;//banner
//            }else{
//                $cid = explode(',',$new_v['column_ids']);
//                foreach($cid as $cid_k=>$cid_v){
//                    $column_id = ['id'=>$cid_v];
//                    $c_name = $column->getSingleColumn($column_id);
//                    switch($c_name['title']){
//                        case '新闻':
//                            $flag_arr[1] = 1;
//                            break;
//                        case '深度':
//                            $flag_arr[2] = 2;
//                            //专题
//                            $zt_title = ['title'=>'深度'];
//                            $zt_pid_arr = $column->getSingleColumn($zt_title);
//
//                            $zt_id = $zt_pid_arr['id'];
//                            $where_zt_id = ['parent_id'=>$zt_id];
//                            $zt_arr = $column->getColumns($where_zt_id);
//                            foreach($zt_arr as $zt_arr_k=>$zt_arr_v){
//                                $child_zt[$zt_arr_k] = $zt_arr_v['id'];
//                            }
//                            $ids_arr = explode(',',$news_list[0]['column_ids']);
//                            $arr = array_intersect($ids_arr,$child_zt);//筛选出专题id
//                            if(!empty($arr)){
//                                $child_id['c.id'] = ['in',$arr];//获取选中的专题
//                                $child_zt_arr = $column->getColumns($child_id);
//                            }else{
//                                $child_zt_arr = [];
//                            }
//                            break;
//                        case '热点':
//                            $flag_arr[3] = 3;
//                            break;
//                    }
//                }
//            }
//        }
//        $banner_wh['out_id'] = $news_list[0]['id'];
//        $banner_arr = $banner->getSingleBanners($banner_wh,'picture_path,sort');//banner图片
//        $banner_pic = $banner_arr['picture_path'];
//        $banner_sort = $banner_arr['sort'];
//        $news_list[0]['banner_pic'] = "$banner_pic";
//        $news_list[0]['banner_sort'] = "$banner_sort";
//        $column_wh['c.id'] = ['in',$news_list[0]['column_ids']];
//        $column_arr = $column->getColumns($column_wh,'title', 'c.id asc');//栏目
//        $columnArr = [];
//        foreach($column_arr as $a_k=>$a_v){
//            $columnArr[] = $a_v['title'];
//        }
//        $news_list[0]['column_ids'] = $columnArr;
//        $new_arr = $news_list[0];
//        //新闻详情
//        $source_detail_id['news_id'] = $nid;
//        $source_detail_pic = $news_source->getNewsSource($source_detail_id,'source_path,type,detail','id asc');
//        if(!empty($source_detail_pic)){
//            $news_list[0]['detail_arr'] = $source_detail_pic;
//            $news_list[0]['detail_type'] = $source_detail_pic[0]['type'];
//            $this->assign('detail_arr',$source_detail_pic);
//        }else{
//            $news_list[0]['detail_arr'] = '';
//            $news_list[0]['detail_type'] = '';
//        }
//        //发布新闻时选中的专题
//        $zt_title = ['title'=>'深度'];
//        $zt_pid_arr = $column->getSingleColumn($zt_title);
//        $zt_id = $zt_pid_arr['id'];
//        $where_zt_id = ['parent_id'=>$zt_id];
//        $zt_arr = $column->getColumns($where_zt_id);
//        //新闻资源图片
//        $source_id['news_id'] = $nid;
//        if($news_list[0]['source_type']==4){//过滤视频缩略图
//            $source_id['type'] = ['neq',1];
//        }
//        $source_pic = $news_source->getNewsSource($source_id,'source_path,type','id asc');
//        $source_arr = [];
//        foreach($source_pic as $sv){
//             $source_arr[] = $sv['source_path'];
//        }
//        $new_arr['source_pic'] = $source_arr;
//        //banner资源图片
//        $banner_id = ['out_id'=>$nid];
//        $banner_pic = $banner->getBanners($banner_id);
//        if(!empty($banner_pic)){
//            foreach($banner_pic as $banner_k=>$banner_v){
//                $banner_pic_arr[0]['source_path'][$banner_k] = $banner_v['picture_path'];
//                $banner_pic_arr[0]['sort'][$banner_k] = $banner_v['sort'];
//            }
//            $this->assign('banner_img',$banner_pic_arr);//banner图片
//        }
//        //所有栏目
//        $all_wh = ['c.is_show'=>1,'c.is_delete'=>0, 'parent_id'=>0];
//        $all_column = $column->getColumns($all_wh,'title', 'c.id asc');
//        //草稿箱编辑过来的如果是banner新闻
//        $where_bid = ['id'=>$nid];
//        $banner_new_arr = $new->getNewsDetail($where_bid, 'is_banner');
//        if($banner_new_arr[0]['is_banner']==1){
//            $position_diff = $this->bannerSort();
//            $this->assign('order',$position_diff);
//        }
//        //is_banner、is_show、column_ids
//        $this->assign('new_list',$news_list);
//        $this->assign('detail',$new_arr);
//        $this->assign('column',$all_column);
//        $this->assign('flag',$flag_arr);
//        $this->assign('zt',$zt_arr);//专题
//        $this->assign('child_zt',$child_zt_arr);//选中的专题
//        return $this->fetch('newsmanage/publish');
//    }
//
//
//    //新闻编辑更新
//    public function editSave($params, $fileArr)
//    {
//        if ($params) {
//            $news_source = new NewsSource();
//            //获取编辑前记录
//            $where_before = ['news_id'=>$params['id']];//新闻id
//            $before_detail = $news_source->getNewsSource($where_before, 'id', 'id asc');//编辑之前的新闻图片资源
//            $new = new NewsModel();
//            $banner = new BannerModel();
//            $news_source = new NewsSource();
//            $column = new Column();
//            $new_table = 'news';
//            $source_table = 'news_source';
//            $column_table = 'column';
//            $banner_table = 'banner';
//            $code = 1;
//            $msg = '操作成功';
//            $err_msg = '操作成功';
//            //保存音频
//            if (!empty($fileArr[2]['error']==0)) {
//                $video_url = $this->file_single_upload($fileArr, 2, $params);//音频上传
//                $news_arr['audio'] = $video_url;//音频
//            }
//            //栏目id
//            $id = '';
//            foreach ($params['publish_position'] as $key => $val) {//0-新闻，1-深度，2-热点
//                $where_title = ['title' => $val, 'is_show' => 1, 'is_delete' => 0];
//                $id_arr = $column->getSingleColumn($where_title);
//                if ($val == '深度') {
//                    $where_zt['title'] = ['in', isset($params['zt']) ? $params['zt'] : ''];//zt 专题 深度下的二级栏目
//                    $where_zt['parent_id'] = ['eq', $id_arr['id']];
//                    $pid_arr = $column->getColumns($where_zt);
//                    foreach ($pid_arr as $p_k => $p_v) {
//                        $p_id[$p_k] = $p_v['id'];
//                    }
//                    $zt_id = implode(',', $p_id);//深度下的二级栏目id
//                    $id .= $zt_id . ',' . $id_arr['id'] . ',';
//                } else {
//                    $id .= $id_arr['id'] . ',';
//                    if ($val == '热点') {
//                        $news_arr['is_hot'] = 1;//是否是热点: 0 否 1 是
//                    }
//                }
//                $ids = trim($id, ',');//栏目id
//                $ids = explode(',', $ids);
//                sort($ids);//给数组排序
//                $ids = implode(',', $ids);
//            }
//            if (empty($ids)) {
//                $ids = 0;
//            }
//            //发布状态
//            switch ($params['publish_time']) {
//                case 0:
//                    $news_arr['is_show'] = 0;//保存至待发布
//                    break;
//                case 1:
//                    $news_arr['is_show'] = 1;//即时发布
//                    break;
//                case 2:
//                    $news_arr['is_show'] = 2;//定时发布
//                    break;
//                default:
//            }
//            //处理关键词
//            $reply = ['   ', '  ', ' ', ',', '，'];
//            $isreply = [',', ',', ',', ',', ','];
//            $key = str_replace($reply, $isreply, $params['news_keyword']);
//            //处理编辑器上传图片
//            if(isset($params['content'])&&!empty($params['content'])){
//                $content = str_replace("\"", "'", $params['content']);
//            }else{
//                $content = '';
//            }
//            //新闻字段
//            $news_arr['column_ids'] = $ids;//发布至banner  banner新闻
//            $news_arr['title'] = trim($params['news_title']);
//            $news_arr['author'] = trim($params['news_author']);
//            $news_arr['keywords'] = trim($key);
//            $news_arr['summary'] = trim($params['news_summary']);
//            $news_arr['detail'] = $content;//新闻正文
//            $news_arr['is_recommend'] = $params['is_recommend'];//是否推送相关新闻
//            $news_arr['allow_comment'] = isset($params['auth_discuss']) ? $params['auth_discuss'] : 0;//评论
//            $news_arr['allow_transmit'] = isset($params['auth_forward']) ? $params['auth_forward'] : 0;//转发
//            $news_arr['source'] = trim($params['source']);//新闻来源
//            $news_arr['update_time'] = time();
//            $news_arr['admin_id'] = session('id');
//            if (!isset($news_arr['is_hot'])) {//更新热点
//                $news_arr['is_hot'] = 0;
//            }
//            // 启动事务
//            Db::startTrans();
//            try {
//                //判断发布位置
//                if (isset($params['is_style'])) {//新闻列表（可同时发布至热点、深度）
//                    if ($params['is_style'] == 1) {//三图
//                        $news_arr['source_type'] = 3;
//                        $news_arr['style'] = 1;
//                    } else if ($params['is_style'] == 2) {//单图（正方形）
//                        $news_arr['source_type'] = 5;
//                        $news_arr['style'] = 2;
//                    } else if ($params['is_style'] == 3) {//单图（长方形）、视频
//                        $news_arr['source_type'] = 2;
//                        $news_arr['style'] = 3;
//                    } else if ($params['is_style'] == 4) {//纯文本
//                        $news_arr['source_type'] = 1;
//                        $news_arr['style'] = 4;
//                        //删除原有的资源数据
//                        $del_id = ['news_id' => $params['id'],'type'=>1];
//                        $news_source->delSource($del_id);
//                    }
//                    //更新新闻资源表
//                    $news_link_arr['news_id'] = $params['id'];//新闻关联id
//                    $new_source_arr = [];
//                    $where_source_id = ['news_id' => $params['id'],'type'=>1];
//                    $new_source_arr = $news_source->getNewsSource($where_source_id, '*', 'id asc');//获取新闻资源
//                    if ($params['is_style'] == 1) {//多图
//                        $news_arr['source_type'] = 3;//新闻资源类型
//                        $news_link_arr['type'] = 1;//列表
//                        $img_url = $this->file_more_upload($fileArr, 1, $params);//多张图片上传
//                        foreach ($img_url as $im_k => $im_v) {
//                            if (!empty($im_v)) {
//                                $news_link_arr['source_path'] = $im_v;//资源路径
//                                if (!isset($new_source_arr[$im_k]['id'])) {
//                                    $news_source->insertSource($news_link_arr);//保存广告资源信息
//                                } else {
//                                    $source_id = ['id' => $new_source_arr[$im_k]['id']];
//                                    $news_source->updateNewsSource($source_id, $news_link_arr, $source_table);//更新广告资源信息
//                                }
//                            }
//                        }
//                    }else {
//                        $img_url = $this->file_single_upload($fileArr, 1, $params);//单图片上传
//
//                        if (!empty($img_url)) {
//                            $mimetype = $this->getImgType("$img_url");//文件类型
//                            //删除原来数据
//                            $del_id = ['news_id' => $params['id'],'type'=>1];
//                            $news_source->delSource($del_id);
//                            //保存新数据  没有更新  没有删除原有数据  style样式没有更新
//                            if ($mimetype == 1) {//视频
//                                if ($params['is_style'] == 3) {
//                                    $news_arr['source_type'] = 4;//新闻资源类型
//                                }
//                                $news_link_arr['type'] = 4;//视频
//                                $news_link_arr['source_path'] = $img_url;//资源路径
//                                $news_link_arr2 = $news_link_arr;
//                                $news_link_arr2['source_path'] = $img_url . '?x-oss-process=video/snapshot,t_1000,f_jpg,w_800,h_600,m_fast';//视频缩略图
//                                $news_link_arr2['type'] = 1;
//                                $all = [0 => $news_link_arr, 1 => $news_link_arr2];
//                                $news_source->moreInsertSource($all);
//                            } else {
//                                $news_link_arr['type'] = 1;//新闻列表
//                                $news_link_arr['source_path'] = $img_url;//资源路径
//                                $news_source->insertSource($news_link_arr);
//                            }
//                        }
//                    }
//                    //更新新闻
//                    $where_new_id = ['id'=>$params['id']];
//                    $new->updateNews($where_new_id,$news_arr,$new_table);
//                    //热点swoole_client
//                    if(in_array('热点',$params['publish_position'])){
//                        $send = [
//                            'news_id' => $params['id'],
//                            'type'    => 1
//                        ];
//                        swoole_client($send);
//                    }
//                    //编辑保存详情图
//                    if($params['news_detail']==1){//存在详情图片
//                        $detail_url = $this->file_more_upload($fileArr,3,$params);//多文件上传
//                        $detail_arr = [];
//                        if(count($before_detail)>=count($detail_url)){//处理原图片个数与新编辑图片个数不相同的情况，原来1张图，编辑为3张图
//                            foreach($before_detail as $before_k=>$before_v){
//                                if(!empty($detail_url[$before_k])){
//                                    $detail_arr[$before_k]['id'] = $before_v['id'];
//                                    $detail_arr[$before_k]['source_path'] = $detail_url[$before_k];
//                                    $detail_arr[$before_k]['detail'] = $params['detailcont'][$before_k];
//                                }
//                                if(!isset($detail_url[$before_k])){//删除多余的图片
//                                    $where_id = ['id'=>$before_v['id']];
//                                    $news_source->delSource($where_id);
//                                }
//                            }
//                            $news_source->updateAll($detail_arr);//更新多条
//                        }else{
//                            foreach($detail_url as $detail_k=>$detail_v){
//                                if(!empty($detail_v)){
//                                    if(!isset($before_detail[$detail_k]['id'])){
//                                        $where_par = ['source_path'=>$detail_v,'type'=>3,'detail'=>$params['detailcont'][$detail_k],'news_id'=>$params['id']];
//                                        $news_source->insertSource($where_par);
//                                    }else{
//                                        $detail_arr[$detail_k]['id'] = $before_detail[$detail_k]['id'];
//                                        $detail_arr[$detail_k]['source_path'] = $detail_v;
//                                        $detail_arr[$detail_k]['detail'] = $params['detailcont'][$detail_k];
//                                    }
//                                }
//                            }
//                            $news_source->updateAll($detail_arr);//更新多条
//                        }
//
//                    }
//                }
//            else{//发布至banner
//                    //更新新闻表
//                    $news_arr['column_ids'] = 1;//发布至banner  banner新闻
//                    $news_arr['is_banner'] = 1;
//                    $news_arr['sort'] = $params['sort'];
//                    $news_arr['style'] = 0;
//                    $news_arr['is_hot'] = 0;//是否是热点: 0 否 1 是
//                    $news_arr['source_type'] = 1; //新闻资源类型 1:纯文本,2:单图(长方形),3多图,4:视频,5:单图(正方形)
//                    $where_nid = ['id'=>$params['id']];
//                    $new->updateNews($where_nid,$news_arr,$new_table);
//                    $news_id = $params['id'];//新闻id
//                    //删除新闻相关资源(如果存在)
//                    $news_source->delSource($where_nid);
//                    //更新banner表
//                    $banner_arr['out_id'] = $news_id;//新闻id
//                    $banner_arr['type'] = 1;//banner类型 0:新闻,1:广告
//                    $banner_arr['create_time'] = time();
//                    $banner_arr['update_time'] = time();
//                    $img_url = $this->file_more_upload($fileArr,0,$params);//单图片上传
//                    //删除原来数据
//                    $del_id = ['news_id'=>$params['id']];
//                    $banner->delBanner($del_id);
//                    //保存新数据  没有更新  没有删除原有数据  style样式没有更新
//                    if(!empty($img_url)){
//                        $banner_arr['picture_path'] = $img_url[0];//资源路径
//                        $banner->saveBanner($banner_arr);
//                    }
//                }
//                // 提交事务
//                Db::commit();
//            }catch(\PDOException $e){
//                //回滚事务
//                Db::rollback();
//                $code = 0;
//                $msg = '操作失败';
//                $err_msg = $e->getMessage();
//            }
//            return json(['code' => $code, 'msg' => $msg, 'err_msg'=>$err_msg]);
//        }
//        return $this->fetch();
//    }
//
//    //判断来源是否在黑名单
//    public function sourceblack(){
//        $req = input();
//
//        $res = Db::table('scs_source_blacklist')->where(['content'=>$req['data'],'is_delete'=>0])->find();
//
//        if($res){
//            return json_encode(['isSuccess'=>1,'msg'=>'存在来源黑名单中!']);
//        }else{
//            return json_encode(['isSuccess'=>0]);
//        }
//    }
//
//
//    //新闻下线
//    public function newsDownline(){
//        $code = 1;
//        $msg = '下线成功';
//        $news = new NewsModel();
//        $banner = new BannerModel();
//        $table = 'news';
//        $news_id = input('param.id');//新闻id
//        // 启动事务
//        Db::startTrans();
//        try{
//            //更新banner表
//            $where_banner = ['out_id'=>$news_id,'type'=>0];//banner类型 0:新闻,1:广告
//            $update_banner['is_show'] = 0;
//            $banner->updateBanners($update_banner,$where_banner);
//            //更新新闻表
//            $where_id = ['id'=>$news_id];
//            $update_arr['is_show'] = 0;
//            $news->updateNews($where_id,$update_arr,$table);
//            // 提交事务
//            Db::commit();
//        }
//        catch (\PDOException $e){
//            //回滚事务
//            Db::rollback();
//            $code = 0;
//            $msg = '操作失败';
//            $err_msg = $e->getMessage();
//        }
//        return json(['code' => $code, 'msg' => $msg]);
//    }
//
//
//    //发布新闻
//    public function publish()
//    {
//        $params = $_POST;
//        if(isset($params['id'])&&!empty($params['id'])){//新闻编辑相关id
//            $bannerArray = isset($_FILES['bannerfile']) ? $_FILES['bannerfile'] : '';//发布banner新闻，banner图片
//            $newsArray = isset($_FILES['newsfile']) ? $_FILES['newsfile'] : '';//新闻图片
//            $videoArray = isset($_FILES['audio']) ? $_FILES['audio'] : '';//发布新闻，音频文件
//            $detailArray = isset($_FILES['newsdetail']) ? $_FILES['newsdetail'] : '';//新闻详情图片
//            $fileArray = [$bannerArray, $newsArray, $videoArray,$detailArray];
//            $this->editSave($params,$fileArray);//新闻编辑保存
//        }else{
//            $new = new NewsModel();
//            $banner = new BannerModel();
//            $news_source = new NewsSource();
//            $column = new Column();
//            if (request()->isPost()) {
//                $code = 1;
//                $msg = '保存成功';
//                $err_msg = '保存成功';
//                $params = $_POST;
//                $bannerArray = isset($_FILES['bannerfile']) ? $_FILES['bannerfile'] : '';//发布banner新闻，banner图片
//                $newsArray = isset($_FILES['newsfile']) ? $_FILES['newsfile'] : '';//新闻图片
//                $videoArray = isset($_FILES['audio']) ? $_FILES['audio'] : '';//发布新闻，音频文件
//                $detailArray = isset($_FILES['newsdetail']) ? $_FILES['newsdetail'] : '';//新闻详情图片
//                $fileArray = [$bannerArray, $newsArray,$videoArray,$detailArray];
//                //保存音频
//                if($fileArray[2]['error']==0){
//                    $video_url = $this->file_single_upload($fileArray,2,$params);//音频上传
//                }else{
//                    $video_url = '';
//                }
//                //栏目id
//                $id = '';
//                foreach($params['publish_position'] as $key=>$val){//0-新闻，1-深度，2-热点
//                    $where_title = ['title'=>$val,'is_show'=>1,'is_delete'=>0];
//                    $id_arr = $column->getSingleColumn($where_title);//通过栏目标题获取栏目信息
//                    if($val=='深度'){
//                        $where_zt['title'] = ['in',isset($params['zt'])?$params['zt']:''];// zt字段 深度的二级栏目专题
//                        $where_zt['parent_id'] = ['eq',$id_arr['id']];
//                        $pid_arr = $column->getColumns($where_zt);//获取选中的二级栏目
//                        foreach($pid_arr as $p_k=>$p_v){
//                            $p_id[$p_k] = $p_v['id'];
//                        }
//                        $zt_id = implode(',',$p_id);
//                        $id.= $zt_id.','.$id_arr['id'].','; //获取二级栏目专题id
//                    }else{
//                        $id.= $id_arr['id'].',';
//                        if($val=='热点'){
//                            $news_arr['is_hot'] = 1;//是否是热点: 0 否 1 是
//                        }
//                    }
//                    $ids = trim($id,',');//二级栏目专题id
//                    $ids = explode(',',$ids);
//                    sort($ids);//给数组排序
//                    $ids = implode(',',$ids);
//                }
//                if(empty($ids)){//默认二级栏目专题id为0，避免为空报错的情况
//                    $ids = 0;
//                }
//                //发布状态
//                switch ($params['publish_time'])
//                {
//                    case 0:
//                        $news_arr['is_show'] = 0;//保存至待发布
//                        break;
//                    case 1:
//                        $news_arr['is_show'] = 1;//即时发布
//                        break;
//                    case 2:
//                        $news_arr['is_show'] = 2;//定时发布
//                        break;
//                    default:
//                }
//                //处理关键词
//                $reply = ['   ','  ',' ',',','，'];
//                $isreply = [',',',',',',',',','];
//                $key = str_replace($reply,$isreply,$params['news_keyword']);
//                //处理编辑器上传图片
//                if(isset($params['content'])&&!empty($params['content'])){
//                    $content = str_replace("\"","'",$params['content']);
//                }else{
//                    $content = '';
//                }
//                //新闻字段
//                $news_arr['title'] = trim($params['news_title']);
//                $news_arr['origin_id'] = 0;
//                $news_arr['author'] = trim($params['news_author']);
//                $news_arr['keywords'] = trim($key);
//                $news_arr['summary'] = trim($params['news_summary']);
//                $news_arr['detail'] = $content;//新闻正文
//                $news_arr['is_recommend'] = $params['is_recommend'];//是否推送相关新闻
//                $news_arr['allow_comment'] = isset($params['auth_discuss']) ? $params['auth_discuss'] : 0;//评论
//                $news_arr['allow_transmit'] = isset($params['auth_forward']) ? $params['auth_forward'] : 0;//转发
//                $news_arr['allow_ad'] = $params['allow_ad'];//是否可插入广告
//                $news_arr['source'] = $params['source'];//新闻来源
//                $news_arr['create_time'] = time();
//                $news_arr['publish_time'] = time();
//                $news_arr['update_time'] = time();
//                $news_arr['admin_id'] = session('id');
//                // 启动事务
//                Db::startTrans();
//                try{
//                    //判断发布位置
//                    if(isset($params['is_style'])){//新闻列表（可同时发布至热点、深度）
//                        if($params['is_style']==1){//3张图    新闻资源类型
//                            $news_arr['source_type'] = 3;
//                            $news_arr['style'] = 1;//新闻样式
//                            $news_arr['audio'] = $video_url;//音频
//                        }else if($params['is_style']==2){//单图（正方形）  新闻资源类型
//                            $news_arr['source_type'] = 5;
//                            $news_arr['style'] = 2;
//                            $news_arr['audio'] = $video_url;//音频
//                        }else if($params['is_style']==3){//单图（长方形或视频）  新闻资源类型
//                            $img_url = $this->file_single_upload($fileArray,1,$params);//单图片上传
//                            $mimetype = $this->getImgType("$img_url");
//                            if($mimetype == 1){//视频
//                                $news_arr['source_type'] = 4;
//                            }else{
//                                $news_arr['source_type'] = 2;
//                            }
//                            $news_arr['style'] = 3;
//                            $news_arr['audio'] = $video_url;//音频
//                        }else if($params['is_style']==4){//纯文本  新闻资源类型
//                            $news_arr['source_type'] = 1;
//                            $news_arr['style'] = 4;
//                        }
//                        $news_arr['column_ids'] = $ids;//新闻栏目
//                        $news_arr['is_banner'] = 0;//不是banner
//                        if($params['news_detail']==1){//存在详情图片
//                            $news_arr['detail_type'] = 2;//详情显示方式 1 正常文本流 2 横向滚动文本流
//                        }
//                        $result = $new->saveNews($news_arr);
//                        $news_id = $result['newsId'];//新闻id
//                       //swoole_client
//                        $data = [
//                            'type' => 2,
//                            'news_id' => $news_id,
//                            'title'   => trim($params['news_title'])
//                        ];
//                        swoole_client($data);
//                        //热点swoole_client
//                        if(in_array('热点',$params['publish_position'])){
//                            $send = [
//                                'news_id' => $news_id,
//                                'type'    => 1
//                            ];
//                            swoole_client($send);
//                        }
//                        //保存详情图
//                        //保存新闻详情
//                        if($params['news_detail']==1){//存在详情图片
//                            $detail_url = $this->file_more_upload($fileArray,3,$params);
//                            $detail_arr = [];
//                            foreach($detail_url as $detail_k=>$detail_v){
//                                $detail_arr[$detail_k]['source_path'] = $detail_v;
//                                $detail_arr[$detail_k]['news_id'] = $news_id;
//                                $detail_arr[$detail_k]['detail'] = $params['detailcont'][$detail_k];
//                                $detail_arr[$detail_k]['type'] = 3;
//                            }
//                            $news_source->moreInsertSource($detail_arr);
//                        }
//                        //保存新闻资源表
//                        $news_link_arr['news_id'] = $news_id;//新闻关联id
//                        if($params['is_style']==1){//多图
//                            $img_url = $this->file_more_upload($fileArray,1,$params);//多张图片上传
//                            foreach($img_url as $im_k=>$im_v){
//                                $news_link_arr['source_path'] = $im_v;//资源路径
//                                $news_source->insertSource($news_link_arr);
//                            }
//                        }else{
//                            if($params['is_style']!=4){
//                                $img_url = $this->file_single_upload($fileArray,1,$params);//单图片上传
//                                $mimetype = $this->getImgType("$img_url");
//                                if($mimetype == 1){//视频
//                                    $news_link_arr['type'] = 4;//视频
//                                    $news_link_arr['source_path'] = $img_url;
//                                    $news_link_arr2 = $news_link_arr;
//                                    $news_link_arr2['source_path'] = $img_url.'?x-oss-process=video/snapshot,t_1000,f_jpg,w_800,h_600,m_fast';//上传视频生成缩略图
//                                    $news_link_arr2['type'] = 1;
//                                    $all = [0=>$news_link_arr,1=>$news_link_arr2];
//                                    $news_source->moreInsertSource($all);
//                                }else{
//                                    $news_link_arr['type'] = 1;//新闻列表
//                                    $news_link_arr['source_path'] = $img_url;//资源路径
//                                    $news_source->insertSource($news_link_arr);
//                                }
//                            }
//                        }
//
//                    }else{//发布至banner
//                        $img_url = $this->file_more_upload($fileArray,0,$params);//多张图片上传
//                        //保存新闻表
//                        $news_arr['column_ids'] = 1;//发布至banner  传1,打新闻标签
//                        $news_arr['is_banner'] = 1;
//                        $news_arr['audio'] = $video_url;//音频
//                        $news_arr['source_type'] = 1; //新闻资源类型 1:纯文本,2:单图(长方形),3多图,4:视频,5:单图(正方形)
//                        $result = $new->saveNews($news_arr);
//                        $news_id = $result['newsId'];//新闻id
//                        //保存banner关联表
//                        $banner_arr['out_id'] = $news_id;//新闻id
//                        $banner_arr['sort'] = $params['sort'];
//                        $banner_arr['type'] = 0;//banner类型 0:新闻,1:广告
//                        $banner_arr['create_time'] = time();
//                        $banner_arr['update_time'] = time();
//                        $banner_arr['picture_path'] = $img_url[0];
//                        //保存前先检查是否有sort（新闻次序）重复的记录 有覆盖（is_show置为0），无更新
//                        $where_sor = ['sort'=>$params['sort'],'is_show'=>1,'is_delete'=>0];
//                        $sort_ar = $banner->getBanners($where_sor);
//                        $out_id = [];
//                        foreach($sort_ar as $out_v){
//                            $out_id[] = $out_v['out_id'];
//                        }
//                        if(!empty($sort_ar)){ //有重复的记录 有覆盖（is_show置为0）
//                            $where_new['id'] = ['in',$out_id];
//                            $param_new = ['is_show'=>0,'is_delete'=>1];
//                            $table = 'news';
//                            $new->updateNews($where_new,$param_new,$table);//更新新闻记录
//                            $where_bane = ['sort'=>$params['sort'],'is_show'=>1,'is_delete'=>0];
//                            $param_bane = ['is_show'=>0,'is_delete'=>1];
//                            $banner->updateBane($where_bane,$param_bane);//更新新闻banner资源记录
//                        }
//                        $banner->saveBanner($banner_arr);//保存banner资源图片
//                    }
//                    // 提交事务
//                    Db::commit();
//                }catch(\PDOException $e){
//                    //回滚事务
//                    Db::rollback();
//                    $code = 0;
//                    $msg = '保存失败';
//                    $err_msg = $e->getMessage();
//                }
//                return json(['code' => $code, 'msg' => $msg, 'err_msg'=>$err_msg]);
//            }else{
//                $c_list = [];
//                $column = new Column();
//                $where_title = ['title'=>'深度'];
//                $id = $column->getSingleColumn($where_title);
//                $where_id = ['parent_id'=>$id['id']];
//                $c_list = $column->getColumns($where_id);//获取所有二级栏目
//                //所有一级栏目
//                $where_all = ['c.is_show'=>1,'c.is_delete'=>0,'parent_id'=>0];
//                $all_arr = $column->getColumns($where_all,'*','c.id asc');
//                $position_diff = $this->bannerSort();//获取banner新闻的推送次序
//                $this->assign('order',$position_diff);
//                $this->assign('zt',$c_list);
//                $this->assign('column',$all_arr);
//                return $this->fetch();
//            }
//        }
//    }
//
//    //banner新闻的推送次序
//    public function bannerSort(){
//        $new = new NewsModel();
//        $banner = new BannerModel();
//        //需要显示的新闻banner次序 news banner banner_adv config
//        // $banner_wh['is_banner'] = 1;
//        // $banner_field = 'id';
//        // $bid_arr = $new->getNews($banner_wh,$banner_field);//banner新闻id
//        // $oid = [];
//        // foreach($bid_arr as $bid_v){
//        //     $oid[] = $bid_v['id'];
//        // }
//        // //已有的banner新闻次序
//        // $order_wh['out_id'] = ['in',$oid];
//        // $order_wh['is_show'] = 1;
//        // $order_arr = $banner->getBanners($order_wh);
//        // $ord = [];
//        // foreach($order_arr as $or_v){
//        //     $ord[] = $or_v['sort'];
//        // }
//        $banner_conf_arr = $banner->getBannersConf();//获取banner_num banner位置数量
//        $where_posi['is_show'] = 1;
//        $banner_position_arr = $banner->getBannersAdver($where_posi);//获取banner广告位置
//        $position = [];
//        foreach($banner_position_arr as $position_v){
//            $position[] = $position_v['position'];//banner广告位置数组 如[1,5]
//        }
//        $all = [];
//        for($i=0;$i<$banner_conf_arr['banner_num'];$i++){
//            $all[] = $i+1; //banner位置数量 如6 [1,2,3,4,5,6]
//        }
//        $position_diff = array_diff($all,$position); //[2,3,4,6]
//        return $position_diff;
//    }

    /**
     * 发送短信
     */
    public function sendSms() {
        $params = array ();
        // *** 需用户填写部分 ***
        $security = false;
        $accessKeyId = "LTAIvU8LsUeOggFD";
        $accessKeySecret = "dyLnq0Y9bfmAMZVc7VMHCn9tNykCdI";
        $params["PhoneNumbers"] = "17601359011";
        $params["SignName"] = "中合消防";
        $params["TemplateCode"] = "SMS_137425533";
        $params['TemplateParam'] = Array (
            "code" => "12345",
            "product" => "阿里通信"
        );
        $params['OutId'] = "12345";
        $params['SmsUpExtendCode'] = "1234567";
        // *** 需用户填写部分结束, 以下代码若无必要无需更改 ***
        if(!empty($params["TemplateParam"]) && is_array($params["TemplateParam"])) {
            $params["TemplateParam"] = json_encode($params["TemplateParam"], JSON_UNESCAPED_UNICODE);
        }
        // 初始化SignatureHelper实例用于设置参数，签名以及发送请求
        $helper = new SignatureHelper();
        // 此处可能会抛出异常，注意catch
        $content = $helper->request(
            $accessKeyId,
            $accessKeySecret,
            "dysmsapi.aliyuncs.com",
            array_merge($params, array(
                "RegionId" => "cn-hangzhou",
                "Action" => "SendSms",
                "Version" => "2017-05-25",
            )),
            $security
        );
        return $content;
    }

    public function test(){
        print_r($this->sendSms());
    }



    //$fileArray 文件数组
    //$order=0--bannerfile、$order=1--newsfile
    public function file_more_upload($fileArray,$order,$param){
        require_once  __DIR__."/../../../vendor/aliyun_oss/autoload.php";//引入阿里云oss
        //阿里云配置
        $accessKeyId = config('oss_file_conf')['accessKeyId'];
        $accessKeySecret = config('oss_file_conf')['accessKeySecret'];
        $endpoint = config('oss_file_conf')['endpoint'];
        $bucket= config('oss_file_conf')['bucket'];
        $filePath = ["serverData/images/","serverData/video/","serverData/audio/"];//文件上传路径
        $arr = $fileArray[$order];
        $files = array();
        if(!empty($arr)){//非空
            for ($i=0; $i < count($arr['name']); $i++) {
                $files[$i]['name'] = $arr['name'][$i];
                $files[$i]['type'] = $arr['type'][$i];
                $files[$i]['tmp_name'] = $arr['tmp_name'][$i];
                $files[$i]['error'] = $arr['error'][$i];
                $files[$i]['size'] = $arr['size'][$i];
            }
            for ($i=0; $i < count($files); $i++) {
                $path = '';
                switch ($files[$i]['error']) {
                    case 0:
                        $fileName = $files[$i]['name'];
                        $fileTemp = $files[$i]['tmp_name'];
                        $suffix = substr($fileName, strrpos($fileName, '.')+1);//文件后缀
                        $file_name = date('YmdHis').mt_rand(10000,99999).'.'.$suffix;
                        $file_name = iconv("UTF-8", "gbk", $file_name);
                        $file = $fileTemp;//$file 本地文件路径
                        //您要创建的Object的名称。
                        $mimetype = $this->getImgType("$file");//判断文件类型
                        if ($mimetype != 1) {
                            $object_ = $filePath[0].$file_name;
                        }else {
                            if ($order == 0) {
                                $object_ = $filePath[2].$file_name;//音频
                            } else {
                                $object_ = $filePath[1].$file_name;//视频
                            }
                        }
                        $ossClient = new OssClient($accessKeyId, $accessKeySecret, $endpoint);
                        $ossClient->uploadFile($bucket, $object_, $file);
                        $path = $endpoint . '/' . $bucket . '/' . $object_;
                        break;
                    default:$path = '';
                }
                $fname[$i] = $path;
            }
        }else{
            $fname[0] = '';
        }
        return $fname;
    }


       //单文件上传   $order等于0时上传音频
       public function file_single_upload($fileArray,$order,$param){
            require_once  __DIR__."/../../../vendor/aliyun_oss/autoload.php";//引入阿里云oss
           //阿里云配置
            $accessKeyId = config('oss_file_conf')['accessKeyId'];
            $accessKeySecret = config('oss_file_conf')['accessKeySecret'];
            $endpoint = config('oss_file_conf')['endpoint'];
            $bucket= config('oss_file_conf')['bucket'];
            $filePath = ["serverData/images/","serverData/video/","serverData/audio/"];//文件上传路径
            $arr = $fileArray[$order];
            $path = '';
            if(!empty($arr)) {//非空
                 if($arr['error']==0){
                     $fileName = $arr['name'];
                     $fileTemp = $arr['tmp_name'];
                     $suffix = substr($fileName, strrpos($fileName, '.') + 1);//文件后缀
                     $file_name = date('YmdHis') . mt_rand(10000, 99999) . '.' . $suffix;
                     $file_name = iconv("UTF-8", "gbk", $file_name);
                     $file = $fileTemp;//$file 本地文件路径
                     //您要创建的Object的名称。
                     $mimetype = $this->getImgType("$file");
                     if ($mimetype != 1) {
                         $object_ = $filePath[0].$file_name;
                     }else {
                         if ($order == 0) {
                             $object_ = $filePath[2].$file_name;//音频
                         } else {
                             $object_ = $filePath[1].$file_name;//视频
                         }
                     }
                     $ossClient = new OssClient($accessKeyId, $accessKeySecret, $endpoint);
                     $ossClient->uploadFile($bucket, $object_, $file);//调用阿里云方法处理文件
                     $path = $endpoint . '/' . $bucket . '/' . $object_;
                 }
            }
                $fname = $path;
            return $fname;
        }
    

    //*判断图片上传格式是否为图片 return返回文件后缀
    public function getImgType($filename)
    {
        $file = fopen($filename, 'rb');
        $bin  = fread($file, 2); //只读2字节
        fclose($file);
        $strInfo  = @unpack('C2chars', $bin);
        $typeCode = intval($strInfo['chars1'].$strInfo['chars2']);
        $fileType = '';
        switch ($typeCode) {
            case 255216:
                $fileType = 'jpg';
                break;
            case 7173:
                $fileType = 'gif';
                break;
            case 6677:
                $fileType = 'bmp';
                break;
            case 13780:
                $fileType = 'png';
                break;
            default:
                $fileType = 1;
        }
        return $fileType;
    }


    //正则匹配图片、视频资源公共方法   暂时没有用到
    //$type  1图片，2视频
    public function pregUrl($params, $type = 1){
        $img_str = $params;
        $img_str=stripslashes($img_str);
        switch ($type)
        {
            case 1: //正则匹配图片链接
                $preg = "|src=\"(/ueditor/php/upload/image.+?)\".*?>|";
                break;
            case 2: //正则匹配视频链接
                $preg = "|src=\"(/ueditor/php/upload/video.+?)\".*?>|";
                break;
            default:
                $preg = "";
        }
        preg_match_all($preg,$img_str,$res);
        $url = implode(',',$res[1]);
        return $url;
    }


    //新闻草稿箱
    public function newsDraft(){
        $new = new NewsModel();
        $advertise = new Advertisements();
        $column = new Column();
        $column_id = input('param.name');//新闻分类 name字段表示分类
        $field  = 'n.id,title,column_ids,author,summary,detail,sort,source_type,is_banner,is_show,n.create_time,allow_transmit,allow_ad,source,a.name';
        // 搜索条件
        $param = input();
        if(!empty($param['title'])) {
            $news_where ['title'] = ['like','%'.$param['title'].'%'];
            $this->assign('title',$param['title']);
        }
        if(!empty($param['source'])) {
            $news_where ['source'] =['like','%'.$param['source'].'%'];
            $this->assign('source',$param['source']);
        }
        if(!empty($param['author'])) {
            $news_where['author'] = ['like','%'.$param['author'].'%'];
            $this->assign('author',$param['author']);
        }
        //栏目列表
        $c_list_arr = ['c.is_show'=>1,'c.is_delete'=>0,'parent_id'=>0];
        $first_list = $column->getColumns($c_list_arr,'c.*');//所有栏目
        $columnArr = [];
        foreach($first_list as $f_k=>$f_v){
            if($f_v['title']=='深度'){
                $p_id = ['parent_id'=>$f_v['id']];
                $second_list = $column->getColumns($p_id,'c.*');//获取二级栏目
                foreach($second_list as $s_k=>$s_v){
                    $columnArr[] = [$f_v['title'].'-->'.$s_v['title'],$s_v['id']];//二级栏目显示处理
                }
            }else{
                $columnArr[] = [$f_v['title'],$f_v['id']];
            }
        }
        if(isset($column_id)){//新闻草稿箱的分类处理
            if($column_id==0){ //全部分类
                $column_where = array('is_show'=>0);
                $column_list = $new->getNews($column_where,$field);
            }else{

                $column_where['is_delete'] = array('=',0);
                $column_where['is_show'] = array('=',1);
                $column_where[] = ['exp',"FIND_IN_SET($column_id,column_ids)"];
                $column_list = $new->getNews($column_where,$field);
            }
            foreach($column_list as $c_k=>$c_v){
                $c_name = [];
                $column_list[$c_k]['create_time'] = !empty($l_v['create_time'])?date('Y-m-d H:i:s',$l_v['create_time']):'';
                if($c_v['is_banner']==1){//处理新闻草稿箱列表的发布位置
                    $column_list[$c_k]['position'] = 'banner';
                }else{
                    $column_ids['c.id'] = ['in',$c_v['column_ids']];
                    $c_list = $column->getColumns($column_ids);//栏目列表
                    foreach($c_list as $k=>$v){
                        $c_name[] = $v['title'];
                    }
                    $column_list[$c_k]['position'] = implode(',',$c_name);
                }
                $column_list[$c_k]['aa'] = $c_v['column_ids'];//aa 字段没实际意义,仅仅用来表示栏目id
                $column_list[$c_k]['allow_ad'] = str_replace(array(0,1),array('否','是'),$c_v['allow_ad']);
                $operate = [
                    '编辑' => url('newsmanage/editNews', ['id' => $c_v['id']]),
                    '删除' => "javascript:delnews('".$c_v['id']."')",
                ];
                $column_list[$c_k]['operate'] = $operate;
            }
            $this->assign('list',$column_list);
            $this->assign('column_info',$columnArr);
            $ad_name[0] = $column_id;
            $this->assign('column',$ad_name);
        }else{
            $news_where['is_show'] = 0;
            $list = $new->getNews($news_where,$field);//新闻列表
            foreach($list as $l_k=>$l_v){
                $c_name = [];
                $list[$l_k]['create_time'] = !empty($l_v['create_time'])?date('Y-m-d H:i:s',$l_v['create_time']):"";
                if($l_v['is_banner']==1){
                    $list[$l_k]['position'] = 'banner';
                }else{
                    $column_where['c.id'] = ['in',$l_v['column_ids']];
                    $c_list = $column->getColumns($column_where);//栏目列表
                    foreach($c_list as $k=>$v){
                        $c_name[] = $v['title'];
                    }
                    $list[$l_k]['position'] = implode(',',$c_name);
                }
                $list[$l_k]['aa'] = $l_v['column_ids'];//aa 字段没实际意义,仅仅用来表示栏目id
                $list[$l_k]['allow_ad'] = str_replace(array(0,1),array('否','是'),$l_v['allow_ad']);
                $operate = [
                    '编辑' => url('newsmanage/editNews', ['id' => $l_v['id']]),
                    '删除' => "javascript:delnews('".$l_v['id']."')",
                ];
                $list[$l_k]['operate'] = $operate;
            }
            $this->assign('list',$list);
            $this->assign('column_info',$columnArr);
            $ad_name[0] = 0;
            $this->assign('column',$ad_name);
        }
        return $this->fetch();
    }


    //新闻草稿箱删除
    public function delNewsDraft(){
        if(request()->isPost()){
            $code = 1;
            $msg = '删除成功';
            $id = input('param.id');
            $new = new NewsModel();
            $banner = new BannerModel();
            // 启动事务
            Db::startTrans();
            try{
                $where_new = ['id'=>$id];
                $result = $new->delNews($where_new);
                $where_banner = ['out_id'=>$id];
                $banner->delBanner($where_banner);
                // 提交事务
                Db::commit();
            }catch(\PDOException $e){
                //回滚事务
                Db::rollback();
                $code = 0;
                $msg = '保存失败';
            }
            return json(['code'=>$code, 'msg'=>$msg]);
        }
        return $this->fetch();
    }



//新闻下拉列表  暂时没用到
    public function spinner(){
        $new = new NewsModel();
        $column = new Column();
        $title = input('param.name');
        $column_wh = array('title'=>$title);
        $column_id = $column->getSingleColumn($column_wh);
        $cid = $column_id['id'];
        $where[] = ['exp',"FIND_IN_SET($cid,id)"];
        $news_arr = $new->getNews($where);
        $this->assign('list',$news_arr);
        return $this->fetch('/newsmanage/newslist');
    }




    //新闻详情列表
    public function newsDetail(){
        $news = new NewsModel();
        if(request()->isAjax()){
            $param = input('param.');
            $limit = $param['pageSize'];
            $offset = ($param['pageNumber'] - 1) * $limit;
            $where = [];
            if (isset($param['searchText']) && !empty($param['searchText'])) {
                $where['author'] = ['like', '%' . $param['searchText'] . '%'];
            }

            $selectResult = $news->getNews($where, $offset, $limit);
            foreach($selectResult as $key=>$vo){
                $operate = [
                    '编辑' => url('role/newsEdit', ['id' => $vo['id']]),
                    '删除' => "javascript:newsDel('".$vo['id']."')",
                ];
                $selectResult[$key]['operate'] = showOperate($operate);
            }
            $return['total'] = $news->getAllNews($where);  //总数据
            $return['rows'] = $selectResult;
            return json($return);
        }
        return $this->fetch();
    }


    //新闻热点列表
    public function hotList(){
        $news = new NewsModel();
        if(request()->isAjax()){
            $param = input('param.');
            $limit = $param['pageSize'];
            $offset = ($param['pageNumber'] - 1) * $limit;
            $where = [];
            if (isset($param['searchText']) && !empty($param['searchText'])) {
                $where['author'] = ['like', '%' . $param['searchText'] . '%'];
            }
            $selectResult = $news->getNews($where, $offset, $limit);
            foreach($selectResult as $key=>$vo){
                $operate = [
                    '编辑' => url('role/newsEdit', ['id' => $vo['id']]),
                    '删除' => "javascript:newsDel('".$vo['id']."')",
                ];
                $selectResult[$key]['operate'] = showOperate($operate);
            }
            $return['total'] = $news->getAllNews($where);  //总数据
            $return['rows'] = $selectResult;
            return json($return);
        }
        return $this->fetch();
    }
    //summernote富文本编辑器上传文件
    public function test(){
        $file = request()->file('image');
        if($file){
            $info = $file->move(ROOT_PATH . 'public' . DS . 'upload');
            if($info){
                $imgPath['imgPath'] = config('zqrb')['domain'].'/upload/'.''.$info->getSaveName();   //dump($imgPath);exit;
                return message('ok',$imgPath);
            }else{
                // 上传失败获取错误信息
                return message('上传失败');
            }
        }
    }

}