<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/5/28
 * Time: 17:37
 */

namespace app\admin\controller;
use app\admin\model\NewsModel;
use app\admin\model\NewsSource;
use app\common\logic\News;
use \think\Db;
use \think\Request;
use app\admin\model\BannerModel;
use app\admin\model\Advertisements;
use app\admin\model\Column;

class Banner extends Base{

    //banner添加
    public function bannerAdd(){
        $code = 1;
        $msg = '保存成功';
        $err_msg = '保存成功';
        if(request()->isPost()){
            $param = input('param.');
            $banner = new BannerModel();
            $new = new NewsModel();
                // 启动事务
                Db::startTrans();
                try{
                    if(isset($param['banner_position'])) {
                        //更新前，先编辑原有数据
                        $where_del['is_show'] = 1;
                        $update_del['is_show'] = 0;
                        $banner->updateBannersAdver($where_del,$update_del);
                        foreach ($param['ad_position'] as $ad_k => $ad_v) {
                            $para_arr['position'] = $param['banner_position'][$ad_k];//广告在banner中相对应的位置
                            $para_arr['adver_id'] = $ad_v;//新闻广告id
                            $banner->saveBannerAdver($para_arr);
                        }
                    }
                    //保存banner数量字段到config表
                    $where_config['banner_num'] = trim($param['banner_num']);
                    $where_config['update_time'] = time();
                    $where_config['create_time'] = time();
                    //$banner->saveBannerNum($where_config);
                    $where['id'] = 1;
                    $banner->updateBannersConf($where_config,$where);
                    //删除相关banner新闻
                    //获取sort大于banner_num的banner新闻资源out_id 吧新闻表中banner新闻次序大于banner_num 的删除
                    $where_oid['sort'] = ['>',$param['banner_num']];
                    $where_oid['is_show'] = 1;
                    $where_oid['is_delete'] = 0;
                    $bane_ar = $banner->getBanners($where_oid);//获取相关新闻id（out_id）
                    $order_ids = [0];
                    foreach($bane_ar as $bane_v){
                        $order_ids[] = $bane_v['out_id'];
                    }
                    $table_new = 'news';
                    $where_news['id'] = ['in',$order_ids];
                    $param_new['is_show'] = 0;
                    $param_new['is_delete'] = 1;
                    $new->updateNews($where_news,$param_new,$table_new);//更新新闻表
                    $where_bane['sort'] = ['>',$param['banner_num']];
                    $update_bane['is_show'] = 0;
                    $update_bane['is_delete'] = 1;
                    $banner->updateBane($where_bane,$update_bane);//删除sort大于banner_num的banner新闻资源 吧新闻表中banner新闻次序大于banner_num 的删除
                    // 提交事务
                    Db::commit();
                }catch(\PDOException $e){
                    //回滚事务
                    Db::rollback();
                    $code = 0;
                    $msg = '保存失败';
                    $err_msg = $e->getMessage();
                }
                return json(['code' => $code, 'msg' => $msg, 'err_msg'=>$err_msg]);

        }
        //banner_num
        //渲染页面
        $banner = new BannerModel();
        $b_num = $banner->getBannersConf();//获取banner配置  scs_config表
        $this->assign('num',$b_num);
        $bid_whe = ['is_show'=>1];
        $banner_arr = $banner->getBannersAdver($bid_whe);//获取banner广告资源
        $this->assign('b_list',$banner_arr);
        //广告列表
        $advertising = new Advertisements();
        $where_title['is_show'] = ['=',1];
        $where_title['is_delete'] = ['=',0];
        $ad_list = $advertising->getAdvertise($where_title);
        foreach($ad_list as $ad_k=>$ad_v){
            $ad_list[$ad_k]['title'] = empty($ad_v['title'])?$ad_v['out_url']:$ad_v['title'];
        }
        $this->assign('a_list',$ad_list);
        return $this->fetch();
    }



