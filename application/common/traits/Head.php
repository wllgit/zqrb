<?php
namespace app\common\traits;
/**
 * @ClassName:    Name 
 * @Description:  Detail 
 * @Created by:   [villager] 
 * @DateTime:     2018-05-24T14:10:46+0800
 */
trait Head 
{
	//model入口
	public function index($action,$query) {
        if(!empty($query['where'])) {
            $this->where = $query['where'];
        }
        if(!empty($query['whereOr'])) {
            $this->whereOr = $query['whereOr'];
        }
        if(!empty($query['start'])) {
            $this->start = $query['start'];
        }
        if(!empty($query['offset'])) {
            $this->offset = $query['offset'];
        }
        if(!empty($query['field'])) {
            $this->self_field = $query['field'];
        }
        if(!empty($query['order'])) {
            $this->order = $query['order'];
        }
        if(!empty($query['data'])) {
            $this->data = $query['data'];
        }
        if(!empty($query['variables'])) {
            $this->variables = $query['variables'];
        }
        return $this -> $action();//分发接口
	}
}