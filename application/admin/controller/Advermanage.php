<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/5/30
 * Time: 15:04
 */
namespace app\admin\controller;
use \think\Db;
use \think\Request;
use app\admin\model\Advertisements;
use app\admin\model\NewsModel;
use app\admin\model\Column;
use app\admin\model\BannerModel;
use OSS\OssClient;
use OSS\Core\OssException;

class Advermanage extends Base{

    //广告发布
    public function advertisementPublish()
    {
        $param = $_POST;
        if(isset($param['id'])&&!empty($param['id'])){
            $startArray = isset($_FILES['startfile']) ? $_FILES['startfile'] : '';//启动页图片
            $bannerArray = isset($_FILES['bannerfile']) ? $_FILES['bannerfile'] : '';//banner页图片
            $newsArray = isset($_FILES['newsfile']) ? $_FILES['newsfile'] : '';//新闻列表页、新闻详情页图片
            $fileArr = [$startArray, $bannerArray, $newsArray];
            $this->editAdvertise($param,$fileArr);//新闻编辑保存
        }else{
            if (request()->isPost()) {
                $advertise = new Advertisements();
                $new = new NewsModel();
                $banner = new BannerModel();
                $column = new Column();
                $code = 1;
                $msg = '发布成功';
                $err_msg = '成功';
                $param = input('param.');
                $bannerArray = isset($_FILES['bannerfile']) ? $_FILES['bannerfile'] : '';//启动页图片
                $startArray = isset($_FILES['startfile']) ? $_FILES['startfile'] : '';//banner页图片
                $columnArray = isset($_FILES['newsfile']) ? $_FILES['newsfile'] : '';//新闻列表页、新闻详情页图片
                $fileArray = [$startArray, $bannerArray, $columnArray];
                // 启动事务
                Db::startTrans();
                try{
                    if(isset($param['publish1'])){
                        $img_url = $this->file_upload($fileArray,0,$param);//上传文件
                        //保存启动页广告
                        $ad_arr = $this->saveAdvertis($param);//广告字段处理
                        $result = $advertise->saveAdvertise($ad_arr);//保存广告
                        $ad_id = $result['adId'];
                        //保存广告资源
                        $this->saveSource($param,$img_url,$ad_id);
                    }elseif(isset($param['publish2'])){
                        $img_url = $this->file_upload($fileArray,1,$param);
                        //保存banner广告
                        $ad_arr = $this->saveAdvertis($param);
                        $result = $advertise->saveAdvertise($ad_arr);
                        $ad_id = $result['adId'];
                        //保存banner广告资源
                        $this->saveSource($param,$img_url,$ad_id);//保存广告资源表
                        //$this->bannerSource($param,$img_url,$ad_id);//保存banner表
                    }elseif(isset($param['publish3'])){
                        $ad_arr = $this->saveAdvertis($param);
                        $state = 2;
                        if($param['is_style']==0){//纯文本
                            $img_url = '';
                        }if($param['is_style']==1){
                            $state = 3;
                            $img_url = $this->file_upload($fileArray,$state,$param);
                        }if($param['is_style']==2){
                            $ad_arr['source_type'] = 5;
                            $img_url = $this->file_upload($fileArray,$state,$param);
                        }if($param['is_style']==3){
                            $img_url = $this->file_upload($fileArray,$state,$param);
                            $pic_type = $this->getImagetype($img_url);//获取上传文件类型
                            if($pic_type == 1){
                                $ad_arr['source_type'] = 4;
                            }else{
                                $ad_arr['source_type'] = 2;
                            }
                        }
                        //保存新闻列表页广告
                        $result = $advertise->saveAdvertise($ad_arr);
                        $ad_id = $result['adId'];
                        //保存广告资源
                        $this->saveSource($param,$img_url,$ad_id);
                    }
                    Db::commit();//提交事务
                }
                catch (\PDOException $e){
                    Db::rollback(); //回滚事务
                    $code = 0;
                    $msg = '发布失败';
                    $err_msg = $e->getMessage();
                }
                return json(['code' => $code, 'msg' => $msg, 'data' => $err_msg]);
            }
            $column = new Column();
            // $column_list = $column->getColumns('','c.*');
            // $this->assign('column_list',$column_list);
            return $this->fetch();
        }

    }

