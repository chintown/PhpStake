<?php

passthru("ps aux | grep mysqld");
require getcwd().'/../config/dev.php';
require getcwd().'/../config/path.php';
require getcwd().'/../config/pw.php';
$info = array(
    "HOST ".DB_HOST,
    "USER ".AUTH_USER,
    "AUTH " .substr(AUTH_PASS, 0, 2),
);
var_dump($info);
var_dump(mysql_connect(DB_HOST, AUTH_USER, AUTH_PASS));
