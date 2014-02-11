<!DOCTYPE html>
<!--[if lt IE 7]>    <html class="no-js lt-ie9 lt-ie8 lt-ie7"> <![endif]-->
<!--[if IE 7]>     <html class="no-js lt-ie9 lt-ie8"> <![endif]-->
<!--[if IE 8]>     <html class="no-js lt-ie9"> <![endif]-->
<!--[if gt IE 8]><!--> <html class="no-js"> <!--<![endif]-->
  <head>
    <!-- <noscript><meta http-equiv="refresh" content="0;url=/nojs"></noscript> -->
    <meta charset='UTF-8'>
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
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
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0">

    <link rel="stylesheet" href="<?=$entry_css?>">
    <style>
      /*body {
        padding-top: 60px;
        padding-bottom: 40px;
      }*/
    </style>
    <link rel="stylesheet" href="<?=WEB_PATH?>/css/reset.boilerplate.css">
    <link rel="stylesheet" href="<?=WEB_PATH?>/css/bootstrap.css">
    <link rel="stylesheet" href="<?=WEB_PATH?>/css/font-awesome.css">
    <link rel="stylesheet" href="<?=WEB_PATH?>/css/webfont.css">
    <script src="<?=WEB_PATH?>/js/vendor/modernizr-2.6.1.min.js"></script>
  </head>
  <body class="<?=getBrowserUACSS()?>">
    <!--[if lt IE 7]>
        <p class="chromeframe">You are using an outdated browser. <a href="http://browsehappy.com/">Upgrade your browser today</a> or <a href="http://www.google.com/chromeframe/?redirect=true">install Google Chrome Frame</a> to better experience this site.</p>
    <![endif]-->
    <div id="wrap">
      <div class="navbar"> <?php /*.navbar-fixed-top*/ ?>
        <div class="navbar-inner">
          <div class="container">
              <ul id="menu" class="nav pull-left">
                  <li class="dropdown">
                      <a href="#" class="dropdown-toggle" data-toggle="dropdown"><i class="icon-reorder"></i></a>
                      <ul class="dropdown-menu">
                          <li><a href="<?=WEB_ROOT?>"><i class="icon-home"></i> <?=$NAV_DICT['index']?></a></li>
                          <li><a href="list.php"><i class="icon-list-alt"></i> <?=$NAV_DICT['list']?></a></li>
                          <li><a href="publish.php"><i class="icon-truck"></i> <?=$NAV_DICT['publish']?></a></li>
                          <li><a href="picked.php"><i class="icon-bullhorn"></i> <?=$NAV_DICT['picked']?></a></li>
                          <li><a href="news_giddens.php"><i class="icon-bullhorn"></i> <?=$NAV_DICT['news_giddens']?></a></li>
                          <?php       if (is_admin()) { ?>
                          <li><a href="xray.php"><i class="icon-eye-open"></i> <?=$NAV_DICT['xray']?></a></li>
                          <?php       } ?>
                      </ul>
                  </li>
              </ul>
            <!-- <a class="brand" href="<?=WEB_ROOT?>"><i class="icon-home"></i></a> -->
            <ul class="breadcrumb pull-left">
                <?=$BREADCRUMB?>
            </ul>
            <?php if(is_login()) { ?>
            <ul class="nav pull-right">
              <li class="dropdown">
                <a href="#" class="dropdown-toggle" data-toggle="dropdown"><?=$_SESSION['ID']?> <b class="caret"></b></a>
                <ul class="dropdown-menu">
                  <li><a href="settings.php"><i class="icon-cog"></i> <?=$NAV_DICT['settings']?></a></li>
                  <li class="divider"></li>
                  <li><a href="logout.php"><i class="icon-off"></i> <?=$NAV_DICT['logout']?></a></li>
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

              <div id="msg" class="container">
                  <span class="alert hide"></span>
              </div>

          </div>
        </div>
      </div>
      <div id="content" class="container">