    /*
     * $param 发布广告字段
     * $url 发布广告图片上传链接
     * $id 广告id
     保存广告资源
    */
    public function saveSource($param,$url,$id){
        $advertise = new Advertisements();
        $source['ad_id'] = $id;
        $source['create_time'] = time();
        $source['update_time'] = time();
        if(isset($param['publish1'])){
            $source['is_show'] = $param['publish1'];//是否显示 0:否,1:是
        }elseif(isset($param['publish2'])){
            $source['is_show'] = $param['publish2'];//是否显示 0:否,1:是
        }elseif(isset($param['publish3'])){
            $source['is_show'] = $param['publish3'];//是否显示 0:否,1:是
        }
        if(is_array($url)){//如果是多文件上传
            foreach($url as $url_k=>$url_v){
                if(!empty($url_v)){
                    $source['pic_path'] = $url_v;
                    $advertise->saveAdverSource($source);//保存广告资源信息
                }
            }
        }else{//如果是单文件上传
            if(!empty($url)){
                $pic_type = $this->getImagetype($url);
                if($pic_type==1){//视频
                    $source['pic_path'] = $url;
                    $source['type'] = 2;
                    $advertise->saveAdverSource($source);//保存广告资源信息
                    $source2 = $source;
                    $source2['type'] = 1;
                    $source2['pic_path'] = $url.'?x-oss-process=video/snapshot,t_1000,f_jpg,w_800,h_600,m_fast';//处理上传视频缩略图
                    $advertise->saveAdverSource($source2);
                }else{
                    $source['pic_path'] = $url;
                    $source['type'] = 1;
                    $advertise->saveAdverSource($source);
                }
            }else{
                $advertise->saveAdverSource($source);
            }
        }
    }


    /*
     * $param 发布广告字段
     *保存广告
     */
    public function saveAdvertis($param){
        if(isset($param['publish1'])){//发布起始页广告
            $ad_arr['posi_type'] = 1;//位置类型 1:起始页 2:banner；3:新闻列表；4:详情
            $ad_arr['source_type'] = 1;
            $ad_arr['out_url'] = '';
            $ad_arr['title'] = '';
            $ad_arr['description'] = '';
            $ad_arr['summary'] = '摘要摘要';
            $ad_arr['circle_time_start'] = !empty($param['start_stime'])?strtotime($param['start_stime']):'0';
            $ad_arr['circle_time_end'] = !empty($param['start_etime'])?strtotime($param['start_etime']):'3000000000';
            $ad_arr['type'] = 0;//广告类型 0:启动页,1焦点广告,2:列表页广告
            $ad_arr['is_show'] = $param['publish1'];//是否显示 0:否,1:是
        }elseif(isset($param['publish2'])){//发布banner页广告
            $ad_arr['out_url'] = isset($param['baner_url'])?$param['baner_url']:'';
            $ad_arr['title'] = isset($param['baner_title'])?$param['baner_title']:'';
            $ad_arr['description'] = isset($param['baner_summery'])?$param['baner_summery']:'';
            $ad_arr['summary'] = isset($param['baner_cont'])?$param['baner_cont']:'';
            $ad_arr['style'] = 0;
            $ad_arr['is_show'] = $param['publish2'];
            $ad_arr['state'] = $param['bner_show'];
            $ad_arr['type'] = 1;//广告类型 0:启动页,1焦点广告,2:列表页广告
            $ad_arr['posi_type'] = 2;
            $ad_arr['source_type'] = 1;
            $ad_arr['circle_time_start'] = !empty($param['banner_stime'])?strtotime($param['banner_stime']):"0";
            $ad_arr['circle_time_end'] = !empty($param['banner_etime'])?strtotime($param['banner_etime']):"3000000000";
        }elseif(isset($param['publish3'])){//发布新闻列表或详情页广告  detail作为标识
            $ad_arr['out_url'] = isset($param['news_url'])?$param['news_url']:"";
            $ad_arr['title'] = isset($param['news_title'])?$param['news_title']:"";
            $ad_arr['description'] = isset($param['news_summary'])?$param['news_summary']:"";
            $ad_arr['summary'] = isset($param['news_cont'])?$param['news_cont']:"";
            $ad_arr['is_show'] = $param['publish3'];
            $ad_arr['state'] = $param['ad_state'];
            $ad_arr['style'] = $param['is_style'];
            if($param['detail']==1){//detail 1 详情   2 列表页广告
                $ad_arr['posi_type'] = 4;
                $ad_arr['type'] = 3;//广告类型 0:启动页,1焦点广告,2:列表页广告,3:详情
            }else{
                $ad_arr['posi_type'] = 3;
                $ad_arr['type'] = 2;//广告类型 0:启动页,1焦点广告,2:列表页广告,3:详情
            }
            $ad_arr['position'] = 0;//广告次序
            $ad_arr['circle_time_start'] = !empty($param['new_stime'])?strtotime($param['new_stime']):"0";
            $ad_arr['circle_time_end'] = !empty($param['new_etime'])?strtotime($param['new_etime']):"3000000000";
        }
        $ad_arr['create_time'] = time();
        $ad_arr['update_time'] = time();
        $ad_arr['auther'] = '作者';
        $ad_arr['source'] = '证券日报';
        return $ad_arr;
    }