    //banner列表
    public function bannerList(){
        $column = new BannerModel();
        $news = new NewsModel();
        $advertising = new Advertisements();
        $new_where['is_show'] = 1;
        $new_where['is_delete'] = 0;
        $new_where['is_banner'] = 1;
        $new_list = $news->getNewsNoPage($new_where);//获取banner新闻列表数据
        if(!empty($new_list)){
            foreach($new_list as $n_k=>$n_v){
                $sort = Db::table('scs_banner')->where(['out_id'=>$n_v['id']])->field('sort')->find();
                $new_list[$n_k]['order'] = $sort['sort'];//获取banner新闻的显示次序
            }
        }
        $ad_list['is_show'] = 1;
        $ad_list['is_delete'] = 0;
        $ad_list['posi_type'] = 2;
        $ad_list['circle_time_start'] = ['<=',time()];
        $ad_list['circle_time_end'] = ['>=',time()];
        $ad_arr = $advertising->getAdvertiseNoPage($ad_list);//获取banner广告列表数据
        if(!empty($ad_arr)){
            foreach($ad_arr as $a_k=>$a_v){
                $position = Db::table('scs_banner_adver')->where(['adver_id'=>$a_v['id']])->field('position')->find();
                $ad_arr[$a_k]['order'] = $position['position'];//获取banner广告的显示次序
            }
        }
        $arr_list = array_merge($new_list,$ad_arr);//新闻、广告列表数据合并
        foreach($arr_list as $c_k=>$c_v){
            $c_name = [];
            $arr_list[$c_k]['create_time'] = !empty($c_v['create_time'])?date('Y-m-d H:i:s',$c_v['create_time']):'';
            if(isset($c_v['is_banner'])){
                $arr_list[$c_k]['position'] = '新闻';
                $arr_list[$c_k]['author'] = $c_v['author'];
            }else{
                $arr_list[$c_k]['position'] = '广告';
                $arr_list[$c_k]['author'] = $c_v['auther'];
            }
            if(isset($c_v['is_banner'])) {
                $operate = [
                    '编辑' => url('banner/editBanner', ['id' => $c_v['id'], 'name' => '新闻']),
                    '删除' => "javascript:del('" . $c_v['id'] . "','新闻')",
                ];
            }else{
                $operate = [
                    '编辑' => url('banner/editBanner', ['id' => $c_v['id'], 'name' => '广告']),
                    '删除' => "javascript:del('" . $c_v['id'] . "','广告')",
                ];
            }
            $arr_list[$c_k]['operate'] = $operate;
        }
        $this->assign('list',$arr_list);
        return $this->fetch();
    }

    //banner新闻、广告编辑
    public function editBanner(){
        $advertise = new Advertisements();
        $news = new NewsModel();
        $column = new Column();
        $news_source = new NewsSource();
        $banner = new BannerModel();
        $param = input('param.');
        if($param['name']=='新闻'){//编辑新闻
            $new = new NewsModel();
            $column = new Column();
            $banner = new BannerModel();
            $news_source = new NewsSource();
            $nid = input('param.id');//新闻id
            $news_id = ['n.id'=>$nid];
            $news_list = $new->getNews($news_id);//获取新闻详情
            $flag_arr = [6,7,8,9];//赋初值，没其他意义，暂时没用到
            $child_zt_arr = [];
            $child_zt = [];
            foreach($news_list as $new_k=>$new_v){
                if($new_v['column_ids']==0){
                    $flag_arr[0] = 0;//column_ids=0，表示banner新闻
                }else{
                    $cid = explode(',',$new_v['column_ids']);//column_ids 数组，代表栏目id数组
                    foreach($cid as $cid_k=>$cid_v){
                        $column_id = ['id'=>$cid_v];
                        $c_name = $column->getSingleColumn($column_id);
                        switch($c_name['title']){
                            case '新闻':
                                $flag_arr[1] = 1;
                                break;
                            case '深度':
                                $flag_arr[2] = 2;
                                //专题
                                $zt_title = ['title'=>'深度'];
                                $zt_pid_arr = $column->getSingleColumn($zt_title);//获取深度id
                                $zt_id = $zt_pid_arr['id'];
                                $where_zt_id = ['parent_id'=>$zt_id];
                                $zt_arr = $column->getColumns($where_zt_id);//获取二级栏目
                                foreach($zt_arr as $zt_arr_k=>$zt_arr_v){
                                    $child_zt[$zt_arr_k] = $zt_arr_v['id'];
                                }
                                $ids_arr = explode(',',$news_list[0]['column_ids']);
                                $arr = array_intersect($ids_arr,$child_zt);//筛选出专题id
                                if(!empty($arr)){
                                    $child_id['id'] = ['in',$arr];//获取选中的专题
                                    $child_zt_arr = $column->getColumns($child_id);
                                }else{
                                    $child_zt_arr = [];
                                }
                                break;
                            case '热点':
                                $flag_arr[3] = 3;
                                break;
                        }
                    }
                }
            }
            //$banner_wh['out_id'] = $news_list[0]['id'];
            $banner_wh['out_id'] = $nid;
            $banner_arr = $banner->getSingleBanners($banner_wh,'picture_path,sort');//banner图片路径、排序
            $banner_pic = $banner_arr['picture_path'];
            $banner_sort = $banner_arr['sort'];
            $news_list[0]['banner_pic'] = "$banner_pic";
            $news_list[0]['banner_sort'] = "$banner_sort";//banner图片路径、排序处理为了赋给detail做页面渲染
            $column_wh['c.id'] = ['in',$news_list[0]['column_ids']];
            $column_arr = $column->getColumns($column_wh,'title', 'c.id asc');//栏目
            $columnArr = [];
            foreach($column_arr as $a_k=>$a_v){
                $columnArr[] = $a_v['title'];
            }
            $news_list[0]['column_ids'] = $columnArr;//栏目标题数组
            $new_arr = $news_list[0];

            $zt_title = ['title'=>'深度'];
            $zt_pid_arr = $column->getSingleColumn($zt_title);
            $zt_id = $zt_pid_arr['id'];
            $where_zt_id = ['parent_id'=>$zt_id];
            $zt_arr = $column->getColumns($where_zt_id);//获取所有二级栏目。渲染页面
            //新闻资源图片
            $source_id['news_id'] = $nid;
            if($news_list[0]['source_type']==4){//过滤视频缩略图
                $source_id['type'] = ['neq',1];
            }
            $source_pic = $news_source->getNewsSource($source_id,'source_path,type','id asc');//获取新闻图片资源、新闻类型
            $source_arr = [];
            foreach($source_pic as $sv){
                $source_arr[] = $sv['source_path'];
            }
            $new_arr['source_pic'] = $source_arr;
            //banner资源图片
            $banner_id = ['out_id'=>$nid];
            $banner_pic = $banner->getBanners($banner_id);//获取banner资源
            if(!empty($banner_pic)){
                foreach($banner_pic as $banner_k=>$banner_v){
                    $banner_pic_arr[0]['source_path'][$banner_k] = $banner_v['picture_path'];
                    $banner_pic_arr[0]['sort'][$banner_k] = $banner_v['sort'];
                }
                $this->assign('banner_img',$banner_pic_arr);//banner图片
            }
            //所有栏目
            $all_wh = ['c.is_show'=>1,'c.is_delete'=>0, 'c.parent_id'=>0];
            $all_column = $column->getColumns($all_wh,'title', 'c.id asc');
            //banner新闻推送次序
            $position_diff = $this->bannerSort();
            $this->assign('order',$position_diff);
            //is_banner、is_show、column_ids
            $this->assign('new_list',$news_list);
            $this->assign('detail',$new_arr);
            $this->assign('column',$all_column);
            $this->assign('flag',$flag_arr);
            $this->assign('zt',$zt_arr);//专题
            $this->assign('child_zt',$child_zt_arr);//选中的专题
            return $this->fetch('newsmanage/publish');
        }else{//编辑广告
            $id = input('param.id');
            $where_id = array('id'=>$id);
            $list = $advertise->getAdvertise($where_id);//广告列表信息
            $where_sid['ad_id'] = array('in',$list[0]['id']);
            $source = $advertise->getSource($where_sid);//广告资源信息
            foreach($list as $l_k=>$l_v){
                $list[$l_k]['crete_time'] = !empty($l_v['create_time'])?date('Y-m-d H:i:s',$l_v['create_time']):'';
                $list[$l_k]['update_time'] = !empty($l_v['update_time'])?date('Y-m-d H:i:s',$l_v['update_time']):'';
                $list[$l_k]['circle_time_start'] = !empty($l_v['circle_time_start'])?date('Y-m-d H:i:s',intval($l_v['circle_time_start'])):'';
                $list[$l_k]['circle_time_end'] = !empty($l_v['circle_time_end'])?date('Y-m-d H:i:s',intval($l_v['circle_time_end'])):'';
            }
            $this->assign('list',$list);
            foreach($source as $s_k=>$s_v){//广告图片资源处理，赋给detail渲染页面
                $source_arr[0]['pic_path'][$s_k] = $s_v['pic_path'];
            }
            $source_arr[0]['pic_num'] = count($source);
            $this->assign('source',$source_arr);
            $column = new Column();
            $column_list = $column->getColumns('','*');
            $this->assign('column_list',$column_list);
            $detail = $list[0];//
            $detail['pic_path'] = $source_arr[0]['pic_path'];
            $this->assign('detail',$detail);
            return $this->fetch('advermanage/advertisementpublish');
        }
    }

