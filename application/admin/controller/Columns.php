<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/5/28
 * Time: 11:43
 */

namespace app\admin\controller;
use app\admin\model\Advertisements;
use \think\Db;
use \think\Request;
use app\admin\model\Column;
use app\admin\validate\ColumnsValidate;

class Columns extends Base{
    public function doColumn(){
        $column = new Column();
        $advertising = new Advertisements();
        if(request()->isPost()) {
            $code = 1;
            $msg = '保存成功';
            $param = input('param.');
            //栏目字段
            $param_master['title'] = $param['column_name'];
            $param_master['day_num'] = trim($param['column_date']);
            $param_master['news_num'] = trim($param['column_num']);
            $param_master['parent_id'] = 0;
            $param_master['column_url'] = trim($param['column_url']);
            $param_master['admin_id'] = session('id');
            $param_master['create_time'] = time();
            $param_master['update_time'] = time();
            $param_master['is_fixed'] = $param['column_fixed'];
            $param_master['is_show'] = 1;
            $result = $this->validate($param_master,'ColumnsValidate.first');
            if(true !== $result) {//字段验证
                return json(['code' => -1, 'data' => '', 'msg' => $result]);
            }
            if(isset($param['top_id'])&&$param['top_id']!=0){//是否选中二级栏目
                $param_master['parent_id'] = $param['top_id'];
                
            }
            if($param['has_child'] == 0 || $param['has_child'] == 0) { //has_child  栏目级别  0二级，1一级
                $result = $this->validate($param_master,'ColumnsValidate.second');
                if(true !== $result) {
                    return json(['code' => -1, 'data' => '', 'msg' => $result]);
                }
            }
            // 启动事务
            Db::startTrans();
            try{
                $pic_arr = $this->upload();//上传栏目图片
                if(is_array($pic_arr)){//如果返回值为数组，报错
                    return json(['code' => $pic_arr['code'], 'msg' => $pic_arr['msg']]);
                }
                $param_master['pic_path'] = config('zqrb')['domain'].$pic_arr;
                $result = $column->saveColumn($param_master);//保存主栏目master
                $parent_id = $result['userId'];//新增数据id
                //保存栏目和广告关联表scs_column_adver
                if(isset($param['ad_sort'])){//设置栏目的广告次序：
                    $param_adver['column_id'] = $parent_id;
                    foreach($param['ad_sort'] as $ad_k=>$ad_v){
                        $banner_radio = 'banner_radio'.($ad_k+1);//拼接第几条随机广告、固定广告
                        $ad_position = 'ad_position'.($ad_k+1);//拼接第几条随机广告、固定广告  选中的广告位置
                        $param_adver['position'] = $ad_v;
                        if($param["$banner_radio"]!=0){//如果不为0，表示选中固定广告
                            $param_adver['adver_id'] = $param["$ad_position"];
                        }else{//如果为0，表示选中随机广告  广告id默认0
                            $param_adver['adver_id'] = 0;
                        }
                        $column->saveColumnAd($param_adver);
                    }
                }
                // 提交事务
                Db::commit();
            } catch (\Exception $e) {
                //回滚事务
                Db::rollback();
                $code = 0;
                $msg = $e->getMessage();
            }
            return json(['code' => $code, 'msg' => $msg]);
        }
        //顶级栏目
        $where_top = ['parent_id'=>0,'c.is_show'=>1,'c.is_delete'=>0];
        $fields = 'c.id,title';
        $order = 'c.id asc';
        $top_arr = $column->getColumns($where_top,$fields,$order,0,100);
        $this->assign('top_arr',$top_arr);
        //广告列表
        $where_title['is_show'] = ['=',1];
        $where_title['is_delete'] = ['=',0];
        $where_title = 'circle_time_start < unix_timestamp() AND unix_timestamp() < circle_time_end';
        $ad_list = $advertising->getAdvertise($where_title);
        foreach($ad_list as $ad_k=>$ad_v){
            $ad_list[$ad_k]['title'] = empty($ad_v['title'])?$ad_v['out_url']:$ad_v['title'];
        }
        $this->assign('a_list',$ad_list);
        return $this->fetch();
    }

