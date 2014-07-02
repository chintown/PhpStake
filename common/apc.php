<?php
    function is_apc_ready() {
        return extension_loaded('apc') && ini_get('apc.enabled');
    }
