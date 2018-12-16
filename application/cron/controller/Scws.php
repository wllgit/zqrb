<?php
/**
 * Created by PhpStorm.
 * User: sogu
 * Date: 2018/7/25
 * Time: 下午4:13
 */
namespace app\cron\controller;

class Scws{
    /**
     * 分词
     */
    public function scws(){

        $res = get_input();
        $keywords = str_replace(' ','',trim($res['keywords']));
        $fpath = ini_get('scws.default.fpath');
        $so = scws_new();
        $so->set_charset('utf-8');
        $so->add_dict($fpath . '/dict.utf8.xdb');
        $so->set_rule($fpath . '/rules.utf8.ini');
        $so->set_ignore(true);
        $so->set_multi(false);
        $so->set_duality(false);
        $so->send_text($keywords);
        $results =  $so->get_result();

        $data = [
            'status' => 'ok',
            'words' => $results
        ];
        return json_encode($data);
    }
}