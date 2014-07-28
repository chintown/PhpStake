<?php
    /* data manipulation */
    function is_defined_const_available($const_name) {
        return defined($const_name) && constant($const_name);
    }
    function is_assoc($arr) {
        return array_keys($arr) !== range(0, count($arr) - 1);
    }
    function pickup() {
        // get key-value pairs of given keys from the associated array
        // and set empty string value if key does not exist
        $picked = array();
        $arg_list = func_get_args();
        $actual = array_shift($arg_list);
        foreach($arg_list as $expected) {
            if (isset($actual[$expected])) {
                $picked[$expected] = $actual[$expected];
            } else {
                $picked[$expected] = '';
            }
        }
        return $picked;
    }
    function optional($nullable, $fallback) {
        if (is_array($nullable)) {
            if (is_array($nullable[0])) {
                return (!isset($nullable[0][$nullable[1]])) ? $fallback : $nullable[0][$nullable[1]];
            } else {
                return (!property_exists($nullable[0], $nullable[1])) ? $fallback : $nullable[0]->{$nullable[1]};
            }
        } else {
            return (!isset($nullable)) ? $fallback : $nullable;
        }
    }
    function optional_str($nullable) {
        return optional($nullable, '');
    }
    function not_empty($val) { return !empty($val); }
    function map($arr, $callback, $is_pair_para=true) {
        $res = array();
        foreach ($arr as $k => $v) {
            $res[$k] = ($is_pair_para)
                        ? $callback($k,$v)
                        : $callback($v)
                        ;
        }
        return $res;
    }
    function pair($k, $v) {
        return "$k=$v";
    }
    function pickup_prefix($haystack, $prefix) {
        $matched_keys = preg_grep('/^'.$prefix.'/', array_keys($haystack));
        $key_val_flipped = array_flip($matched_keys);
        return array_intersect_key($haystack, $key_val_flipped);
    }
    /* data manipulation */

    /* string manipulation */
    function ucfirst_sentences($s) {
        return  preg_replace_callback('/([.!?])\s+(\w)/', function ($matches) {
            return strtoupper($matches[1] . ' ' . $matches[2]);
        }, ucfirst(strtolower($s)));
    }
    /* string manipulation */

    /* control */
    function mergeQuery($given_query) { // TODO rename
        parse_str($given_query, $given_queries);
        parse_str($_SERVER['QUERY_STRING'], $queries);
        $new_queries = array_merge($queries, $given_queries);
        return http_build_query($new_queries);
    }
    function fix_font_css_path($style_content) {
        // ../ -> PARENT_FOLDER_ROOT/
        return preg_replace('/[.]{2}/', 'http://'.SERVER_HOST.PARENT_WEB_PATH, $style_content);
    }
    function fix_url_protocol($url_might_miss_protocol, $protolcol=null) {
        if (!$protolcol) {
            $protocol = is_https() ? 'https:' : 'http:';
        }
        if (strpos($url_might_miss_protocol, '://') !== false) {
            return $url_might_miss_protocol;
        } else {
            return $protocol.$url_might_miss_protocol;
        }
    }
    /* control */

    /* template */
    $HEADER_EXTRA = '';
    $FOOTER_EXTRA = '';
    $MODERNIZR = '';
    function add_extra_header($template_name) {
        global $HEADER_EXTRA;
        $HEADER_EXTRA = $template_name;
    }
    function add_extra_footer($template_name) {
        global $FOOTER_EXTRA;
        $FOOTER_EXTRA = $template_name;
    }
    function add_modernizr($template_name) {
        global $MODERNIZR;
        $MODERNIZR = $template_name;
    }
    function amend_nojs_controller_name($controller_name) {
        $needle = '_nojs';
        $pos = strrpos($controller_name, $needle);
        return ($pos === false)
                    ? $controller_name
                    : substr_replace($controller_name, '', $pos, strlen($needle));
    }
    function serialize_vars_as_js($global_var_for_js) {
        $vars = array();
        foreach ($global_var_for_js as $k => $v) {
            if (gettype($v) == "integer" ) {
                $vars[] = "$k = $v";
            } else if (gettype($v) == "array" ) {
                $vars[] = "$k = ".json_encode($v);
            } else {
                $vars[] = "$k = '$v'";
            }
        }
        $vars = '<script type="text/javascript">var '.join(',', $vars).';</script>';
        return $vars;
    }
    function render_back_link($target=array("path"=>"index.php", "text"=>"Home"), $is_return=false) {
        $s = array();
        $s[] = '<ul class="nav nav-tabs nav-stacked">';
        $s[] = '<li><a href="'.$target['path'].'">';
        $s[] = '<i class="icon-circle-arrow-left"></i>';
        $s[] = '&nbsp;'.$target['text'].'</a>';
        $s[] = '</li></ul>';
        $s = implode('', $s);
        if (!$is_return) {
            echo $s;
            return true;
        } else {
            return $s;
        }
    }
    function render_if_non_empty($var, $fmt_str) {
        render_if(!empty($var), $var, $fmt_str);
    }
    function render_if($condition, $var, $fmt_str) {
        if ($condition) {
            $s = sprintf($fmt_str, $var);
            echo $s;
        }
    }
    function generate_pager($info=array('val'=>0, 'text'=>0, 'class'=>''), $active=false) {
        $active = ($active) ? 'active' : '';
        $query = 'o=' . $info['val'];
        $query = mergeQuery($query);
        //de($query);
        return '<li class="pager ' . $active . ' '. $info['class'] . '" ><a href="?' . $query . '">' . $info['text'] . '</a></li>';
    }
    function render_pagers($info=array('low'=>0, 'high'=>10, 'current'=>0, 'max'=>0, 'num_per_page'=> 10), $is_return=false) {
        global $TRANS;
        $s = array();
        $s[] = '<ul class="pagination container">';
        $class_first = ($info['low'] === 1) ? 'inactive' : '';
        $class_prev = ( $info['current'] === 1) ? 'inactive' : '';
        $class_next = ( $info['current'] === $info['max']) ? 'inactive' : '';
        $class_last = ($info['low'] + $info['num_per_page'] >= $info['max']) ? 'inactive' : '';
        $s[] = generate_pager(array('val'=> 1, 'text'=> 1, 'class'=> $class_first));
        $s[] = generate_pager(array('val'=> $info['current'] - 1, 'text'=> $TRANS->k('common.prev page', 'capital'), 'class'=> $class_prev));
        for ($i = $info['low']; $i <= $info['high']; $i++) {
            $s[] = generate_pager(
                array('val'=> $i, 'text'=> $i, 'class'=> ''),
                ($i === $info['current'])
            );
        }
        //$class = ($info['high'] >= $info['max']) ? 'inactive' : '';
        $s[] = generate_pager(array('val'=> $info['current'] + 1, 'text'=> $TRANS->k('common.next page', 'capital'), 'class'=> $class_next));
        $s[] = generate_pager(array('val'=> $info['max'], 'text'=> $info['max'], 'class'=> $class_last));
        $s[] = '</ul>';
        $s = implode('', $s);
        if (!$is_return) {
            echo $s;
            return true;
        } else {
            return $s;
        }
    }
    /* http://stackoverflow.com/questions/79960/how-to-truncate-a-string-in-php-to-the-word-closest-to-a-certain-number-of-chara */
    function token_truncate($string, $width) {
        $parts = preg_split('/([\s\n\r]+)/', $string, null, PREG_SPLIT_DELIM_CAPTURE);
        $parts_count = count($parts);

        $length = 0;
        $last_part = 0;
        for (; $last_part < $parts_count; ++$last_part) {
            $length += mb_strlen($parts[$last_part]);
            if ($length > $width) { break; }
        }
        $final = implode(array_slice($parts, 0, $last_part));
        $final .= (mb_strlen($final) < mb_strlen($string))
                    ? '…' : '';
        return $final;
    }
    function get_breadcrumb() {
        global $NAV_DICT, $CONTROLLER_NAME;
        $crumb = array(array(
                'name'=> $NAV_DICT['index'],
                'href'=> WEB_PATH.'/index.php'
            ));
        if ($CONTROLLER_NAME !== 'index') {
            $crumb[] = array(
                'name'=> $NAV_DICT['list'],
                'href'=> WEB_PATH.'/list.php'
            );
        }
        return $crumb;
    }
    function get_breadcrumb_class($filename, $class='active') {
        return (basename($_SERVER['PHP_SELF']) == $filename) ? ' '.$class : '';
    }
    function generate_nested($input, $depth=0, $purify=false) {
        if (!is_array($input)) {
            de('generate_nested only accepts array input');
            return '';
        }
        $r = array();
        $r[] = "<ul>";
        foreach ($input as $k=> $v) {
            $sub_v = '';
            if (is_array($v)) {
                $sub_v  = generate_nested($v, $depth+1, $purify);
            } else {
                $sub_v = ''.$v;
                $sub_v = ($purify) ? purify($sub_v, 'html') : $sub_v;
            }
            $s = "<li class='pairs lv$depth'><span class='k'>" . purify(''.$k, 'html') . "</span>  <span class='v'>" . $sub_v . "</span></li>";
            $r[] = $s;
        }
        $r[] = "</ul>";
        return implode("\n", $r);

    }
    /* template */

    /* time */
    // http://php.net/manual/en/function.time.php
    function nicetime($date,
                      $periods=array("second", "minute", "hour", "day", "week", "month", "year", "decade"),
                      $lengths=array("60","60","24","7","4.35","12","10"),
                      $tenses=array('past'=>'ago', 'future'=>'from now'),
                      $plural='s'
    ) {
        if(empty($date)) {
            return "No date provided";
        }

//        $periods         = array("second", "minute", "hour", "day", "week", "month", "year", "decade");
//        $lengths         = array("60","60","24","7","4.35","12","10");

        $now               = time();
        $unix_date         = strtotime($date);

        // check validity of date
        if (empty($unix_date)) {
            return "Bad date";
        }

        // is it future date or past date
        if ($now > $unix_date) {
            $difference     = $now - $unix_date;
            $tense          = $tenses['past'];

        } else {
            $difference     = $unix_date - $now;
            $tense          = $tenses['future'];
        }

        for ($j = 0; $difference >= $lengths[$j] && $j < count($lengths)-1; $j++) {
            $difference /= $lengths[$j];
        }

        $difference = round($difference);

        if ($difference != 1) {
            $periods[$j].= $plural;
        }

        return "$difference $periods[$j] {$tense}";
    }
