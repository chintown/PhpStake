<?php

require_once 'vendor/autoload.php';
use DebugBar\StandardDebugBar;
if (is_defined_const_available('PHP_DEBUG_BAR') && PHP_DEBUG_BAR === 'on') {
    $baseUrl = '//'.WEB_HOST.'phpstake/debugbar/';
    $debugbar = new StandardDebugBar();
    $debugbarRenderer = $debugbar->getJavascriptRenderer($baseUrl);
}