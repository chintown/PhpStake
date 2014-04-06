<?php
    // 0 | 1
    define('DEV_MODE',1);
    if (DEV_MODE) {
        ini_set('display_errors', 'On');
    }
    // remote (for linode dev) | local (for Mac dev)
    define('ENV','local');

    define('AUTH_COOKIE_SECONDS', 60*60*24); // 24hr

    // error logging
    // 'http://03071e4c4c914bc78b720403e57d1ee4:89efa30eab8b43af86192dded6ee2b6c@sentry.chintown.org/5
    define('SENTRY_API_PHP', '');
    // 'http://03071e4c4c914bc78b720403e57d1ee4@sentry.chintown.org/5'
    define('SENTRY_API_JS', '');
