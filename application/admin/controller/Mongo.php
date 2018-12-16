<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/5/7
 * Time: 15:37
 */
namespace app\admin\Controller;
use think\Request;
class Mongo extends Base{
    public function mongo()
    {
        $MONGO_SERVER = array(    'host'=>'127.0.0.1',
            'port'=>27017,
            'dbname'=>'mongo_fast',
            'user'=>'',
            'pwd'=>''
        );

        $count = $coll->count();
        print("count: " . $count);

        $host_port = $MONGO_SERVER['host'] . ":" . $MONGO_SERVER['port'];        // '10.123.55.16:20517'

        $conn = new MongoClient($host_port);
        $db = $conn->selectDB($MONGO_SERVER['dbname']);
        $coll = new MongoCollection($db, $connName);
        $conn->close();
    }
}