    /*
     * $param banner相关字段
     * 保存banner页广告
     */
    public function saveBanner($param){
        $banner_arr['out_id'] = '';
        $banner_arr['description'] = '';
        $banner_arr['out_url'] = '';
        $banner_arr['circle_time_start'] = $param['start_stime'];
        $banner_arr['circle_time_end'] = isset($param['start_etime'])?$param['start_etime']:'';
        $banner_arr['is_show'] = $param['publish'];//是否显示 0:否,1:是
        $banner_arr['type'] = 0;//广告类型 0:启动页,1焦点广告,2:列表页广告
        $banner_arr['create_time'] = time();
        $banner_arr['update_time'] = time();
        $banner_arr['auther'] = '作者';
        $banner_arr['summary'] = '摘要摘要';
        $banner_arr['source'] = '证券日报';
        $banner_arr['posi_type'] = 1;//位置类型 1:起始页 2:banner；3:新闻列表；4:新闻详情
        $banner_arr['source_type'] = 2;//新闻资源类型 1:纯文本,2:单图,3多图,4:视频'
        return $banner_arr;
    }


    //保存banner广告资源
    public function bannerSource($param,$url,$id){
        $banner = new BannerModel();
        $banner_arr['out_id'] = $id;
        $banner_arr['create_time'] = time();
        $banner_arr['update_time'] = time();
        $banner_arr['is_show'] = 1;//是否显示 0:否,1:是
        $banner_arr['type'] = 1;//banner类型 0:新闻,1:广告
//        $banner_arr['sort'] = $param['banner_position'];//banner类型 0:新闻,1:广告
        if(!empty($url)){
            $banner_arr['picture_path'] = $url;
            $banner->saveBanner($banner_arr);
        }else{
            $banner->saveBanner($banner_arr);
        }
    }



