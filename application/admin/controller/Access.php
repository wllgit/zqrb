<?php
/**
 * @ClassName:    Access 
 * @Description:  Detail 
 * @Created by:   [villager] 
 * @DateTime:     2018-07-27T14:00:24+0800
 */
namespace app\admin\controller;
use app\admin\model\Role;
use app\admin\controller\Base;
use think\Db;
Class Access extends Base 
{
	/**
	 * [_initialize:  description] 
	 * @Created by:   [villager] 
	 * @DateTime:     2018-07-27T14:00:24+0800
	 */
	public function _initialize()
	{
		parent::_initialize();
		$this->db = Db::name('Access');
	}
	/**
	 * [accessList 权限列表]
	 * @AuthorHTL
	 * @DateTime  2018-07-27T14:09:15+0800
	 * @return    [type]                   [description]
	 */
	public function accessList()
	{
		// $field = '';
		// $where = ['is_show' => 2,'is_delete' => 0];
		// $list  = $this->db->where($where)->field($field)->paginate(10,false,['query' => request()->param()]);
		// $this->assign('list',$list);
		// $this->fetch();
		if(request()->isAjax()){
            $param = input('');
            $limit = $param['pageSize'];
            $offset = ($param['pageNumber'] - 1) * $limit;
            $where=['is_delete'=>0];
            if (isset($param['searchText']) && !empty($param['searchText'])) {
                $where['name'] = ['like', '%' . $param['searchText'] . '%'];
            }
            $Result=$this->db->limit($offset, $limit)->where($where)->select();
            foreach($Result as $key=>$vo){
                $operate = [
                    '编辑' => url('Access/accessedit', ['id' => $vo['id']]),
                    '删除' => "javascript:accessDel('".$vo['id']."')"
                ];
                $Result[$key]['operate'] = showOperate($operate);
            }
            $return['total'] = $this->db->where($where)->count();  //总数据
            $return['rows'] = $Result;
            return json($return);
        }
        return $this->fetch();
	}
	/**
	 * description 添加权限
	 * @author    [villager]
	 * @param     [type]     $param1 [description]
	 * @param     [type]     $param2 [description]
	 * @return    [type]     [description]
	 * @DateTime  2018-07-27T14:00:24+0800
	*/
	public function accessAdd()
	{
		$where = ['is_delete'=>0,'parent_id' =>0];
		$field = 'id,name,parent_id';
		$list = $this->db->where($where)->field($field)->select();
		if(request()->isPost()){
            $param = input('post.');
            $param = parseParams($param['data']);
            $param['module'] = strtolower($param['module']);
            $param['controller'] = strtolower($param['controller']);
            $param['action'] = strtolower($param['action']);
            $param['create_time'] = time();
            $param['update_time'] = time();
        	$result = $this->db->insert($param);
        	$this->accessUserUpdate();
            if(false === $result){
                return json(['code' => 0, 'data' => '', 'msg' => $this->getError()]);
            }else{
                return json(['code' => 1, 'data' => '', 'msg' => '添加节点成功']);
            }
        }
        $this->assign('list',$list);
        return $this->fetch();
	}
	/**
	 * description
	 * @author    [villager]
	 * @param     [type]     $param1 [description]
	 * @param     [type]     $param2 [description]
	 * @return    [type]     [description]
	 * @DateTime  2018-07-27T14:00:24+0800
	*/
	public function accessDelete()
	{
		$id = input('param.id');
		$data = ['is_delete' => 1];
        $result = $this->db->where('id',$id)->update($data);
        if(false === $result){
            return json(['code' => 0, 'data' => '', 'msg' => $this->getError()]);
        }else{
            return json(['code' => 1, 'data' => '', 'msg' => '删除节点成功']);
        }
	}
	/**
	 * [accessEdit 权限节点]
	 * @AuthorHTL
	 * @DateTime  2018-07-27T17:05:51+0800
	 * @return    [type]                   [description]
	 */
	public function accessEdit()
	{
		$where = ['is_delete'=>0,'is_show'=>2,'parent_id' =>0];
		$field = 'id,name,parent_id';
		$list = $this->db->where($where)->field($field)->select();
		if(request()->isPost()){
            $param = input('post.');
            $param = parseParams($param['data']);
            $param['module'] = strtolower($param['module']);
            $param['controller'] = strtolower($param['controller']);
            $param['action'] = strtolower($param['action']);
        	$where = ['id'=>$param['id']];
        	$result = $this->db->where($where)->update($param);
        	$this->accessUserUpdate();
            if(false === $result){
                return json(['code' => 0, 'data' => '', 'msg' => $this->getError()]);
            }else{
                return json(['code' => 1, 'data' => '', 'msg' => '更新节点成功']);
            }
            
        }
        $id = input('param.id');
        $info=$this->db->where('id',$id)->find();
        $this->assign('info',$info);
        $this->assign('list',$list);
        return $this->fetch('accessadd');
	}
	/**
	 * [accessUserUpdate 更新用户权限]
	 * @AuthorHTL
	 * @DateTime  2018-07-28T12:19:58+0800
	 * @return    [type]                   [description]
	 */
	public function accessUserUpdate()
	{
		$user_id = session('id');
		$hasUser = db('admin')->where('id', $user_id)->find();
		//获取该管理员的角色信息
        $role = new Role();
        $info = $role->getRoleInfo($hasUser['role_id']);
		session('role', $info['rolename']);  //角色名
        session('rule', $info['access_ids']);  //角色节点
        session('action', $info['action']);  //角色权限
	}
}