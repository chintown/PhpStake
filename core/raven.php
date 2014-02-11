<?php

    if (SENTRY_API_PHP != '' && file_exists($path_raven)) {
        $path_raven = '../../../open/raven-php/lib/Raven/Autoloader.php';
        Raven_Autoloader::register();
        $raven = new Raven_Client(SENTRY_API_PHP);
        function sde($msg) { // Sentry Debugging
            global $raven;
            $raven->captureMessage($msg);
        }
        $error_handler = new Raven_ErrorHandler($raven);
        set_exception_handler(array($error_handler, 'handleException'));
    }
