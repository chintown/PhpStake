<?php

if (is_defined_const_available('XHPROF_NS') && extension_loaded('xhprof')) {
    include_once 'lib/xhprof/utils/xhprof_lib.php';
    include_once 'lib/xhprof/utils/xhprof_runs.php';

    xhprof_enable(XHPROF_FLAGS_CPU + XHPROF_FLAGS_MEMORY);
}