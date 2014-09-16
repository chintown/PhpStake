<?php
    $cwd = dirname(__FILE__);
    $project_path = $argv[1];
    require $project_path.'/config/prerequisite.php';
    require $project_path.'/config/dev.php';
    require $project_path.'/config/path.php'; // need DB_SERVER
    require $project_path.'/config/pw.php';
    require $cwd.'/../common/db.php';

    fwrite(STDOUT, "name: ");
    $user = fgets(STDIN);
    $user = trim($user, "\n");
    fwrite(STDOUT, "pass: ");
    $pass = fgets(STDIN);
    $pass = trim($pass, "\n");

    $q = "SELECT COUNT(*) num FROM ".AUTH_TABLE." WHERE pig='$user'";
    $num = db2Val(dbQ($q), 'num');
    if ($num == '0') {
        $q = "INSERT INTO  `".AUTH_DB."`.`".AUTH_TABLE."` (`pig` , `pen`) VALUES ('$user',  '');";
        dbQ($q);
    }
    var_dump(db_auth_update($user, $pass) === DB_VALID);
