<?php
    // 0 | 1
    define('DEV_MODE',0);
    if (DEV_MODE) {
        ini_set('display_errors', 'On');
    }
    // remote (for linode dev) | local (for Mac dev)
    define('ENV','remote');
//    if (DEV_MODE) { @mkdir('/tmp/xhprof/'); define('XHPROF_NS', SITE_CODE); }
//    if (DEV_MODE) { define('PHP_DEBUG_BAR', 'on'); }
//    if (DEV_MODE) { define('JS_PREF_BAR', 'on'); } // will conflict with PHP_DEBUG_BAR

    define('MOBILE_NO_TOOLBAR', false);
    define('MOBILE_APP_ICON', false);

    define('AUTH_COOKIE_SECONDS', 60*60*24); // 24hr
    define('AUTH_SESSION_SECONDS', 30 * 60); // 30min

    //define('FB_APP_ID', '');
    //define('FB_APP_SECRET', '');

    // error logging. http://sentry.chintown.org/
    define('SENTRY_API_PHP', null);
    define('SENTRY_API_JS', null);

    define('GA_CODE', null);