    //二级栏目  暂时未用到
    public function secondColumn(){
        $column = new Column();
        $param = $_GET['first_name'];
        $where_title['is_show'] = ['=',1];
        $where_title['is_delete'] = ['=',0];
        $where_title['title'] = ['=',$param];
        //栏目列表
        $column_list = $column->getSingleColumn($where_title);
        $child_arr['is_show'] = ['=',1];
        $child_arr['is_delete'] = ['=',0];
        $child_arr['parent_id'] = ['=',$column_list['id']];
        $child_list = $column->getColumns($child_arr);
        return json($child_list);
    }

    //栏目列表
    public function columnList(){
        $column = new Column();
        $where = ['c.is_delete'=>0];
        if(!empty(input('title'))) {
            $title = input('title');
            $where['title'] = ['like','%'.$title.'%'];
        }
//        $column_list = $column->getColumns(['c.is_show'=>1,'c.is_delete'=>0],'c.*,a.name','c.id asc');
        $column_list = $column->getColumns($where,'c.*,a.name','c.id asc');
        if(!empty($column_list)){
            foreach($column_list as $l_k=>$l_v){
                $column_list[$l_k]['create_time'] = isset($l_v['create_time'])?date('Y-m-d H:i:s',$l_v['create_time']):'';
                $column_list[$l_k]['column_top'] = $l_v['parent_id']==0?'一级':'二级';
                $operate = [
                    '编辑' => url('columns/editColumn', ['id' => $l_v['id']]),
                    '删除' => "javascript:delcolumn('".$l_v['id']."')",
                    //'隐藏' => "javascript:hideColumn('".$l_v['id']."')",
                ];
                $column_list[$l_k]['operate'] = $operate;
                if($l_v['is_show']==0){
                    $column_list[$l_k]['show'] =  "显示";
                    $column_list[$l_k]['url'] =  "javascript:showColumn('".$l_v['id']."')";
                }else{
                    $column_list[$l_k]['show'] =  "隐藏";
                    $column_list[$l_k]['url'] =  "javascript:hideColumn('".$l_v['id']."')";
                }

            }

        }
        $this->assign('list',$column_list);
        return $this->fetch();
    }



