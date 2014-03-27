<?php
    require_once 'common/rest.php';

    define('OAUTH_STATE_UNKNOWN', -1);
    define('OAUTH_STATE_REQUEST', 0);
    define('OAUTH_STATE_CALLBACK', 1);
    define('OAUTH_STATE_FAILED', 2);

    // create a entry in child project. then call this flow wrapper in the get method
    function fb_get_wrapper($req, $res) {
        $param = pickup($req, 'r', 'code', 'state', 'error', 'error_code', 'error_description', 'error_reason');
        $r = purify($param['r'], 'eol');
        $r = empty($r) ? 'index.php' : $r;
        $code = $param['code']; // action should be done before code expires
        $token = $param['state'];

        $state = evaluate_fb_oauth_state($code, $token, $param);
        if ($state == OAUTH_STATE_REQUEST) {
            set_redirection_to_session($r);
            set_csrf_token();
            delegate_by_fb_oauth(WEB_ROOT.'/oauth_fb.php');
        } else if ($state == OAUTH_STATE_FAILED) {
            inspect_fb_error($param);
            Header("Location: login.php");
            exit(0);
        } else if ($state == OAUTH_STATE_CALLBACK) {
            if (!is_csrf_token_valid($token)) {
                die('go away');
            } else {
                // write the token into a session, fb_access_token
                $access = request_fb_graph_token($code, WEB_ROOT.'/oauth_fb.php');
                if(!$access) {
                    die('no graph token');
                } else {
                    $response = request_fb_graph_profile($access['token']);
                    if (!$response) {
                        die('no graph profile');
                    } else {
                        // welcome!
                        $profile = pickup($response, 'id', 'name', 'email');
                        $profile['access_token'] = $access['token'];
                        $profile['expired_stamp'] = time() + $access['expiration_seconds'];

                        //keep_user_in_session($profile['id']);
                        connect_oauth_user('facebook', $profile['id'], $profile);
                    }
                }
            }

//            header('Location: '. $r);
//            exit(0);
        } else if ($state == OAUTH_STATE_UNKNOWN) {
            die("nothing to do");
        }
    }

    function evaluate_fb_oauth_state($code, $token, $param) {
        if (!empty($param['error'])) {
            return OAUTH_STATE_FAILED;
        } else if (empty($code)) {
            return OAUTH_STATE_REQUEST;
        } else if (!empty($code) && !empty($token)) {
            return OAUTH_STATE_CALLBACK;
        } else {
            return OAUTH_STATE_UNKNOWN;
        }
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
        de($token_url);
        $response = file_get_contents($token_url);
        if ($response === false) {
            error_log('[Error] fetch fb graph token: failed to request on '.$token_url);
            return false;
        }
        $params = null;
        parse_str($response, $params);
        de($params);
        return array(
            'token' => $params['access_token'],
            'expiration_seconds' => intval($params['expires'])
        );
    }
    // https://developers.facebook.com/docs/graph-api/reference/user/
    function request_fb_graph_profile($access_token) {
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
                    ."?access_token=" . $access_token;
        de($graph_url);
        $response = file_get_contents($graph_url);
        if ($response === false) {
            error_log('[Error] fetch fb graph: failed to request on '.$graph_url);
            return false;
        }
        $profile = json_decode($response, true);
        if (!isset($profile['id'])) {
            error_log('[Error] fetch fb graph: failed to parse json response'.var_export($profile, true));
            return false;
        }
        return $profile;
    }

    // https://developers.facebook.com/docs/facebook-login/manually-build-a-login-flow/#nonjscancel
    function inspect_fb_error($param) {
        if ($param['error_reason'] != 'user_denied') {
            $param = pickup($param, 'error', 'error_code', 'error_description', 'error_reason');
            de($param);
            error_log(var_export($param, true));
        }
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

    function connect_oauth_user($source, $identifier, $params) {
        $params['source'] = $source;
        $params['identifier'] = $identifier;
        $response = send_rest_w_curl_less('POST', 'oauth_connect.php', $params);
        var_dump($response);
        // TODO error handling
    }
