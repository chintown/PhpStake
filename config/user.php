<?php
    $allowed_langs = array('en', 'zh-hant');
    $default_lang = 'en'; //'zh-hant';
    $lang = (empty($_GET['lang'])) ? $default_lang : strtolower(trim($_GET['lang']));
    $lang = in_array($lang, $allowed_langs) ? $lang : 'zh-hant';
    define('LANG', $lang);
    $TRANS = new Translator(LANG);

    $site_caped = ucfirst(SITE_CODE);
    // browser title
    $TITLE_SITE = $site_caped;
    // display title of html page
    $TITLE_PAGE = $site_caped;
    // meta tags
    $KEYWORDS = ''.SITE_CODE;
    $DESCRIPTION = '___DESCRIPTION___';
    // prefix of browser title shows the current page
    $NAV_DICT = array(
        'index' => $TRANS->k('entry.index', 'capital')
    );