    //编辑广告
    public function editAdvertise($param = '',$fileArr = []){
        $advertise = new Advertisements();
        $table = 'advertisements';
        if (!empty($param)) {
            $code = 1;
            $msg = '编辑保存成功';
            $err_msg = '成功';
            // 启动事务
            Db::startTrans();
            try{
                if(isset($param['publish1'])){//启动页广告
                    $img_url = $this->file_upload($fileArr,0,$param);
                    //更新保存启动页广告
                    $ad_arr = $this->editAdver($param);
                    $where_id = ['id'=>$param['id']];
                    $advertise->updateNews($where_id,$ad_arr,$table);//更新
                    //更新保存广告资源
                    $this->editSource($param,$img_url,$param['id']);//更新
                }elseif(isset($param['publish2'])){//banner广告
                    $img_url = $this->file_upload($fileArr,1,$param);
                    //更新保存广告
                    $ad_arr = $this->editAdver($param);
                    $where_id = ['id'=>$param['id']];
                    $advertise->updateNews($where_id,$ad_arr,$table);//更新
                    //更新保存广告资源
                    $this->editSource($param,$img_url,$param['id']);//更新
                }elseif(isset($param['publish3'])){//新闻列表页或详情页广告
                    $ad_arr = $this->editAdver($param);
                    $state = 2;
                    $img_url = '';
                    if($param['is_style']==1){//is_style 0存文本，1三图，2单小图，3单大图或视频
                        $state = 3;
                        $ad_arr['source_type'] = 3;
                        $img_url = $this->file_upload($fileArr,$state,$param);
                    }
                    if($param['is_style']==2){
                        $ad_arr['source_type'] = 5;
                        $img_url = $this->file_upload($fileArr,$state,$param);
                    }
                    if($param['is_style']==3){
                        $img_url = $this->file_upload($fileArr,$state,$param);
                        if(!empty($img_url)){
                            $pic_type = $this->getImagetype($img_url);
                            if($pic_type == 1){
                                $ad_arr['source_type'] = 4;
                            }else{
                                $ad_arr['source_type'] = 2;
                            }
                        }
                    }

                    //更新保存列表广告
                    $where_id = ['id'=>$param['id']];
                    $advertise->updateNews($where_id,$ad_arr,$table);//更新
                    //更新保存广告资源
                    if($param['is_style']==0){//纯文本
                        $where_del['ad_id'] = $param['id'];
                        $advertise->delSource($where_del);//删除原图片资源
                    }else{
                        $this->editSource($param,$img_url,$param['id']);//更新
                    }
                }
                Db::commit();//提交事务
            }
            catch (\PDOException $e){
                Db::rollback(); //回滚事务
                $code = 0;
                $msg = '发布失败';
                $err_msg = $e->getMessage();
            }
            return json(['code' => $code, 'msg' => $msg, 'data' => $err_msg]);
        }else{//编辑渲染页面
            $id = input('param.id');
            $where_id = array('id'=>$id);
            $list = $advertise->getAdvertise($where_id);//广告列表信息
            $where_sid['ad_id'] = array('in',$list[0]['id']);
            $source = $advertise->getSource($where_sid);//广告资源信息
            foreach($list as $l_k=>$l_v){
                $list[$l_k]['crete_time'] = !empty($l_v['create_time'])?date('Y-m-d H:i:s',$l_v['create_time']):'';
                $list[$l_k]['update_time'] = !empty($l_v['update_time'])?date('Y-m-d H:i:s',$l_v['update_time']):'';
                if($l_v['circle_time_start']=='0'){//如果开始推广周期为零，做空字符串处理
                    $list[$l_k]['circle_time_start'] = '';
                }else{
                    $list[$l_k]['circle_time_start'] = date('Y-m-d H:i:s',intval($l_v['circle_time_start']));
                }
                if($l_v['circle_time_end']=='3000000000'){//如果截止推广周期为3000000000，做空字符串处理
                    $list[$l_k]['circle_time_end'] = '';
                }else{
                    $list[$l_k]['circle_time_end'] = date('Y-m-d H:i:s',intval($l_v['circle_time_end']));
                }
//                $list[$l_k]['circle_time_start'] = !empty($l_v['circle_time_start'])?date('Y-m-d H:i:s',intval($l_v['circle_time_start'])):'';
//                $list[$l_k]['circle_time_end'] = !empty($l_v['circle_time_end'])?date('Y-m-d H:i:s',intval($l_v['circle_time_end'])):'';
            }
            $this->assign('list',$list);
            if(!empty($source)){//广告图片资源处理
                foreach($source as $s_k=>$s_v){
                    $source_arr[0]['pic_path'][$s_k] = $s_v['pic_path'];//广告图片路径处理 存在赋值，不存在赋空值
                }
            }else{
                $source = [];
                $source_arr[0]['pic_path'] = '';
            }

            $source_arr[0]['pic_num'] = count($source);//暂时未用到
            $this->assign('source',$source_arr);
            $column = new Column();
            $column_list = $column->getColumns('','*');
            $this->assign('column_list',$column_list);
            $detail = $list[0];
            $detail['pic_path'] = $source_arr[0]['pic_path'];
            $this->assign('detail',$detail);
            return $this->fetch('advermanage/advertisementpublish');
        }
    }


    /*
     * $param  编辑广告参数
     * $url  编辑广告生成的图片路径
     * $pid  编辑广告生成id
     编辑广告资源表
    */
    public function editSource($param,$url,$pid){
        $table = 'advertisementpics';
        $advertise = new Advertisements();
        $source['update_time'] = time();
        if(isset($param['publish1'])){
            $source['is_show'] = $param['publish1'];//是否显示 0:否,1:是
        }elseif(isset($param['publish2'])){
            $source['is_show'] = $param['publish2'];//是否显示 0:否,1:是
        }elseif(isset($param['publish3'])){
            $source['is_show'] = $param['publish3'];//是否显示 0:否,1:是
        }

        //获取广告资源信息
        $where_sid = ['ad_id'=>$pid];
        $ad_arr = $advertise->getSource($where_sid);
        if(is_array($url)){//如果，多文件上传  循环保存广告资源信息
            foreach($url as $url_k=>$url_v){
                if(!empty($url_v)){
                    $source['pic_path'] = $url_v;//资源路径
                    if (!isset($ad_arr[$url_k]['id'])) {//如果编辑的广告图片个数与原图片有对应，更新
                        $source['ad_id'] = $pid;
                        $source['create_time'] = time();
                        $advertise->saveAdverSource($source);
                    } else {   //如果编辑的广告图片个数与原图片没有对应，插入新的编辑图片
                        $source_id = ['id' => $ad_arr[$url_k]['id']];
                        $source['type'] = 1;
                        $advertise->updateNews($source_id, $source, $table);
                    }
                }
            }
        }else{
            if(!empty($url)){//删除原来的所有图片资源信息，重新保存编辑后的文件资源信息
                //删除原来数据
                $del_sid = ['ad_id'=>$pid];
                $advertise->delSource($del_sid);
                //保存新数据  没有更新  没有删除原有数据  style样式没有更新
                $pic_type = $this->getImagetype($url);
                $source['pic_path'] = $url;
                $source['ad_id'] = $pid;
                $source['create_time'] = time();
                $source['update_time'] = time();
                $advertise->saveAdverSource($source);
                if($pic_type==1){//视频
                    $source2 = $source;
                    $source2['type'] = 2;
                    $source2['pic_path'] = $url.'?x-oss-process=video/snapshot,t_1000,f_jpg,w_800,h_600,m_fast';//处理上传的视频的缩略图
                    $advertise->saveAdverSource($source2);
                }
            }
        }
    }

