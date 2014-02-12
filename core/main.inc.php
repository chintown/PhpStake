<?php
    /* for self-contain testing only */
    /**
    $project_path = realpath(dirname(__FILE__).'/../');
    ini_set('include_path', ini_get('include_path') . ':' . $project_path);
    //*/

    /* parent project */
    /**/
    require 'config/dev.php';
    require 'config/prerequisite.php';

    $project_path = realpath(dirname(__FILE__).'/../');
    ini_set('include_path', ini_get('include_path') . ':' . $project_path);

    require 'config/path.php';
    require 'common/std.php';
    require 'lib/mdetect.php';
    require 'core/translation.php';
    require 'core/controller.php';
    require 'config/user.php';
    //*/

    /* child project */
    /**
    $project_path = realpath(dirname(__FILE__).'/../');
    ini_set('include_path', ini_get('include_path') . ':' . $project_path);
    // var_dump(ini_get('include_path'));
    require '/Users/chintown/src/php/PhpStake/core/main.inc.php';
    //*/
