<?php
    define('CHINTOWN_HOST', 'www.chintown.org');
    define('SERVER_HOST', (ENV === 'remote')
                        ? 'x'
                        : 'localhost');
    define('WEB_HOST',    (ENV === 'remote')
                        ? SERVER_HOST
                        : 'localhost/~chintown');
    define('DB_HOST', (ENV === 'remote')
                        ? 'localhost'
                        : CHINTOWN_HOST);


    define('WEB_PATH',    '/___SITE___');
    define('WEB_ROOT',    'http://'.WEB_HOST.WEB_PATH);
    define('STATIC_ROOT', 'http://'.WEB_HOST.WEB_PATH);


    define('HOME', (ENV === 'remote')
                        ? '/home/chintown'
                        : '/Users/chintown');
    define('FOLDER_ROOT', HOME . '/src/php/___SITE___/');
