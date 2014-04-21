<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="initial-scale=1.0, user-scalable=no">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black">
    <title></title>

<!--    <link rel="stylesheet" href="--><?//=PARENT_WEB_PATH?><!--/css/reset.boilerplate.css">-->
<!--    <link rel="stylesheet" href="--><?//=PARENT_WEB_PATH?><!--/css/font-awesome.css">-->
    <link rel="stylesheet" href="<?=PARENT_WEB_PATH?>/css/jquery.mobile/jquery.mobile-1.4.2.min.css">
    <link rel="stylesheet" href="<?=PARENT_WEB_PATH?>/css/jquery.mobile/jquery.mobile.theme-1.4.2.min.css">
    <link rel="stylesheet" href="<?=$ENTRY_CSS?>">

    <script src="//<?=toggle_min_script('ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.js')?>" type="text/javascript"></script>
    <script>window.jQuery || document.write('<script src="<?=toggle_min_script('js/vendor/jquery-1.10.2.js')?>" type="text/javascript"><\/script>')</script>
    <script src="http://<?=toggle_min_script('code.jquery.com/mobile/1.4.2/jquery.mobile-1.4.2.js')?>" type="text/javascript"></script>

    <!-- Extra Codiqa features -->
<!--    <link rel="stylesheet" href="codiqa.ext.css">-->
    <!-- Extra Codiqa features -->
<!--    <script src="https://d10ajoocuyu32n.cloudfront.net/codiqa.ext.js"></script>-->

</head>
<body>
    <div data-role="page" id="<?=$CONTROLLER_NAME?>">
        <div id="page_header" data-theme="a" data-role="header" data-position="fixed">
            <h3 id="title">
                <?=$TITLE_PAGE?>
            </h3>

            <?php
            if (!empty($r_fr)) {
                ?>
                <a data-role="button" data-direction="reverse" data-rel="back" href="<?=$r_fr?>" data-icon="arrow-l" data-iconpos="left">&nbsp;</a>
            <?php
            }
            ?>
        </div>