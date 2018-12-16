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

use think\Db;
use app\admin\model\UserDepartment;
use app\admin\model\Petpermissionrole;
use app\admin\model\UserfontModel;
use think\Exception;
use think\Request;
use app\api\model\Leave;
class Userfont extends Base
{
    //用户列表
    public function index()
    {
        if(request()->isAjax()){
            $param = input('');
            $limit = $param['pageSize'];
            $offset = ($param['pageNumber'] - 1) * $limit;

            $where['suf.status']=['<>',3];
            if (isset($param['searchText']) && !empty($param['searchText'])) {
                $where['suf.name'] = ['like', '%' . $param['searchText'] . '%'];
            }
            $user = new UserfontModel();
            $selectResult = $user->getUsersByWhere($where, $offset, $limit);
            $status = config('user_status.status');
            $attitude = config('user_status.attitude');
            $is_admin = config('user_status.is_admin');
            foreach($selectResult as $key=>$vo){
                $selectResult[$key]['status'] = $status[$vo['status']];
                $selectResult[$key]['attitude'] = $attitude[$vo['attitude']];
                $selectResult[$key]['is_admin'] = $is_admin[$vo['is_admin']];
                $operate = [
                    '编辑' => url('Userfont/userEdit', ['id' => $vo['id']]),
                    '删除' => "javascript:userDel('".$vo['id']."')"
                ];
                $selectResult[$key]['operate'] = showOperate($operate);
                $selectResult[$key]['leader'] = Db::table('sg_user_front')->where('id',$vo['pid'])->value('name');
            }
            $return['total'] = $user->getAllUsers($where);  //总数据
            $return['rows'] = $selectResult;
            return json($return);
        }
        return $this->fetch();
    }
    //节点列表
    public function node()
    {
        if(request()->isAjax()){
            $param = input('');
            $limit = $param['pageSize'];
            $offset = ($param['pageNumber'] - 1) * $limit;
            $where=[];
            if (isset($param['searchText']) && !empty($param['searchText'])) {
                $where['node_name'] = ['like', '%' . $param['searchText'] . '%'];
            }
            $Result=DB::name('permission_node')->limit($offset, $limit)->where($where)->select();
            foreach($Result as $key=>$vo){
                if($vo['is_menu']==1){
                    $Result[$key]['is_menu']='不是';
                }else{
                    $Result[$key]['is_menu']='是';
                }
                $operate = [
                    '编辑' => url('Userfont/nodeEdit', ['id' => $vo['id']]),
                    '删除' => "javascript:nodeDel('".$vo['id']."')"
                ];
                $Result[$key]['operate'] = showOperate($operate);
            }
            $return['total'] = DB::name('permission_node')->count();  //总数据
            $return['rows'] = $Result;
            return json($return);
        }
        return $this->fetch();
    }
    //编辑角色
    public function nodeEdit()
    {
        if(request()->isPost()){
            $param = input('post.');
            $param = parseParams($param['data']);
            $result = DB::name('permission_node')->where('id',$param['id'])->update($param);
            if(false === $result){
                return json(['code' => 0, 'data' => '', 'msg' => $this->getError()]);
            }else{
                return json(['code' => 1, 'data' => '', 'msg' => '编辑节点成功']);
            }
        }
        $id = input('param.id');
        $info=DB::name('permission_node')->where('id',$id)->find();
        $this->assign([
            'info' => $info
        ]);
        return $this->fetch();
    }
    //添加节点
    public function nodeadd()
    {
        if(request()->isPost()){
            $param = input('post.');
            $param = parseParams($param['data']);
            $result = DB::name('permission_node')->insert($param);
            if(false === $result){
                return json(['code' => 0, 'data' => '', 'msg' => $this->getError()]);
            }else{
                return json(['code' => 1, 'data' => '', 'msg' => '添加节点成功']);
            }
        }
        $id = input('param.id');
        $info=DB::name('permission_node')->where('id',$id)->find();
        $this->assign([
            'info' => $info
        ]);
        return $this->fetch();
    }
    //删除节点
    public function nodeDel()
    {
        $id = input('param.id');
        $result = DB::name('permission_node')->where('id',$id)->delete();
        if(false === $result){
            return json(['code' => 0, 'data' => '', 'msg' => $this->getError()]);
        }else{
            return json(['code' => 1, 'data' => '', 'msg' => '删除节点成功']);
        }
    }

