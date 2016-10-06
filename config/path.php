<?php
    define('CHINTOWN_HOST', 'www.chintown.org');
    define('DB_HOST',     (ENV === 'local') ? CHINTOWN_HOST : 'localhost');


    define('SERVER_HOST', (ENV === 'local') ? 'localhost' : CURRENT_HOST);
    define('WEB_HOST',    (ENV === 'local') ? 'localhost/~chintown/' : SERVER_HOST); // for dpd.js
    define('WEB_PATH',    (ENV === 'local') ? '/~chintown/'.SITE_CODE : '/'.SITE_CODE);
    define('WEB_ROOT',    'http://'.SERVER_HOST.WEB_PATH);
    define('STATIC_ROOT', 'http://'.SERVER_HOST.WEB_PATH);


    define('HOME',        (ENV === 'remote') ? '/home/chintown' : '/Users/chintown');
    define('FOLDER_ROOT', HOME.'/proj/php/'.SITE_CODE.'/');


    define('PARENT_SITE_CODE', 'phpstake');
    define('PARENT_WEB_PATH',    (ENV === 'local') ? '/~chintown/'.PARENT_SITE_CODE : '/'.PARENT_SITE_CODE);
    define('PARENT_FOLDER_ROOT', HOME.'/proj/php/'.PARENT_SITE_CODE.'/');
