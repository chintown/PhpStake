<?php
    // add this file at the top of any entry file if authentication is needed
    // this file will be included before any 3rd/user files.
    // use generic php method ONLY rather than 3rd/user method
    #error_log(var_export($_SESSION, true));
    if((isset($_SESSION['ID']) && trim($_SESSION['ID']) != '')) {
        // valid user
    } else {
        $ref = 'http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']; // keep query params, too. do not use PHP_SELF
        $ref = rawurlencode($ref);
        header('Location: login.php?r='.$ref); // TO CHECK: should avoid NUL character for setting malicious header.
    }
