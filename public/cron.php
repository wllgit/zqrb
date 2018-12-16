<?php


//shell:>>>>> php public/cron.php test/consume

if (PHP_SAPI != 'cli') {
    die('bad request');
}
set_time_limit(0);

define('APP_PATH', __DIR__ . '/../application/');
define('ATTACHMENT_PATH', __DIR__ . '/../attachment/');
define('BIND_MODULE', 'cron');

require __DIR__ . '/../thinkphp/start.php';