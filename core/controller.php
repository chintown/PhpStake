<?php
    require 'raven.php';
    require_once('../lib/FirePHPCore/fb.php');

    // routing
    $script_name = $_SERVER['SCRIPT_NAME']; // /foo/bar/index.php
    $controller_id = basename($script_name, '.php'); // index
    $controller = basename($script_name); // index.php
    $controller_path = 'controller/_'.$controller;
    // loading
    if (file_exists(FOLDER_ROOT.$controller_path)) { // use (full) local path
        include $controller_path; // use path under php's include_path scope
    } else {
        $err_msg = 'WARN: '.FOLDER_ROOT.$controller_path.' does not exist.';
        if (DEV_MODE) {
            de($err_msg);
        } else {
            error_log($err_msg);
            Header('Location: error/404.html');
            exit(1);
        }
    }
    // executing
    if (function_exists('get')) { // get is the key method of any controller file
        $r = array();
        $_REQUEST = purify_values($_REQUEST, 'null|eol'); // policy: binary data in parameter is not allowed
        get($_REQUEST, &$r);
        // dump result from associated array to prefixed variable
        // for better coding experience
        extract($r, EXTR_PREFIX_ALL, 'r');
        //var_dump(get_defined_vars());
    } else {
        $err_msg = "WARN: [get] method is not available in _$controller.";
        if (DEV_MODE) {
            de($err_msg);
        } else {
            error_log($err_msg);
            Header('Location: error/500.html');
            exit(1);
        }
    }
    // plugging
    $extra_css = (file_exists(FOLDER_ROOT.'htdoc/css/'.$controller_id.'.css'))
                    ? WEB_PATH.'/css/'.$controller_id.'.css'
                    : '';

    // dynamic variable for global usage
    $IS_MOBILE = is_mobile_client();
    $CONTROLLER_NAME = $controller_id;
    $ENTRY_CSS = $extra_css;
    $BREADCRUMB = '';

    // FUTURE remember some parameter like (&lang=en)
    //fde(get_defined_constants());

    function is_mobile_client() {
        $uagent_obj = new uagent_info();
        return $uagent_obj->DetectTierIphone();
    }