    //编辑栏目
    public function editColumn(){
        $column = new Column();
        $advertising = new Advertisements();
        if(request()->isPost()) {
            $code = 1;
            $msg = '编辑成功';
            $param = input('param.');
            $data['day_num'] = $param['news_day'];//新闻显示天数
            $data['news_num'] = $param['news_num'];//新闻显示数量
            $data['is_fixed'] = $param['column_fixed'];//栏目固定
            $data['column_url'] = isset($param['column_url'])?$param['column_url']:'';//二级栏目url
            if(isset($param['second_column'])){//二级栏目id
                $where_id = ['id'=>$param['second_column']];
            }else{
                $where_id = ['title'=>trim($param['news_name'])];
            }
            // 启动事务
            Db::startTrans();
            try{
                $pic_arr = $this->upload();
                if(is_array($pic_arr)){//如果返回值为数组，报错
                    return json(['code' => $pic_arr['code'], 'msg' => $pic_arr['msg']]);
                }
                $data['pic_path'] = config('zqrb')['domain'].$pic_arr;
                $column->updateColumn($where_id,$data);
                switch($param['news_name']){
                    case '新闻':
                        break;
                    case '热点': //如果编辑热点栏目  会显示权重相关
                        $weight['click_weight'] = $param['hot_read'];//阅读量（点击量）
                        $weight['praise_weight'] = $param['hot_praise'];//点赞量
                        $weight['transmit_weight'] = $param['hot_transmit'];//转发量
                        $weight['colletion_weight'] = $param['hot_collection'];//收藏量
                        $weight['update_time'] = time();

                        $weight_id = ['is_delete'=>0];
                        $column->updateWeight($weight_id,$weight);

                        break;
                }
                //保存栏目和广告关联表scs_column_adver
                //删除原记录，保存新记录
                $where_del = ['column_id'=>$param['cid']];
                $column->delColumnAd($where_del);
                if(isset($param['ad_sort'])){ //设置栏目第几条前插入广告
                    $column_ad = ['column_id'=>$param['cid']];
                    $column_ad_arr = $column->getColumnAd($column_ad);//获取栏目和广告关联表信息
                    $param_adver['column_id'] = $param['cid'];//栏目id
                    foreach($param['ad_sort'] as $ad_k=>$ad_v){
                        $banner_radio = 'banner_radio'.($ad_k+1);//拼接第几条随机广告、固定广告
                        $ad_position = 'ad_position'.($ad_k+1);//拼接第几条随机广告、固定广告  选中的广告位置
                        $param_adver['position'] = $ad_v;
                        if($param["$banner_radio"]!=0){ //如果不为0，表示选中固定广告
                            $param_adver['adver_id'] = $param["$ad_position"];
                        }else{//如果为0，表示选中随机广告
                            $param_adver['adver_id'] = 0;
                        }
                        if(count($column_ad_arr)>0){//存在，则更新
                            $column->saveColumnAd($param_adver);
                        }else{//不存在，插入
                            $column->saveColumnAd($param_adver);
                        }
                    }
                }
                // 提交事务
                Db::commit();
            }
            catch (\PDOException $e){
                //回滚事务
                Db::rollback();
                $code = 0;
                $msg = '编辑失败';
                $msg = $e->getMessage();
            }
            return json(['code' => $code, 'msg' => $msg]);
        }else{//渲染页面
            $id = input('param.id');
            $column_id = ['id'=>$id];
            $column_list = $column->getSingleColumn($column_id,'*');//获取栏目数据
            if($column_list['title'] == '热点'){//当编辑热点栏目是  才显示权重信息设置
                $weight_where = ['is_delete'=>0];
                $weight_list = $column->getWeight($weight_where);
                if(!empty($weight_list)&&count($weight_list)>0){
                    $weight_list['click'] = $weight_list['click_weight'];
                    $weight_list['praise'] = $weight_list['praise_weight'];
                    $weight_list['transmit'] = $weight_list['transmit_weight'];
                    $weight_list['colletion'] = $weight_list['colletion_weight'];
                    $this->assign('weight',$weight_list);
                }
            }
            //广告列表
            $where_ad['is_show'] = ['=',1];
            $where_ad['is_delete'] = ['=',0];
            $where_ad = 'circle_time_start < unix_timestamp() AND unix_timestamp() < circle_time_end';
            $ad_list = $advertising->getAdvertise($where_ad);
            foreach($ad_list as $ad_k=>$ad_v){
                $ad_list[$ad_k]['title'] = empty($ad_v['title'])?$ad_v['out_url']:$ad_v['title'];//如果为没有标题广告，以链接代替（当选择广告时）
            }
            //栏目列表
            $where_column['parent_id'] = 0;
            $where_column['is_show'] = 1;
            $where_column['is_delete'] = 0;
            $where_column['id'] = $id;
            $column_arr = $column->getSingleColumn($where_column);
            if(empty($column_arr)){
                $where_column['parent_id'] = ['neq',0];
                $column_arr = $column->getSingleColumn($where_column);
            }
            //二级栏目
            if($column_list['title'] == '深度'){
                $where_second['parent_id'] = ['neq',0];
                $where_second['c.is_show'] = 1;
                $where_second['c.is_delete'] = 0;
                $column_second = $column->getColumns($where_second);
                $this->assign('second_list',$column_second);
            }
            //栏目和广告关联表 相关数据
            $where_column_ad= ['column_id'=>$id];
            $column_ad_arr = [];
            $column_ad_arr = $column->getColumnAd($where_column_ad);
            $column_ad_num = count($column_ad_arr);
            $this->assign('list',$column_list);
            $this->assign('c_list',$column_arr);
            $this->assign('a_list',$ad_list);
            $this->assign('column_ad',$column_ad_arr);
            $this->assign('num',$column_ad_num);
            return $this->fetch();
        }

    }



