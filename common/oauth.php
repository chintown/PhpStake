<?php
    define('OAUTH_STATE_REQUEST', 0);
    define('OAUTH_STATE_CALLBACK', 1);
    function evaluate_oauth_state($code, $token) {
        return (!empty($code) && !empty($token)) ? OAUTH_STATE_CALLBACK : OAUTH_STATE_REQUEST;
    }
    // https://developers.facebook.com/docs/facebook-login/manually-build-a-login-flow/
    function delegate_by_fb_oauth($url_callback=null) {
        // redirect to fb login path
        // user authentication
        // get fb "code"
        $url_callback = isset($url_callback) ? $url_callback : WEB_ROOT.'/login.php';
        $dialog_url = "https://www.facebook.com/dialog/oauth"
            ."?client_id=" . FB_APP_ID
            ."&redirect_uri=" . urlencode($url_callback)
            ."&state=" . $_SESSION['token'];
        if (DEV_MODE) {
            error_log($dialog_url);
        }
        echo("<script> top.location.href='" . $dialog_url . "'</script>");
        exit(0);
    }
    // https://developers.facebook.com/docs/facebook-login/access-tokens
    function request_fb_graph_token($code, $url_callback=null) {
        // get fb "access token" for using Graph API
        $url_callback = isset($url_callback) ? $url_callback : WEB_ROOT.'/login.php';
        $token_url = "https://graph.facebook.com/oauth/access_token"
                    ."?client_id=" . FB_APP_ID
                    ."&redirect_uri=" . urlencode($url_callback)
                    ."&client_secret=" . FB_APP_SECRET
                    ."&code=" . $code;

        $response = file_get_contents($token_url);
        if ($response === false) {
            error_log('[Error] fetch fb graph token: failed to request on '.$token_url);
            return false;
        }
        $params = null;
        parse_str($response, $params);
        $_SESSION['fb_access_token'] = $params['access_token'];
        return true;
    }
    // https://developers.facebook.com/docs/graph-api/reference/user/
    function request_fb_graph_profile() {
        /* array(
            'id' => '807364688',
            'name' => 'Chintown Chen',
            'first_name' => 'Chintown',
            'last_name' => 'Chen',
            'link' => 'http://www.facebook.com/chintown',
            'username' => 'chintown',
            'gender' => 'male',
            'timezone' => 8,
            'locale' => 'en_US',
            'verified' => true,
            'updated_time' => '2012-07-24T16:03:10+0000',
        ))
        */
        $graph_url = "https://graph.facebook.com/me"
                    ."?access_token=" . $_SESSION['fb_access_token'];

        $response = file_get_contents($graph_url);
        if ($response === false) {
            error_log('[Error] fetch fb graph: failed to request on '.$graph_url);
            return false;
        }
        $profile = json_decode($response);
        if (!isset($profile->id)) {
            error_log('[Error] fetch fb graph: failed to parse json response'.var_export($profile));
            return false;
        }
        return $profile;
    }

    // -------------------------------------------------------------------------
    function set_csrf_token() {
        $_SESSION['token'] = md5(uniqid(rand(), TRUE)); //CSRF protection
    }
    function is_csrf_token_valid($token) {
        $is_valid = (isset($_SESSION['token']) && ($_SESSION['token'] === $token));
        if (!$is_valid) {
            error_log('[Error] validate csrf: ' . 'invalid token: '.$token."\n"."expected token:".$_SESSION['token']);
        }
        return $is_valid;
    }
    function set_redirection_to_session($url) {
        $_SESSION['r'] = $url;
    }
    function get_redirection_from_session() {
        $url = $_SESSION['r'];
        unset($_SESSION['r']);
        return $url;
    }
