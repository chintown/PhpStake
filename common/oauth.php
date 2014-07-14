<?php
    require_once 'common/auth.php';
    //require_once 'common/rest.php';
    require 'common/oauth_link.php';

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
        $msg = '_';

        $state = evaluate_fb_oauth_state($code, $token, $param);
        if ($state == OAUTH_STATE_REQUEST) {de('go to fb');
            set_redirection_to_session($r);
            set_csrf_token();
            delegate_by_fb_oauth(WEB_ROOT.'/oauth_fb.php');
        } else if ($state == OAUTH_STATE_FAILED) {de('come from fb w error');
            $msg = inspect_fb_error($param);
            if (DEV_MODE) {die($msg);}
        } else if ($state == OAUTH_STATE_CALLBACK) {de('come from fb');
            if (!is_csrf_token_valid($token)) {de('csrf');
                $msg = 'go away';
                if (DEV_MODE) {die($msg);}
            } else {
                // write the token into a session, fb_access_token
                $access = request_fb_graph_token($code, WEB_ROOT.'/oauth_fb.php');
                if(!$access) {de('can not access graph');
                    $msg = 'no graph token';
                    if (DEV_MODE) {die($msg);}
                } else {
                    $response = request_fb_graph_profile($access['token']);
                    if (!$response) {de('can not get profile');
                        $msg = 'no graph profile';
                        if (DEV_MODE) {die($msg);}
                    } else {
                        // welcome!
                        $profile = pickup($response, 'id', 'name', 'email');
                        $profile['access_token'] = $access['token'];
                        $profile['expired_stamp'] = time() + $access['expiration_seconds'];

                        $user_id = link_oauth_user('facebook', $profile['id'], $profile);
                        if (!$user_id) {de('can not link');
                            $msg = 'oauth user does not link';
                            if (DEV_MODE) {die($msg);}
                        }
                        keep_user_in_session($user_id, $profile['name']);

                        if (DEV_MODE) {die('ok');}
                        header('Location: '. get_redirection_from_session());
                        exit(0);
                    }
                }
            }
        } else if ($state == OAUTH_STATE_UNKNOWN) {de('unknown status');
            $msg = "nothing to do";
            if (DEV_MODE) {die($msg);}
        }

        Header("Location: login.php?msg=".$msg);
        exit(0);
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
        /*
        {
           "id": "100007849806256",
           "name": "Chi Ku",
           "first_name": "Chi",
           "last_name": "Ku",
           "link": "https://www.facebook.com/chi.ku.9026",
           "birthday": "12/20/1984",
           "gender": "male",
           "email": "app\u0040kuchi.tw",
           "timezone": 8,
           "locale": "en_US",
           "verified": true,
           "updated_time": "2014-03-27T02:39:19+0000",
           "username": "chi.ku.9026"
        }
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
            return $param['error_description'];
        }
        return '';
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

    // with given oauth source and oauth identification string (and other optional parameters),
    // child project must implement oath_link entry
    // to bind login information on certain existing/newly-created user account.
    // the entry should also returns the user_id for session setting
    function link_oauth_user($source, $identifier, $params) {
//        $params['source'] = $source;
//        $params['identifier'] = $identifier;
//        $response = send_rest_w_curl_less('POST', WEB_ROOT . '/oauth_link.php', $params);
//        $json = json_decode($response['result'], true);

        $response = oauth_link($source, $identifier, $params);
        $json = json_decode($response, true);

        de($response);
        if (isset($json['msg']) && $json['msg'] == 'ok') {
            return $json['user_id'];
        } else {
            de($json['msg']);
            error_log('[Error] link oauth user: '.$json['result']);
            return false;
        }
    }
