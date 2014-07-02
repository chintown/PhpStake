<?php

if (is_defined_const_available('XHPROF_NS') && extension_loaded('xhprof')) {
    $ns = XHPROF_NS;

    $xhprof_data = xhprof_disable();
    $xhprof_runs = new XHProfRuns_Default();
    $run_id = $xhprof_runs->save_run($xhprof_data, $ns);

    $url = 'http://'.WEB_HOST.'xhprof_html/index.php';
    $url.= '?run=%s&source=%s';
    $url = sprintf($url, $run_id, $ns);

    echo "<a href='$url' target='_blank'>XHProf Output</a>";
}