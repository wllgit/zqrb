<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/5/30
 * Time: 22:16
 */
namespace app\admin\controller;
use \think\Db;
use \think\Request;
use app\admin\controller\Base;

class Systemconfig extends Base{

    public function setDomain(){
        return $this->fetch();
    }

    public function setSystem(){
        return $this->fetch();
    }

}