<?php

passthru("ps aux | grep mysqld");
$cwd = dirname(__FILE__);
require $cwd.'/../config/dev.php';
require $cwd.'/../config/path.php';
require $cwd.'/../config/pw.php';
$info = array(
    "HOST ".DB_HOST,
    "USER ".AUTH_USER,
    "AUTH " .substr(AUTH_PASS, 0, 2),
);
var_dump($info);
var_dump(mysql_connect(DB_HOST, AUTH_USER, AUTH_PASS));
