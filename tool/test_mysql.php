<?php

passthru("ps aux | grep mysqld");
$project_path = $argv[1];
require $project_path.'/config/prerequisite.php';
require $project_path.'/config/dev.php';
require $project_path.'/config/path.php';
require $project_path.'/config/pw.php';
$info = array(
    "HOST ".DB_HOST,
    "USER ".AUTH_USER,
    "AUTH " .substr(AUTH_PASS, 0, 2),
);
var_dump($info);
var_dump(mysql_connect(DB_HOST, AUTH_USER, AUTH_PASS));