    //编辑栏目  未用到
    public function editColumn1(){
        $column = new Column();
        if(request()->isPost()) {
            $code = 1;
            $msg = '保存成功';
            $param = input('param.');
            $param = parseParams($param['data']);
            $where_id = ['id'=>$param['id']];
            $data['title'] = $param['news_name'];
            $data['day_num'] = $param['news_date'];//新闻显示天数
            $data['news_num'] = $param['news_num'];//新闻显示数量
            // 启动事务
            Db::startTrans();
            try{
                $column->updateColumn($where_id,$data);
                switch($param['news_name']){
                    case '新闻':
                        break;
                    case '热点':
                        $weight['click'] = $param['hot_read']/100;//阅读量（点击量）
                        $weight['praise'] = $param['hot_praise']/100;//点赞量
                        $weight['transmit'] = $param['hot_transmit']/100;//转发量
                        $weight['colletion'] = $param['hot_collection']/100;//收藏量
                        $weight['create_time'] = time();
                        $weight['update_time'] = time();
                        $weight_id = ['id'=>$param['wid']];
                        $column->updateWeight($weight_id,$weight);
                        break;
                }
                // 提交事务
                Db::commit();
            }
            catch (\PDOException $e){
                //回滚事务
                Db::rollback();
                $code = 0;
                $msg = '保存失败';
                $msg = $e->getMessage();
            }
            return json(['code' => $code, 'msg' => $msg]);
        }
        $id = input('param.id');
        $column_id = ['id'=>$id];
        $column_list = $column->getColumns($column_id,'*');
        if($column_list[0]['title'] == '热点'){
            $weight_where = ['is_delete'=>0];
            $weight_list = $column->getWeight($weight_where);
            foreach($weight_list as $w_k=>$w_v){
                $weight_list[$w_k]['click'] = $w_v['click']*100;
                $weight_list[$w_k]['praise'] = $w_v['praise']*100;
                $weight_list[$w_k]['transmit'] = $w_v['transmit']*100;
                $weight_list[$w_k]['colletion'] = $w_v['colletion']*100;
            }
            $this->assign('weight',$weight_list);
        }
        $this->assign('list',$column_list);
        return $this->fetch();
    }


    //删除栏目
    public function delColumn(){
        $column = new Column();
        if(request()->isPost()) {
            $code = 1;
            $msg = '删除成功';
            $id = input('param.id');
            $where_del = ['id'=>$id];
            $result = $column->delColumn($where_del);
            if(!$result){
                $code = 0;
                $msg = '删除失败';
            }
            return json(['code' => $code, 'msg' => $msg]);
        }
        return $this->fetch();
    }


    //隐藏栏目
    public function hideColumn(){
        $column = new Column();
        if(request()->isPost()) {
            $code = 1;
            $msg = '隐藏成功';
            $id = input('param.id');
            $where = ['id'=>$id];
            $data = ['is_show'=>0];
            $result = $column->updateColumn($where,$data);
            if(!$result){
                $code = 0;
                $msg = '隐藏失败';
            }
            return json(['code' => $code, 'msg' => $msg]);
        }
        return $this->fetch();
    }

    //显示栏目
    public function showColumn(){
        $column = new Column();
        if(request()->isPost()) {
            $code = 1;
            $msg = '显示成功';
            $id = input('param.id');
            $where = ['id'=>$id];
            $data = ['is_show'=>1];
            $result = $column->updateColumn($where,$data);
            if(!$result){
                $code = 0;
                $msg = '显示失败';
            }
            return json(['code' => $code, 'msg' => $msg]);
        }
        return $this->fetch();
    }

    //单文件上传
    function upload(){
        // 获取表单上传文件
        $file = request()->file('pic_path');
        if($file){//1024000
            $info = $file->validate(['size'=>1024000,'ext'=>'jpg,png,gif'])->rule('uniqid')->move(ROOT_PATH . 'public' . DS . 'upload',true,false);//如果上传文件验证不通过，则move方法返回false。
            if($info){
                $path = '/upload/'.$info->getFilename();// echo ROOT_PATH . 'public' . DS . 'upload/'.$path;
                return $path;
            }else{
                $code = 0;
                // 上传失败获取错误信息
                $msg = $file->getError();
                return ['code' => $code, 'msg' => $msg];
            }
        }
    }

}