<?php
    // 0 | 1
    define('DEV_MODE',1);
    if (DEV_MODE) {
        ini_set('display_errors', 'On');
    }
    // remote (for linode dev) | local (for Mac dev)
    define('ENV','local');

    // error logging
    // 'http://03071e4c4c914bc78b720403e57d1ee4@sentry.chintown.org/5'
    define('SENTRY_API', '');
?>
