<?php
namespace app\index\controller;
use app\common\logic\News as newsLogic;
use think\Controller;

class News extends Controller
{
    public function detail()
    {
    	$newsLogic = new newsLogic();
    	$param = get_input();
    	$info = $newsLogic->newsDetail($param);
    	$this->assign('info',$info);
        return $this->fetch();
    }
}
