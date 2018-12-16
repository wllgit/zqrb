<?php
// +----------------------------------------------------------------------
// | snake
// +----------------------------------------------------------------------
// | Copyright (c) 2016~2022 http://baiyf.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: NickBai <1902822973@qq.com>
// +----------------------------------------------------------------------
namespace app\admin\controller;
use app\admin\model\Admin;
use app\admin\model\Role;
use app\admin\model\UserModel;
use app\admin\model\UserType;

class User extends Base
{
    //用户列表
    public function index()
    {
        if(request()->isAjax()){
            $param = input('param.');
            $limit = $param['pageSize'];
            $offset = ($param['pageNumber'] - 1) * $limit;
            $where = ['scs_admin.is_delete' => 0];
            if (isset($param['searchText']) && !empty($param['searchText'])) {
                $where['name'] = ['like', '%' . $param['searchText'] . '%'];
            }
            $user = new Admin();
            $selectResult = $user->getUsersByWhere($where, $offset, $limit);
            $status = config('user_status');
            foreach($selectResult as $key=>$vo){
//                $selectResult[$key]['last_login_time'] = date('Y-m-d H:i:s', $vo['last_login_time']);
//                $selectResult[$key]['status'] = $status[$vo['status']];
                $operate = [
                    '编辑' => url('user/userEdit', ['id' => $vo['id']]),
                    '删除' => "javascript:userDel('".$vo['id']."')"
                ];
                $selectResult[$key]['operate'] = showOperate($operate);

                if( 1 == $vo['id'] ){
                    $selectResult[$key]['operate'] = '';
                }
            }
            $return['total'] = $user->getAllUsers($where);  //总数据
            $return['rows'] = $selectResult;
            return json($return);
        }
        return $this->fetch('user/index');
    }

    //添加用户
    public function userAdd()
    {
        if(request()->isPost()){

            $param = input('param.');
            $param = parseParams($param['data']);
            // if($param['role_id'] == 0 || empty($param['role_id'] || $param['role_id'] == '0')) {
            //     return ['code' => -1, 'data' => '', 'msg' => '角色不能为空'];
            // }
            $param['password'] = md5($param['password']);
            $param['create_time'] = time();
            $param['update_time'] = time();
            $user = new Admin();
            $flag = $user->insertUser($param);

            return json(['code' => $flag['code'], 'data' => $flag['data'], 'msg' => $flag['msg']]);
        }

        $role = new Role();
        $this->assign([
            'role' => $role->getRole(),
//            'status' => config('user_status')
        ]);

        return $this->fetch();
    }

    //编辑角色
    public function userEdit()
    {
        $user = new Admin();

        if(request()->isPost()){

            $param = input('post.');
            $param = parseParams($param['data']);
            if(empty($param['password'])){
                unset($param['password']);
            }else{
                $param['password'] = md5($param['password']);
            }
            $flag = $user->editUser($param);

            return json(['code' => $flag['code'], 'data' => $flag['data'], 'msg' => $flag['msg']]);
        }

        $id = input('param.id');
        $role = new Role();
        $this->assign([
            'user' => $user->getOneUser($id),
//            'status' => config('user_status'),
            'role' => $role->getRole()
        ]);
        return $this->fetch();
    }

    //删除角色
    public function userDel()
    {
        $id = input('param.id');

        $role = new Admin();
        $flag = $role->delUser($id);
        return json(['code' => $flag['code'], 'data' => $flag['data'], 'msg' => $flag['msg']]);
    }
}
