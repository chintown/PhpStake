<?php

if (is_defined_const_available('PHP_DEBUG_BAR') && PHP_DEBUG_BAR === 'on') {
    echo $debugbarRenderer->renderHead();
    echo $debugbarRenderer->render();
}