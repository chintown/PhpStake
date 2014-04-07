<!DOCTYPE html>
<!--[if lt IE 7]>    <html class="no-js lt-ie9 lt-ie8 lt-ie7"> <![endif]-->
<!--[if IE 7]>     <html class="no-js lt-ie9 lt-ie8"> <![endif]-->
<!--[if IE 8]>     <html class="no-js lt-ie9"> <![endif]-->
<!--[if gt IE 8]><!--> <html class="no-js"> <!--<![endif]-->
  <head>
    <!-- <noscript><meta http-equiv="refresh" content="0;url=/nojs"></noscript> -->
    <meta charset='UTF-8'>
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0" />
<?php
      if (!empty($HEADER_EXTRA)) { include("template/$HEADER_EXTRA"); }

      // compose title in the format: <detail> | <tab page>( | <site name>)
      // *_TITLE will be only purified in header.php

      $EXTRA_TITLE = isset($EXTRA_TITLE) ? $EXTRA_TITLE : '';
      $CONTROLLER_TITLE = isset($NAV_DICT[$CONTROLLER_NAME])
                              ? $NAV_DICT[$CONTROLLER_NAME]
                              : '';

      $TITLE = array($EXTRA_TITLE, $CONTROLLER_TITLE, $TITLE_SITE);
      $TITLE = array_filter($TITLE); // filter falsy value
      $TITLE = join(' | ', $TITLE);
      $TITLE = purify($TITLE, 'html');

      $KEYWORDS = purify($KEYWORDS, 'html');
?>
    <title><?=$TITLE?></title>
    <meta name="description" content="<?=$DESCRIPTION?>"/>

    <link rel="stylesheet" href="<?=$ENTRY_CSS?>">
    <style>
      /*body {
        padding-top: 60px;
        padding-bottom: 40px;
      }*/
    </style>
    <link rel="stylesheet" href="<?=PARENT_WEB_PATH?>/css/reset.boilerplate.css">
    <link rel="stylesheet" href="<?=PARENT_WEB_PATH?>/css/bootstrap.css">
    <link rel="stylesheet" href="<?=PARENT_WEB_PATH?>/css/font-awesome.css">
    <link rel="stylesheet" href="<?=PARENT_WEB_PATH?>/css/webfont.css"> <!-- or specific ones -->
    <script src="<?=PARENT_WEB_PATH?>/js/vendor/modernizr-2.6.1.min.js"></script>
    <script type="text/javascript" src="//dl1d2m8ri9v3j.cloudfront.net/releases/1.2.4/tracker.js" data-customer="54e327e40f1e46b08cf96b2d83630d58"></script>
  </head>
  <body class="<?=getBrowserUACSS()?>">
    <!--[if lt IE 7]>
        <p class="chromeframe">You are using an outdated browser. <a href="http://browsehappy.com/">Upgrade your browser today</a> or <a href="http://www.google.com/chromeframe/?redirect=true">install Google Chrome Frame</a> to better experience this site.</p>
    <![endif]-->

    <div id="wrap">
      <div class="navbar"> <!-- .navbar-fixed-top -->
        <div class="navbar-inner">
          <div class="container">
            <?
               $has_home_logo = !empty($HOME_LOGO);
               if ($has_home_logo) {
            ?>
            <a id="home_logo" class="brand" href="<?=WEB_ROOT?>"><div style="background-image: url('<?=$HOME_LOGO?>')"></div></a>
            <? }
               $class_menu_position = ($has_home_logo) ? 'pull-right' : 'pull-left';
            ?>

            <ul id="menu" class="nav <?=$class_menu_position?>">
              <li class="dropdown">
                <a href="#" class="dropdown-toggle" data-toggle="dropdown"><i class="icon-reorder"></i></a>
                <ul class="dropdown-menu">
                  <li><a href="<?=WEB_ROOT?>"><i class="icon-home"></i> <?=$NAV_DICT['index']?></a></li>
                  <?php       foreach ($DROPDOWN_DICT as $entry=>$profile) { ?>
                  <li><a href="<?=WEB_ROOT.'/'.$entry.'.php'?>"><i class="<?=$profile['icon']?>"></i> <?=$NAV_DICT[$entry]?></a></li>
                  <?php       } ?>
                  <?php       if (is_admin()) { ?>
                  <?php       } ?>
                </ul>
              </li>
            </ul>

            <ul class="breadcrumb pull-left">
              <?=$BREADCRUMB?>
            </ul>

            <?php if(is_login()) {
                    require_once "common/auth.php";
            ?>
            <ul id="user_control" class="nav pull-right">
              <li class="dropdown">
                <a href="#" class="dropdown-toggle" data-toggle="dropdown"><?=get_user_disp_name_from_session()?> <b class="caret"></b></a>
                <ul class="dropdown-menu">
                  <li><a href="setting.php"><i class="icon-cog"></i> <?=$NAV_DICT['setting']?></a></li>
                  <li class="divider"></li>
                  <li><a  id="logout" href="logout.php"><i class="icon-off"></i> <?=$NAV_DICT['logout']?></a></li>
                </ul>
              </li>

            </ul>
            <?php       if (is_admin()) {
                            $active = (DEV_MODE) ? 'active' : '';
            ?>
            <button type="button" class="btn btn-primary <?=$active?> pull-right" onmouseup="$(this).toggleClass('active'); DEV_MODE = $(this).hasClass('active');">DEV</button>
            <?php
                        }
                  } else { ?>
            <ul class="nav pull-right">
              <li><a href="login.php"><?=$NAV_DICT['login']?></a></li>
            </ul>
            <?php } ?>

          </div>
        </div>
      </div>
      <div id="content" class="container">