    //添加用户
    public function userAdd()
    {
        if(request()->isPost()){
            $param = input('param.');
            $param = parseParams($param['data']);
            $leader_id = $param['leader'];
            Db::startTrans();
            try {
                if ($leader_id == 0) {//没有上级领导
                    $leader = Db::table('sg_user_front')->order('rgt','desc')->limit(1)->find();
                    if ($leader['lft'] < 1) json(['code'=>0,'msg'=>'上级领导的数据错误']);
                    $param['pid'] = 0;
                    $param['lft'] = $leader['rgt']+1;
                    $param['rgt'] = $leader['rgt']+2;
                } else {
                    $leader = Db::table("sg_user_front")->where('id', $leader_id)->field('id,lft,rgt')->find();
                    if ($leader['lft'] < 1) json(['code'=>0,'msg'=>'上级领导的数据错误']);
                    Db::table("sg_user_front")->where("lft>={$leader['rgt']}")->setInc("lft", 2);
                    Db::table("sg_user_front")->where("rgt>={$leader['rgt']}")->setInc("rgt", 2);
                    $param['pid'] = $leader['id'];
                    $param['lft'] = $leader['rgt'];
                    $param['rgt'] = $leader['rgt']+1;
                }
                unset($param['leader']);
                $user = new UserfontModel();
                $flag = $user->insertUser($param);
                Db::commit();
                return json(['code' => $flag['code'], 'data' => $flag['data'], 'msg' => $flag['msg']]);
            } catch (Exception $e) {
                Db::rollback();
                return json(['code'=>0,'msg'=>'添加错误']);
            }
        }
        $role = new UserDepartment();
        $this->assign([
            'role' => $role->getRole(),
            'status' => config('user_status.status'),
            'attitude' => config('user_status.attitude'),
            'is_admin' => config('user_status.is_admin'),
            'users'=> Db::table('sg_user_front')->field('id,name')->select()
        ]);
        return $this->fetch();
    }

    //编辑员工对推送的态度
    function changeAttitude(){
        $param = Request::instance()->param();
        $id = $param['id'];
        $attitude=$param['attitude'];
        $attitude=$attitude==1?0:1;
        $user = new UserfontModel();
        $flag = $user->changeAt($id,$attitude);
        return json(['code' => $flag['code'], 'data' => $flag['data'], 'msg' => $flag['msg']]);
    }

    //编辑角色
    public function userEdit()
    {
        $user = new UserfontModel();
        if(request()->isPost()){
            $param = input('post.');
            $param = parseParams($param['data']);
            $is_role=DB::name('permission_user_role')->where('user_id',$param['id'])->value('id');
            if($is_role){
                DB::name('permission_user_role')->where('user_id',$param['id'])->update(['role_id'=>$param['role_id']]);
            }else{
                DB::name('permission_user_role')->insert(['role_id'=>$param['role_id'],'user_id'=>$param['id']]);
            }
            unset($param['role_id']);
            $flag = $user->editUser($param);
            return json(['code' => $flag['code'], 'data' => $flag['data'], 'msg' => $flag['msg']]);
        }
        $id = input('param.id');
        $role = new UserDepartment();
        $roleinfo=DB::name('permission_role')->select();
        $this->assign([
            'user' => $user->getOneUser($id),
            'status' => config('user_status.status'),
            'attitude' => config('user_status.attitude'),
            'is_admin' => config('user_status.is_admin'),
            'role' => $role->getRole(),
            'roleinfo' => $roleinfo
        ]);
        return $this->fetch();
    }

    //删除角色
    public function userDel()
    {
        $id = input('param.id');
        $role = new UserfontModel();
        $flag = $role->delUser($id);
        return json(['code' => $flag['code'], 'data' => $flag['data'], 'msg' => $flag['msg']]);
    }

