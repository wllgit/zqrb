<?php
/**
 * Created by PhpStorm.
 * User: sogu
 * Date: 2018/7/13
 * Time: 下午6:00
 */

namespace app\admin\controller;

use app\admin\model\Comment;
use app\admin\model\Reply;
use think\Db;

class Wordblack extends Base{

    //文字黑名单列表
    public function blackList(){
        $list = Db::table('scs_word_blacklist')->where('is_delete',0)->order('create_time desc')->select();
        $this->assign('list',$list);
        return $this->fetch();
    }

    //存储文字黑名单
    public function saveWord(){
        $cont = input();
        if(!isset($cont['word']) || !$cont['word']){
            return json_encode(['isSuccess'=>0,'msg'=>'添加失败！']);
        }

        $data = [
            'content' => str_replace(' ','',trim($cont['word'])),
            'create_time' => time()
        ];

        $is_exist = Db::table('scs_word_blacklist')->where('content',$data['content'])->find();

        if($is_exist){
            return json_encode(['isSuccess'=>0,'msg'=>'已存在！']);
        }

        $res = Db::table('scs_word_blacklist')->insert($data);

        if($res){
            return json_encode(['isSuccess'=>1,'msg'=>'添加成功！']);
        }else{
            return json_encode(['isSuccess'=>0,'msg'=>'添加失败！']);
        }
    }
    //删除文字黑名单
    public function deleWord(){
        $cont = input();

        $res = Db::table('scs_word_blacklist')->where('id',$cont['id'])->update(['is_delete'=>1]);

        if($res){
            return json_encode(['isSuccess'=>1]);
        }else{
            return json_encode(['isSuccess'=>0]);
        }
    }
    //评论列表
    public function commentlist(){
        $com_model = New Comment();
        $rep_model = New Reply();

        $level = input('param.level');
        $con = input();

        if(isset($con['column_order'])){
            $level = intval(substr($con['column_order'],-1));//var_dump($level);exit;
        }

        if(isset($con['comment'])){
            $com = $con['comment'];
            if($level == 2){
                $where = "is_delete = 0 AND reply like '%$com%'";
            }else{
                $where = "is_delete = 0 AND comment like '%$com%'";
            }
        }else{
            $where = [
                'is_delete' => 0
            ];
        }

        $field = "user_id,comment,create_time,id,is_show,news_id";

        if($level == 2){
            $list = $rep_model->getReply($where,'user_id,news_id,reply,create_time,id,is_show','create_time desc');
        }else{
            $list = $com_model->getComment($where,$field,'create_time desc');//echo '<pre>';print_r($list);exit;
        }

        foreach ($list as $k=>$v){
            $list[$k]['create_time'] = date('Y-m-d H:i',$v['create_time']);

            if($level == 2){
                $list[$k]['comment'] = $v['reply'];
                $list[$k]['level'] = '二级';
            }else{
                $list[$k]['comment'] = $v['comment'];
                $list[$k]['level'] = '一级';
            }

        }

        $this->assign('list',$list);
        $column = [
            $level
        ];

        $this->assign('column',$column);
        return $this->fetch();
    }

    public function isShow(){
        $cont = input();
        if($cont['level'] == '一级'){
            $res = Db::table('scs_comment')->where('id',$cont['id'])->update(['is_show'=>$cont['type']]);
        }else{
            $res = Db::table('scs_comment_reply')->where('id',$cont['id'])->update(['is_show'=>$cont['type']]);
        }
        if($cont['type'] == 1){
            Db::table('scs_news')->where('id',$cont['news_id'])->setInc('comments_num');
        }else{
            Db::table('scs_news')->where('id',$cont['news_id'])->setDec('comments_num');
        }

        if($res){
            return json_encode(['isSuccess'=>1]);
        }else{
            return json_encode(['isSuccess'=>0]);
        }
    }

    /**
     * 来源黑名单列表
     */
    public function sourcelist(){
        $list = Db::table('scs_source_blacklist')->where('is_delete',0)->order('create_time desc')->select();
        $this->assign('list',$list);
        return $this->fetch();
    }

    //存储来源黑名单
    public function savesource(){
        $cont = input();
        if(!isset($cont['word']) || !$cont['word']){
            return json_encode(['isSuccess'=>0,'msg'=>'添加失败！']);
        }
        $data = [
            'content' => str_replace(' ','',trim($cont['word'])),
            'create_time' => time()
        ];

        $is_exist = Db::table('scs_source_blacklist')->where('content',$data['content'])->find();

        if($is_exist){
            return json_encode(['isSuccess'=>0,'msg'=>'已存在！']);
        }

        $res = Db::table('scs_source_blacklist')->insert($data);

        if($res){
            return json_encode(['isSuccess'=>1,'msg'=>'添加成功！']);
        }else{
            return json_encode(['isSuccess'=>0,'msg'=>'添加失败！']);
        }
    }

    //删除来源黑名单
    public function delesource(){
        $cont = input();

        $res = Db::table('scs_source_blacklist')->where('id',$cont['id'])->update(['is_delete'=>1]);

        if($res){
            return json_encode(['isSuccess'=>1]);
        }else{
            return json_encode(['isSuccess'=>0]);
        }
    }
}