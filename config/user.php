<?php
    $allowed_langs = array('en', 'zh-hant');
    $default_lang = 'en'; //'zh-hant';
    $lang = (empty($_GET['lang'])) ? $default_lang : strtolower(trim($_GET['lang']));
    $lang = in_array($lang, $allowed_langs) ? $lang : 'zh-hant';
    define('LANG', $lang);
    $TRANS = new Translator(LANG);

    // browser title
    $TITLE_SITE = '___SITE_CAPED___'; // 'eContent — My ePub Assistant';
    // display title of html page
    $TITLE_PAGE = '___SITE_CAPED___';
    // meta tags
    $KEYWORDS = '___SITE___';
    $DESCRIPTION = '___DESCRIPTION___';
    // prefix of browser title shows the current page
    $NAV_DICT = array(
        'index' => $TRANS->k('entry.index', 'capital')
    );
