<?php
    /* for self-contain testing only */
    /**
    $project_path = realpath(dirname(__FILE__).'/../');
    ini_set('include_path', ini_get('include_path') . ':' . $project_path);
    //*/


    /**/ //__PARENT_PROJECT__
    require 'config/dev.php';
    require 'config/prerequisite.php';

    $project_path = realpath(dirname(__FILE__).'/../').'/';
    ini_set('include_path', ini_get('include_path') . ':' . $project_path);

    require $project_path.'config/path.php';
    require $project_path.'common/std.php';
    require $project_path.'lib/mdetect.php';
    require $project_path.'core/translation.php';
    require $project_path.'core/controller.php';
    require 'config/user.php';
    //*/


    /** //__CHILD_PROJECT__
    $project_path = realpath(dirname(__FILE__).'/../');
    ini_set('include_path', ini_get('include_path') . ':' . $project_path);
    // var_dump(ini_get('include_path'));
    require '/Users/chintown/src/php/PhpStake/' . 'core/main.inc.php'; // __PARENT_ROOT__
    //*/