    //banner新闻的推送次序
    public function bannerSort(){
        $new = new NewsModel();
        $banner = new BannerModel();
        //需要显示的新闻banner次序 news banner banner_adv config
        //$banner_conf_arr = $banner->getBannersConf();//获取banner_num
        $banner_conf_arr = Db::table('scs_config')->find();;//获取banner_num  banner位置数量
        $where_posi['is_show'] = 1;
        $banner_position_arr = $banner->getBannersAdver($where_posi);//获取banner广告位置
        $position = [];
        foreach($banner_position_arr as $position_v){//处理banner新闻的推送次序
            $position[] = $position_v['position'];//广告位置数组
        }
        $all = [];
        for($i=0;$i<$banner_conf_arr['banner_num'];$i++){
            $all[] = $i+1;//banner位置数量 数组
        }
        $position_diff = array_diff($all,$position);//差集就是发布banner新闻的  新闻次序
        return $position_diff;
    }


    //删除banner
    public function bannerDel(Request $request){
        $code = 1;
        $msg = '删除成功';
        $err_msg = '编辑成功';
        $id = $request->param('id');
        $name = $request->param('name');
        $advertising = new Advertisements();
        $news = new NewsModel();
        $where = ['id'=>$id];
        if($name=='新闻'){
            $result = $news->delNews($where);
        }else{
            $result = $advertising->delAdvertise($where);
        }
        if(!$result){
            $code = 0;
            $msg = '删除失败';
        }
        return json(['code'=>$code, 'msg'=>$msg]);
    }


}