    //编辑广告表
    public function editAdver($param){
        if(isset($param['publish1'])){//启动页
            $ad_arr['circle_time_start'] = !empty($param['start_stime'])?strtotime($param['start_stime']):"0";
            $ad_arr['circle_time_end'] = !empty($param['start_etime'])?strtotime($param['start_etime']):'3000000000';
            $ad_arr['is_show'] = $param['publish1'];//是否显示 0:否,1:是
            $ad_arr['style'] = 0;
            $ad_arr['type'] = 0;//广告类型 0:启动页,1焦点广告,2:列表页广告
            $ad_arr['source_type'] = 1;
            $ad_arr['posi_type'] = 1;
            $ad_arr['type'] = 0;//广告类型 0:启动页,1焦点广告,2:列表页广告,3:详情
        }elseif(isset($param['publish2'])){//banner页
            $ad_arr['out_url'] = isset($param['baner_url'])?$param['baner_url']:'';
            $ad_arr['title'] = isset($param['baner_title'])?$param['baner_title']:'';
            $ad_arr['description'] = isset($param['baner_cont'])?$param['baner_cont']:'';
            $ad_arr['summary'] = isset($param['baner_summary'])?$param['baner_summary']:'';
            $ad_arr['is_show'] = $param['publish2'];
            $ad_arr['type'] = 1;//广告类型 0:启动页,1焦点广告,2:列表页广告
            $ad_arr['posi_type'] = 2;
            $ad_arr['state'] = $param['bner_show'];
            $ad_arr['style'] = 0;
            $ad_arr['source_type'] = 1;
            $ad_arr['circle_time_start'] = !empty($param['banner_stime'])?strtotime($param['banner_stime']):"0";
            $ad_arr['circle_time_end'] = !empty($param['banner_etime'])?strtotime($param['banner_etime']):'3000000000';
        }elseif(isset($param['publish3'])){//新闻列表页、详情页  detail区别
            $ad_arr['out_url'] = isset($param['news_url'])?$param['news_url']:'';
            $ad_arr['title'] = isset($param['news_title'])?$param['news_title']:'';
            $ad_arr['description'] = isset($param['news_summary'])?$param['news_summary']:'';
            $ad_arr['summary'] = isset($param['news_cont'])?$param['news_cont']:'';
            $ad_arr['is_show'] = $param['publish3'];
            $ad_arr['state'] = $param['ad_state'];
            $ad_arr['style'] = $param['is_style'];
            if(!empty($param['detail'])&&$param['detail']==4){
                $ad_arr['posi_type'] = 4;
                $ad_arr['type'] = 3;//广告类型 0:启动页,1焦点广告,2:列表页广告,3:详情
            }else{
                $ad_arr['posi_type'] = 3;
                $ad_arr['type'] = 2;//广告类型 0:启动页,1焦点广告,2:列表页广告,3:详情
            }
            $ad_arr['position'] = 0;//广告次序
            $ad_arr['circle_time_start'] = !empty($param['new_stime'])?strtotime($param['new_stime']):'0';
            $ad_arr['circle_time_end'] = !empty($param['news_etime'])?strtotime($param['news_etime']):'3000000000';
        }
        $ad_arr['update_time'] = time();
        return $ad_arr;
    }