//    $date = "2013-01-14 18:50";
//    $result = nicetime($date); // 2 days ago
//    var_dump($result);
    /* time */

    /* security */
    function is_csrf_request() {
        $expected_uri = WEB_ROOT;
        $comparing_max_pos = strlen($expected_uri);
        $r = (strncmp(@$_SERVER['HTTP_REFERER'], $expected_uri, $comparing_max_pos));
        error_log('ERROR: CSRF found');
        return $r;
    }
    function is_login() {
        return (isset($_SESSION['ID']) && trim($_SESSION['ID']) != '');
    }
    function is_admin() {
        return (isset($_SESSION['ROLE']) && trim($_SESSION['ROLE']) === 'admin');
    }
    function purify($input, $methods) {
        // skip non-string input
        if (gettype($input) == 'NULL') {
            $input = '';
        } else if (gettype($input) != 'string') {
            $type = gettype($input);
            de("invalid  purify input type [$type] (should be string).", $backtrace_depth=2);
            return false;                                               // exit
        }

        $purifying = $input;
        $methods = explode('|', $methods);
        foreach ($methods as $method) {
            if ($method == 'html') {
                $purifying = htmlspecialchars($purifying, ENT_QUOTES);
            } else if ($method == 'url') {
                $purifying = rawurlencode($purifying);
            } else if ($method == 'urldecode') {
                $purifying = rawurldecode($purifying);
            } else if ($method == 'sql') {
                $purifying = addslashes($purifying);
            } else if ($method == 'eol') {
                $purifying = preg_replace(array('/%0d/','/%0a/','/\\r/','/\\n/'),
                                          array('','','',''),
                                          $purifying);
            } else if ($method == 'null') {
                $purifying = str_replace("\0", '', $purifying);
            } else {
                de("invalid purify method [$method]. (rollback all)");
                return false;                                           // exit
            }
        }
        $purified = $purifying;
        return $purified;
    }
    function purify_values($dict, $methods) {
        $purified = array();
        foreach ($dict as $k => $v) {
            if (is_array(($v))) {
                $purified[$k] = purify_values($v, $methods);
            } else {
                $purified[$k] = purify($v, $methods);
            }
        }
        return $purified;
    }
    function get_real_ip() {
        // http://roshanbh.com.np/2007/12/getting-real-ip-address-in-php.html
        if (!empty($_SERVER['HTTP_CLIENT_IP'])) { //check ip from share internet
            $ip=$_SERVER['HTTP_CLIENT_IP'];
        } else if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) { //to check ip is pass from proxy
            $ip=$_SERVER['HTTP_X_FORWARDED_FOR'];
        } else {
            $ip=$_SERVER['REMOTE_ADDR'];
        }
        return $ip;
    }
    function is_https() {
        return (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
                    || $_SERVER['SERVER_PORT'] == 443;
    }
    /* security */

    /* debug */
    function toggle_min_script($file_path) {
        $norm_path = $file_path;
        if (!DEV_MODE) {
            $info = pathinfo($file_path);
            $norm_path = $info['dirname'].'/'.$info['filename'].'.min.'.$info['extension'];
        }
        return $norm_path;
    }
    function html_it($obj) {
        if (is_string($obj)) {
            $obj = htmlspecialchars($obj);
        } else if (is_bool($obj)) {
            $obj = ($obj) ? 'true' : 'false';
        } else if (is_array($obj)) {
            foreach ($obj as $k => $v) {
                $obj[$k] = html_it($v);
            }
        } else {
            // $obj = $obj;
        }
        return $obj;
    }
    // NOTE:
    // - use [] instead of "" in de() message
    function de($arr, $backtrace_depth=1) {
        if (!DEV_MODE) {return; }

        $msg = array();
        $msg[] = "<div class='debug'><code><pre>";
        $msg[] = get_caller($backtrace_depth);
        $msg[] = '<br/>';
        $var_name = get_var_name($arr);
        $msg[] = ($var_name !== false) ? $var_name.' ' : '';
        $msg[] = var_export($arr, true);
        //$msg[] = nl2br(var_export($arr, true));
        $msg[] = "</pre></code></div>";
        echo join('', $msg);
    }
    function get_caller($backtrace_depth) {
        $trace = debug_backtrace();
        for($i=0; $i<$backtrace_depth; $i++) {
            array_shift($trace);
        }
        $caller_file_info = array_shift($trace);
        $caller_info = array_shift($trace);
        $parent_caller =    (empty($caller_info['function']))
                            ? ''
                            :"{$caller_info['function']}()";
        $msg = "$parent_caller { ... {$caller_file_info['function']}() ...} ".
                "at +{$caller_file_info['line']} {$caller_file_info['file']}";
        return $msg;
    }
    function get_var_name($var) {
        foreach($GLOBALS as $var_name => $value) {
            if ($value === $var) {
                return $var_name;
            }
        }
        return false;
    }
    function fde($arr) {
        if (!DEV_MODE) {return; }
        fb($arr);
    }
    function bde($arr) {
        error_log(var_export($arr, true));
    }
    /* debug */

    // http://davidshariff.com/blog/100-no-css-hacks/
    function getBrowserUACSS() {

        $ua = strtolower($_SERVER['HTTP_USER_AGENT']);

        $platform = '';
        if (preg_match('/linux/i', $ua)) {
            $platform = 'linux';
        }
        else if (preg_match('/macintosh|mac os x/i', $ua)) {
            $platform = 'mac';
        }
        else if (preg_match('/windows|win32/i', $ua)) {
            $platform = 'windows';
        }

        // browsers to sniff and the css class name to give them
        // yes browser sniffing sucks, but it will do for now
        $browsers = array(
            'chrome'  => 'chrome',
            'safari'  => 'safari',
            'opera'   => 'opera',
            'msie'    => 'ie',
            'firefox' => 'ff'
        );

        $browser_css_string = '';

        foreach ($browsers as $browser => $css_class) {
            if (preg_match('#(' . $browser .')[/ ]+([0-9]+(?))#', $ua, $version)) {
                $browser_css_string = $platform . ' ' . $css_class . ' ' . $css_class . '_v' .$version[2];
                break;
            }
        }

        return $browser_css_string;
    }
