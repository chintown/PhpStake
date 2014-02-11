<?php
    // 0 | 1
    define('DEV_MODE',1);
    if (DEV_MODE) {
        ini_set('display_errors', 'On');
    }
    // remote (for linode dev) | local (for Mac dev)
    define('ENV','local');
?>