    /**
     * 排班表
     */
    public function showClass(Request $request){
        //获取参数
        $userName = $request->get('userName', '');
        $class_id = $request->get('class_id', 0);
        $pageParam = ['query' => []];
        $where = array();
        //按人名模糊查询
        if ($userName) {
            $where['f.name'] = ['like', "%{$userName}%"];
            $pageParam['query']['companyName'] = $userName;
        }
        if ($class_id) {
            $where['c.id'] =$class_id;
            $pageParam['query']['status'] = $class_id;
        }
        $this->assign('class_id',$class_id);
        $class=Db::table('sg_user_class')->field('name,id')->select();
        $list =Db::table('sg_user_class')->alias('c')
            ->field('f.name,c.id as class_id,c.name as className,f.id as uid,c.start_time,c.end_time,c.restStartTime,c.restEndTime')
            ->join('sg_user_info i','i.class_id=c.id')
            ->join('sg_user_front f','f.id=i.user_id')
            ->where($where)
            ->order('i.id','asc')
            ->paginate(10,false, $pageParam);
        // 获取分页显示
        $page = $list->render();
        return $this->fetch('', [
            'class' => $class,
            'list'  => $list,
            'page'  => $page
        ]);
    }

    //添加部门
    public function addClass(Request $request)
    {
        if($request->isPost()){
            $data=$request->post();
            Db::table('sg_user_info')->insert($data);
            return json(['code' => 1, 'data' => 'dd', 'msg' => '添加成功']);
        }
        $userInfo=Db::table('sg_user_front')->where('status',1)->column('name','id');
        $allUid=array_keys($userInfo);
        $readyUid=Db::table('sg_user_info')->column('user_id');
        $class=Db::table('sg_user_class')->column('name','id');
        $diff=array_diff($allUid,$readyUid);
        $else=[];
        foreach ($diff as $v){
            $else[$v]=$userInfo[$v];
        }
        $this->assign('class',$class);
        $this->assign('user',$else);
        return $this->fetch();
    }

    //编辑个人班次
    public function editOneClass(Request $request)
    {
        if($request->isPost()){
            $data=$request->only(['class_id','user_id'],'post');
            Db::table('sg_user_info')->where('user_id',$data['user_id'])->update(['class_id'=>$data['class_id']]);
            return json(['code' => 1, 'data' => '', 'msg' => '更新成功']);
        }else{
            return json(['code' => 0, 'data' => '', 'msg' => '更新失败']);
        }
    }

