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
namespace app\admin\model;

use think\Model;

class Access extends Model
{
    protected $table = "scs_access";

    public function node(){
        return $this->field('*')->select();
    }
    /**
     * 获取节点数据
     */
    public function getNodeInfo($id)
    {
        $where = ['is_delete' => 0];
        $result = $this->field('id,name,parent_id')-> where($where)->select();
        $str = "";

        $role = new Role();
        $rule = $role->getRuleById($id);

        if(!empty($rule)){
            $rule = explode(',', $rule);
        }
        foreach($result as $key=>$vo){
            $str .= '{ "id": "' . $vo['id'] . '", "pId":"' . $vo['parent_id'] . '", "name":"' . $vo['name'].'"';

            if(!empty($rule) && in_array($vo['id'], $rule)){
                $str .= ' ,"checked":1';
            }

            $str .= '},';

        }

        return "[" . substr($str, 0, -1) . "]";
    }

    /**
     * 根据节点数据获取对应的菜单
     * @param $nodeStr
     */
    public function getMenu($nodeStr = '')
    {
        //超级管理员没有节点数组
        $where = empty($nodeStr) ? 'is_show = 2 and is_delete = 0' : 'is_show = 2 and id in('.$nodeStr.') and is_delete = 0';

        $result = db('access')->field('id,name,parent_id,controller,action,icron')
            ->where($where)->select();
        $menu = prepareMenu($result);

        return $menu;
    }
    /**
     * [addNode 添加节点]
     * @AuthorHTL
     * @DateTime  2018-07-27T13:55:11+0800
     */
    public function addNode()
    {
        $result = $this -> save($this->data);
        return $result;
    }
}
