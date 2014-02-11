<?php
    function readpath($dir, $level, $last, &$dirs, &$files){
        // recursively parsing the directory until $last depth
        // storing directory entries into $dirs, file entries into $files
        //print $dir." (DIR)<br/>\n";
        $dp = opendir($dir);
        while (false !== ($file=readdir($dp)) && $level <= $last){
            if ($file == "." || $file == "..") {continue;}

            if (is_dir($dir."/".$file)) {
                readpath($dir."/".$file, $level+1, $last, $dirs, $files);
                $dirs[] = "$dir/$file";
            }
            else{
                $files[] = "$dir/$file";
            }
        }
    }

    /**
     * trailing_path('/five/four/three/two/one.file', 3)
     *      -> 'three/two/one.file'
     */
    function get_trailing_path($fn, $level, $resolve_realpath=false) {
        $path = $resolve_realpath ? realpath($fn) : $fn;
        $info = pathinfo($path);

        $result_path = array($info['basename']);
        $remain_path = $info['dirname'];

        for ($i=0; $i < ($level-1); $i++) {
            $curr_info = pathinfo($remain_path);
            $curr_dir  = $curr_info['dirname'];
            $curr_base  = $curr_info['basename'];
            array_unshift($result_path, $curr_base);
            if ($remain_path == '/' || $remain_path == '.') break;
            $remain_path = $curr_dir;
        }

        $result_path = join('/', $result_path);
        return $result_path;
    }
    //echo get_trailing_path('static/css/index.css', 5); // test

    /**
     * '/some/parent/folder/contains/some.file'
     *      -> create '/some/parent/folder/contains' if it does not exist
     */
    function mkdir_for_file_if_needed($file, $perm=0644) {
        $info = pathinfo($file);
        if (file_exists($info['dirname'])) {return;}

        mkdir($info['dirname'], $perm, true);
    }


    /**
     *  (space) ~!@#$%^&*=\;'/()[]{}<>`+|:"?
     *      ->
     *  "_"
     */
    function normalize_filename($text, $prefix_prevent_empty='fn_') {
        if ($prefix_prevent_empty === 'fn_') {
            $prefix_prevent_empty .= ''.time();
        }
        $text = preg_replace('/[ ~!@#$%\^&*=\\\\;\'\/(){}<>\\[\\]`+|:"]/', '_', $text);
        if (preg_replace('/_/', '', $text) === '') {
            $text = $prefix_prevent_empty . $text;
        }
        return $text;
    }