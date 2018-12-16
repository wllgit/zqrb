<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/6/9
 * Time: 18:16
 */
namespace app\admin\controller;

use app\admin\model\Start;

class Startpage extends Base{

    //启动页添加
    public function startpageAdd(){
        if(request()->isPost()){
            $code = 1;
            $msg = '添加成功';
            $startpage = new Start();
            $param = input('param.');
            $param = parseParams($param['data']);
            $param['create_time'] = time();
            $param['update_time'] = time();
            $userinfo = $startpage->saveStartpage($param);
            if($userinfo['code']==0){
                $code = 0;
                $msg = '添加失败';
            }
            return json(['code'=>$code, 'data'=>$userinfo['data'], 'msg'=>$msg]);
        }
        return $this->fetch();
    }


    //启动页列表
    public function startpageList(){
        $startpage = new Start();
        $list = $startpage->getStartpage();
        foreach($list as $k=>$v){
            $list[$k]['operate'] = url('startpage/editStartpage', ['id' => $v['id']]);
        }
        $this->assign('list',$list);
        return $this->fetch();
    }


    //编辑启动页
    public function editStartpage(){
        $code = 1;
        $msg = '添加成功';
        $startpage = new Start();
        if(request()->isPost()){
            $param = input('param.');
            $param = parseParams($param['data']);
            $where = array('id'=>$param['id']);
            $result = $startpage->updateStartpage($param,$where);
            if($result['code']==0){
                $code = 0;
                $msg = '编辑失败';
            }
            return json(['code'=>$code, 'data'=>$result['data'], 'msg'=>$msg]);
        }
        $id = input('param.id');
        $list = $startpage->getStartpage($id);
        $this->assign('list',$list);
        return $this->fetch();
    }
    //删除启动页
    public function del(){
        $code = 1;
        $msg = '成功';
        $startpage = new Start();
        $param = input('param.');
        if($param['state']==1){//编辑
            $where = array('id'=>$param['id']);
            $list = $startpage->getStartpage($where);
            $this->assign('list',$list);
            $this->fetch('/startpage/startpageadd');
        }else{
            $where = array('id'=>$param['id']);
            $list = $startpage->delStartpage($where);
            if($list['code']==0){
                $code = 0;
                $msg = '删除失败';
            }
            return json(['code'=>$code, 'data'=>$list['data'], 'msg'=>$msg]);
        }

    }



}