    //假期列表
    public function userVacationList(Request $request)
    {
        if ($request->isAjax()) {
            $param = $request->get('');
            $limit = $param['pageSize'];
            $offset = ($param['pageNumber'] - 1) * $limit;
            //获取参数
            $where =  [];
            if (isset($param['searchText']) && !empty($param['searchText'])) {
                $where['f.name'] = ['like', '%' . $param['searchText'] . '%'];
            }
            $result = Db::table('sg_user_front')->alias('f')
                ->field('SQL_CALC_FOUND_ROWS u.id,u.user_id,u.leave_type,u.hours,u.add_time,
                u.update_time,f.name,t.name as leave_name,t.status')
                ->join('pe_leave_user u', 'u.user_id=f.id')
                ->join('pe_leave_type t', 't.id=u.leave_type')
                ->where($where)
                ->limit($offset,$limit)
                ->order('f.id','asc')
                ->select();
            $res = Db::query('select found_rows() as total_count');
            foreach ($result as $k => $v) {
                $operate = [
                    '编辑' => url('Userfont/vacationEdit', ['id' => $v['user_id']]),
//                    '清空' => "javascript:userVacationClear('".$v['id']."')"
                ];
                $result[$k]['operate'] = showOperate($operate);;
            }
            $return['total'] = $res[0]['total_count'];
            $return['rows'] = $result;
            return json($return);
        }
        return $this->fetch();
    }

    //修改个人假期时间
    public function vacationEdit(Request $request){
        if($request->isPost()){
            $param = $request->post('');
            $result = parseParams($param['data']);
            $date=date('Y-m-d H:i:s',time());
            if($result){
                $data = $result['type'];
                foreach($data as $k=>$v){
                    Db::table('pe_leave_user')->where(['user_id'=>$result['user_id'],'leave_type'=>$k])->update(['hours'=>$v,'update_time'=>$date]);
                }
                return json(['code' => 1, 'data' => '', 'msg' => '编辑成功']);
            }
        }
        $id = $request->param('id');
        $userName=Db::table('sg_user_front')->where('id',$id)->value('name');
        $data=Db::table('pe_leave_user')->alias('u')
            ->field('u.id,u.user_id,u.leave_type,u.hours,t.name as leave_name,t.status')
            ->join('pe_leave_type t','t.id=u.leave_type')
            ->where('u.user_id',$id)
            ->select();
        $this->assign('list',$data);
        $this->assign('user',$id);
        $this->assign('userName',$userName);
        return $this->fetch();
    }

    //分配员工假期时间
    public function addVacationTime(Request $request){
        if($request->isPost()){
            $result = $request->post();
            $date=date('Y-m-d H:i:s',time());
            if($result){
                $data = $result['type'];
                foreach($data as $k=>$v){
                    $insert[]=[
                        'user_id'=>(int)$result['user_id'],
                        'leave_type'=>$k,
                        'hours'=>$v=='0'?'0':ltrim($v,0),
                        'add_time'=>$date
                    ];
                }
                Db::table('pe_leave_user')->insertAll($insert);
                return json(['code' => 1, 'data' => '', 'msg' => '添加成功']);
            }
        }
        $userInfo=Db::table('sg_user_front')->where('status',1)->column('name','id');
        $allUid=array_keys($userInfo);
        $readyUid=Db::table('pe_leave_user')->group('user_id')->column('user_id');
        $type=Db::table('pe_leave_type')->select();
        $diff=array_diff($allUid,$readyUid);
        $else=[];
        foreach ($diff as $v){
            $else[$v]=$userInfo[$v];
        }
        $this->assign('type',$type);
        $this->assign('user',$else);
        return $this->fetch();
    }

    //添加假期类型
    public function addLeaveType(Request $request){
        if($request->isPost()){
            $data=$request->post();
            $result = parseParams($data['data']);
            if(Db::table('pe_leave_type')->insert($result)){
                return json(['code' => 1, 'data' => 'dd', 'msg' => '添加成功']);
            }
            return json(['code' => 0, 'data' => 'dd', 'msg' => '添加失败']);
        }
        return $this->fetch();
    }

    //班次列表
    public function classList(Request $request)
    {
        if ($request->isAjax()) {
            $param = $request->get('');
            $limit = $param['pageSize'];
            $offset = ($param['pageNumber'] - 1) * $limit;
            $select =Db::table('sg_user_class')->limit($offset,$limit)->order('id','asc')->select();
            foreach ($select as $key => $vo) {
                $operate = [
                    '编辑' => url('Userfont/classEdit', ['id' => $vo['id']]),
//                    '删除' => "javascript:classDel( '" .$vo['id']."' )"
                ];
                $select[$key]['operate'] = showOperate($operate);
            }
            $return['total'] = Db::table('sg_user_class')->count();
            $return['rows'] = $select;
            return json($return);
        }
        return $this->fetch();
    }
    //添加班次
    public function classAdd(Request $request)
    {
        if($request->isPost()){
            $data=$request->post();
            $cs=strtotime($data['start_time']);
            $ce=strtotime($data['end_time']);
            if($ce<$cs){
                return json(['code' => 0, 'data' => '', 'msg' => '下班时间不能小于上班时间']);
            }
            if($data['restStartTime'] && $data['restEndTime']){
                $rs=strtotime($data['restStartTime']);
                $re=strtotime($data['restEndTime']);
                if(! ($cs< $rs && $rs<$re && $re<$ce )){
                    return json(['code' => 0, 'data' => '', 'msg' => '请检查时间是否冲突']);
                }
            }
            Db::table('sg_user_class')->insert($data);
            return json(['code' => 1, 'data' => 'dd', 'msg' => '添加成功']);
        }
        return $this->fetch();
    }

    //编辑班次
    public function classEdit(Request $request)
    {
        if($request->isPost()){
            $data=$request->post();
            $cs=strtotime($data['start_time']);
            $ce=strtotime($data['end_time']);
            if($ce<$cs){
                return json(['code' => 0, 'data' => '', 'msg' => '下班时间不能小于上班时间']);
            }
            if($data['restStartTime'] && $data['restEndTime']){
                $rs=strtotime($data['restStartTime']);
                $re=strtotime($data['restEndTime']);
                if(! ($cs< $rs && $rs<$re && $re<$ce )){
                    return json(['code' => 0, 'data' => '', 'msg' => '请检查时间是否冲突']);
                }
            }
            Db::table('sg_user_class')->where('id',$data['id'])->update($data);
            return json(['code' => 1, 'data' => 'dd', 'msg' => '修改成功']);
        }
        $id=$request->param('id');
        $class=Db::table('sg_user_class')->where('id',$id)->find();
        $this->assign('class',$class);
        return $this->fetch();
    }
    //角色列表
    public function role()
    {
        if(request()->isAjax()){

            $param = input('param.');

            $limit = $param['pageSize'];
            $offset = ($param['pageNumber'] - 1) * $limit;

            $where = [];
            if (isset($param['searchText']) && !empty($param['searchText'])) {
                $where['rolename'] = ['like', '%' . $param['searchText'] . '%'];
            }

            $selectResult=DB::name('permission_role')->where($where)->limit($offset, $limit)->select();
            foreach($selectResult as $key=>$vo){
                $operate = [
                    '编辑' => url('Userfont/roleEdit', ['id' => $vo['id']]),
                    '删除' => "javascript:roleDel('".$vo['id']."')",
                    '分配权限' => "javascript:giveQx('".$vo['id']."')"
                ];
                $selectResult[$key]['operate'] = showOperate($operate);

            }

            $return['total'] = DB::name('permission_role')->where($where)->count();  //总数据
            $return['rows'] = $selectResult;

            return json($return);
        }

        return $this->fetch();
    }

    //添加角色
    public function roleAdd()
    {
        if(request()->isPost()){
            $param = input('post.');
            $param = parseParams($param['data']);
            $result = DB::name('permission_role')->insert($param);
            if(false === $result){
                return json(['code' => 0, 'data' => '', 'msg' => $this->getError()]);
            }else{
                return json(['code' => 1, 'data' => '', 'msg' => '添加角色成功']);
            }
        }

        return $this->fetch();
    }

    //编辑角色
    public function roleEdit()
    {
        if(request()->isPost()){

            $param = input('post.');
            $param = parseParams($param['data']);

            $result = DB::name('permission_role')->where('id',$param['id'])->update($param);
            if(false === $result){
                return json(['code' => 0, 'data' => '', 'msg' => $this->getError()]);
            }else{
                return json(['code' => 1, 'data' => '', 'msg' => '编辑角色成功']);
            }
        }

        $id = input('param.id');
        $this->assign([
            'role' => DB::name('permission_role')->where('id',$id)->find()
        ]);
        return $this->fetch();
    }

    //删除角色
    public function roleDel()
    {
        $id = input('param.id');
        $result = DB::name('permission_role')->where('id',$id)->delete();
        if(false === $result){
            return json(['code' => 0, 'data' => '', 'msg' => $this->getError()]);
        }else{
            return json(['code' => 1, 'data' => '', 'msg' => '删除角色成功']);
        }
    }

    //分配权限
    public function giveAccess()
    {
        $param = input('param.');
        $node = new Node();
        //获取现在的权限
        if('get' == $param['type']){
            $nodeStr = $this->getNodeInfo($param['id']);
            return json(['code' => 1, 'data' => $nodeStr, 'msg' => 'success']);
        }
        //分配新权限
        if('give' == $param['type']){

            $doparam = [
                'id' => $param['id'],
                'rule' => $param['rule']
            ];
            $result = DB::name('permission_role')->where('id',$param['id'])->update($doparam);
            if(false === $result){
                return json(['code' => 0, 'data' => '', 'msg' => $this->getError()]);
            }else{
                return json(['code' => 1, 'data' => '', 'msg' => '编辑角色成功']);
            }
        }
    }
    /**
     * 获取节点数据
     */
    public function getNodeInfo($id)
    {
        $result = DB::name('permission_node')->field('id,node_name')->select();
        $str = "";
        $rule = DB::name('permission_role')->where('id',$id)->value('rule');
        if(!empty($rule)){
            $rule = explode(',', $rule);
        }
        foreach($result as $key=>$vo){
            $vo['typeid']=0;
            $str .= '{ "id": "' . $vo['id'] . '", "pId":"' . $vo['typeid'] . '", "name":"' . $vo['node_name'].'"';

            if(!empty($rule) && in_array($vo['id'], $rule)){
                $str .= ' ,"checked":1';
            }

            $str .= '},';

        }

        return "[" . substr($str, 0, -1) . "]";
    }

}