    //广告列表
    public function advlist(){
        $advertise = new Advertisements();
        $ad_name = input('param.name');//广告分类  name字段表示分类id
        $advertise_name = input('param.adname');//广告搜索 广告名称
        $filed  = 'id,title,description,out_url,circle_time_start,circle_time_end,type,create_time,is_show,auther,summary,position,posi_type,source_type';
        if(isset($ad_name)){
            if(isset($advertise_name)){//获取广告搜索条件
                $column_where['title'] = ['like',"%$advertise_name%"];
                $this->assign('adname',$advertise_name);
            }
            if($ad_name==0){
                //全部分类
                $column_where = array('is_show'=>1);
                $column_list = $advertise->getAdvertise($column_where,$filed);
            }else{
                $column_where = array('posi_type'=>$ad_name,'is_show'=>1);
                $column_list = $advertise->getAdvertise($column_where,$filed);
            }
            foreach($column_list as $c_k=>$c_v){//结束推广周期为3000000000表示永久有效
                if($c_v['circle_time_end']=='3000000000'){
                    $column_list[$c_k]['extension_time'] = '永久有效';
                }elseif($c_v['circle_time_start']=='0'&&$c_v['circle_time_end']!='3000000000'){
                    $column_list[$c_k]['extension_time'] = date('Y-m-d',intval($c_v['circle_time_end']));
                }elseif($c_v['circle_time_start']!='0'&&$c_v['circle_time_end']!='3000000000'){
                    $column_list[$c_k]['extension_time'] = date('Y-m-d',intval($c_v['circle_time_start'])).'---'.date('Y-m-d',intval($c_v['circle_time_end']));
                }
//                $column_list[$c_k]['circle_time_start'] = !empty($c_v['circle_time_start'])?date('Y-m-d',intval($c_v['circle_time_start'])):'';
//                $column_list[$c_k]['circle_time_end'] = !empty($c_v['circle_time_end'])?date('Y-m-d',intval($c_v['circle_time_end'])):'';
                $column_list[$c_k]['create_time'] = !empty($c_v['create_time'])?date('Y-m-d H:i:s',$c_v['create_time']):'';
                $column_list[$c_k]['posi_type'] = str_replace(array(1,2,3,4,5),array('启动页','banner','新闻列表页','新闻详情','深度'),$c_v['posi_type']);//广告发布位置
                $operate = [
                    '编辑' => url('advermanage/editAdvertise', ['id' => $c_v['id']]),
                    '下线' => "javascript:downline('".$c_v['id']."')",
                ];
                $column_list[$c_k]['operate'] = $operate;
            }
            $this->assign('list',$column_list);
            $ad_name[0] = $ad_name;
            $this->assign('column',$ad_name);
        }else{
            $column_where = array('is_show'=>1,'is_delete'=>0);
            if(isset($advertise_name)){//获取广告搜索条件
                $column_where['title'] = ['like',"%$advertise_name%"];
                $this->assign('adname',$advertise_name);
            }
            $list = $advertise->getAdvertise($column_where,$filed);
            foreach($list as $l_k=>$l_v){
                if($l_v['circle_time_end']=='3000000000'){
                    $list[$l_k]['extension_time'] = '永久有效';
                }elseif($l_v['circle_time_start']=='0'&&$l_v['circle_time_end']!='3000000000'){
                    $list[$l_k]['extension_time'] = date('Y-m-d',intval($l_v['circle_time_end']));
                }elseif($l_v['circle_time_start']!='0'&&$l_v['circle_time_end']!='3000000000'){
                    $list[$l_k]['extension_time'] = date('Y-m-d',intval($l_v['circle_time_start'])).'---'.date('Y-m-d',intval($l_v['circle_time_end']));
                }
//                $list[$l_k]['circle_time_start'] = !empty($l_v['circle_time_start'])?date('Y-m-d',intval($l_v['circle_time_start'])):'';
//                $list[$l_k]['circle_time_end'] = !empty($l_v['circle_time_end'])?date('Y-m-d',intval($l_v['circle_time_end'])):'';
                $list[$l_k]['create_time'] = !empty($l_v['create_time'])?date('Y-m-d H:i:s',$l_v['create_time']):'';
                $list[$l_k]['posi_type'] = str_replace(array(1,2,3,4,5),array('启动页','banner','新闻列表页','新闻详情','深度'),$l_v['posi_type']);//广告位置
                $operate = [
                    '编辑' => url('advermanage/editAdvertise', ['id' => $l_v['id']]),
                    '下线' => "javascript:downline('".$l_v['id']."')",
                ];
                $list[$l_k]['operate'] = $operate;
            }
            $this->assign('list',$list);
            $ad_name[0] = 0;
            $this->assign('column',$ad_name);
        }
        return $this->fetch();
    }


    //广告下线
    public function adDownline(){
        $code = 1;
        $msg = '下线成功';
        $advertise = new Advertisements();
        $table = 'advertisements';
        $ad_id = input('param.id');
        // 启动事务
        Db::startTrans();
        try{
            //更新广告资源表
            $where_source = ['ad_id'=>$ad_id];
            $update_source = ['is_show'=>0];
            $advertise->updateSource($where_source,$update_source);
            //更新广告表
            $where_id = ['id' => $ad_id];
            $update_arr['is_show'] = 0;
            $advertise->updateNews($where_id, $update_arr, $table);
            Db::commit();//提交事务
        }
        catch (\PDOException $e){
            Db::rollback(); //回滚事务
            $code = 0;
            $msg = '发布失败';
        }
        return json(['code' => $code, 'msg' => $msg]);
    }


    //$fileArray 文件数组
    //$order 1,2是启动页图   3,4是banner页图  5,6是新闻列表页图
    public function file_upload($fileArray,$order,$param){
        require_once  __DIR__."/../../../vendor/aliyun_oss/autoload.php";//引入阿里云oss
        //阿里云配置
        $accessKeyId = config('oss_file_conf')['accessKeyId'];
        $accessKeySecret = config('oss_file_conf')['accessKeySecret'];
        $endpoint = config('oss_file_conf')['endpoint'];
        $bucket= config('oss_file_conf')['bucket'];
        $filePath = ["serverData/advertising/","serverData/audio/","serverData/video/"];//文件上传路径
        if($order==0 || $order==1 || $order==2){//单张
            $file_name = '';//文件名
            if ($fileArray[$order]['error'] == 0) { //表示上传没有出错
                $tmp_name = $fileArray[$order]['tmp_name'];
                $file_name = $fileArray[$order]['name'];
                $suffix = substr($file_name, strrpos($file_name, '.')+1);//文件后缀
                $file_name = date('YmdHis').mt_rand(10000,99999).'.'.$suffix;
                $file_name = iconv("UTF-8", "gbk", $file_name);
                $file = $tmp_name;//$file 本地文件路径
                //您要创建的Object的名称。
                if(isset($param['is_style']) && $param['is_style'] == 3){ //添加新闻列表广告   图片或音频
                    $file_type = $this->getImagetype($tmp_name);
                    if($file_type == 1){//非图片
                        $object_url = $filePath[2].$file_name;
                    }else{
                        $object_url = $filePath[0].$file_name;
                    }
                }else{
                    $object_url = $filePath[0].$file_name;
                }
                $ossClient = new OssClient($accessKeyId, $accessKeySecret, $endpoint);
                $ossClient->uploadFile($bucket, $object_url, $file);//调用阿里云方法处理上传文件
                $fname = $endpoint.'/'.$bucket.'/'.$object_url;
            }else{
                $fname = '';
            }
        }
        if($order==3) {//多张
            $fname = array();
            foreach ($fileArray[2]['error'] as $key => $error) {
                if ($error == 0) { //表示上传没有出错
                    $tmp_name = $fileArray[2]['tmp_name'][$key];
                    $file_name = $fileArray[2]['name'][$key];
                    $suffix = substr($file_name, strrpos($file_name, '.') + 1);//文件后缀
                    $file_name = date('YmdHis') . mt_rand(10000, 99999) . '.' . $suffix;
                    $file_name = iconv("UTF-8", "gbk", $file_name);
                    $object_ = $filePath[0].$file_name;//$object object name
                    $file = $tmp_name;//$file 本地文件路径
                    $ossClient = new OssClient($accessKeyId, $accessKeySecret, $endpoint);
                    $ossClient->uploadFile($bucket, $object_, $file);
                    $fname[$key] = $endpoint.'/'.$bucket.'/'.$object_;
                }else{
                    $fname[$key] = '';
                }
            }
        }
        return $fname;
    }


    //*判断图片上传格式是否为图片 return返回文件后缀
    public function getImagetype($filename)
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


    //广告草稿箱列表
    public function adverDraft(){
        $advertise = new Advertisements();
        $ad_name = input('param.name');//广告分类 name字段表示分类搜索条件
        $filed  = 'id,title,description,out_url,circle_time_start,circle_time_end,type,create_time,is_show,auther,summary,position,posi_type,source_type';
        if(isset($ad_name)){
            if($ad_name==0){//广告分类
                //全部分类
                $column_where = array('is_show'=>0);
                $column_list = $advertise->getAdvertise($column_where,$filed);
            }else{
                $column_where = array('posi_type'=>$ad_name,'is_show'=>0);
                $column_list = $advertise->getAdvertise($column_where,$filed);
            }
            foreach($column_list as $c_k=>$c_v){
                if($c_v['circle_time_end']=='3000000000'){ //结束推广周期为3000000000表示永久有效
                    $column_list[$c_k]['extension_time'] = '永久有效';
                }elseif($c_v['circle_time_start']=='0'&&$c_v['circle_time_end']!='3000000000'){
                    $column_list[$c_k]['extension_time'] = date('Y-m-d',intval($c_v['circle_time_end']));
                }elseif($c_v['circle_time_start']!='0'&&$c_v['circle_time_end']!='3000000000'){
                    $column_list[$c_k]['extension_time'] = date('Y-m-d',intval($c_v['circle_time_start'])).'---'.date('Y-m-d',intval($c_v['circle_time_end']));
                }
//                $column_list[$c_k]['circle_time_start'] = !empty($c_v['circle_time_start'])?date('Y-m-d',$c_v['circle_time_start']):'';
//                $column_list[$c_k]['circle_time_end'] = !empty($c_v['circle_time_end'])?date('Y-m-d',$c_v['circle_time_end']):'';
                $column_list[$c_k]['create_time'] = !empty($c_v['create_time'])?date('Y-m-d H:i:s',$c_v['create_time']):'';
                $column_list[$c_k]['posi_type'] = str_replace(array(1,2,3,4,5),array('启动页','banner','新闻列表页','详情','热点'),$c_v['posi_type']);
                $operate = [
                    '编辑' => url('advermanage/editAdvertise', ['id' => $c_v['id']]),
                    '删除' => "javascript:downline('".$c_v['id']."')",
                ];
                $column_list[$c_k]['operate'] = $operate;
            }
            $this->assign('list',$column_list);
            $ad_name[0] = $ad_name;
            $this->assign('column',$ad_name);
        }else{//列表
            $column_where = array('is_show'=>0);
            $list = $advertise->getAdvertise($column_where,$filed);
            foreach($list as $l_k=>$l_v){//结束推广周期为3000000000表示永久有效
                if($l_v['circle_time_end']=='3000000000'){
                    $list[$l_k]['extension_time'] = '永久有效';
                }elseif($l_v['circle_time_start']=='0'&&$l_v['circle_time_end']!='3000000000'){
                    $list[$l_k]['extension_time'] = date('Y-m-d',intval($l_v['circle_time_end']));
                }elseif($l_v['circle_time_start']!='0'&&$l_v['circle_time_end']!='3000000000'){
                    $list[$l_k]['extension_time'] = date('Y-m-d',intval($l_v['circle_time_start'])).'---'.date('Y-m-d',intval($l_v['circle_time_end']));
                }
//                $list[$l_k]['circle_time_start'] = !empty($l_v['circle_time_start'])?date('Y-m-d',$l_v['circle_time_start']):'';
//                $list[$l_k]['circle_time_end'] = !empty($l_v['circle_time_end'])?date('Y-m-d',$l_v['circle_time_end']):'';
                $list[$l_k]['create_time'] = !empty($l_v['create_time'])?date('Y-m-d H:i:s',$l_v['create_time']):'';
                $list[$l_k]['posi_type'] = str_replace(array(1,2,3,4,5),array('启动页','banner','新闻列表页','详情','热点'),$l_v['posi_type']);
                $operate = [
                    '编辑' => url('advermanage/editAdvertise', ['id' => $l_v['id']]),
                    '删除' => "javascript:downline('".$l_v['id']."')",
                ];
                $list[$l_k]['operate'] = $operate;
            }
            $this->assign('list',$list);
            $ad_name[0] = 9;
            $this->assign('column',$ad_name);
        }
        return $this->fetch();
    }

    //删除广告草稿箱
    public function delDraft(){
        $code = 1;
        $msg = '删除成功';
        $advertise = new Advertisements();
        $ad_id = input('param.id');
        // 启动事务
        Db::startTrans();
        try{
        //删除广告资源表数据
        $where_source = ['ad_id'=>$ad_id];
        $advertise->delSource($where_source);
        //删除广告数据
        $where_id = ['id'=>$ad_id];
        $advertise->delAdvertise($where_id);
            Db::commit();//提交事务
        }
        catch (\PDOException $e){
            Db::rollback(); //回滚事务
            $code = 0;
            $msg = '发布失败';
        }
        return json(['code' => $code, 'msg' => $msg]);
    }



}