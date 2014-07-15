<?php
    require 'common/OauthManager.php';

    class FacebookOauthManager extends OauthManager {
        function __construct() {
            parent::__construct('facebook');
        }

        protected function parseCsrfToken($req) {
            $param = pickup($req, 'state');
            return $param['state'];
        }

        protected function parseOauthCode($req) {
            $param = pickup($req, 'code');
            return $param['code'];
        }

        protected function evaluateOauthState($req) {
            if (!empty($req['error'])) {
                return self::OAUTH_STATE_FAILED;
            } else if (empty($this->code)) {
                return self::OAUTH_STATE_REQUEST;
            } else if (!empty($this->code) && !empty($this->csrfToken)) {
                return self::OAUTH_STATE_CALLBACK;
            } else {
                return self::OAUTH_STATE_UNKNOWN;
            }
        }

        // https://developers.facebook.com/docs/facebook-login/manually-build-a-login-flow/
        // https://developers.facebook.com/docs/facebook-login/manually-build-a-login-flow/v2.0#logindialog
        protected function requestOauthCode() {
            // redirect to fb login path
            // user authentication
            // get fb "code"
            $dialog_url = "https://www.facebook.com/dialog/oauth"
                ."?client_id=" . FB_APP_ID
                ."&redirect_uri=" . $this->getOauthPongUrl()
                ."&state=" . $_SESSION['token']
                .'&scope=email';
            $this->logDetails("url.request.code", $dialog_url);
            echo("<script> top.location.href='" . $dialog_url . "'</script>");
            exit(0);
        }

        // https://developers.facebook.com/docs/facebook-login/access-tokens
        protected function requestOauthAccessToken($code) {
            // get fb "access token" for using Graph API
            $token_url = "https://graph.facebook.com/oauth/access_token"
                ."?client_id=" . FB_APP_ID
                ."&redirect_uri=" . $this->getOauthPongUrl()
                ."&client_secret=" . FB_APP_SECRET
                ."&code=" . $code;
            $this->logDetails("url.request.access_token", $token_url);
            $response = file_get_contents($token_url);
            if ($response === false) {
                return false;
            }
            $params = null;
            parse_str($response, $params);
            $this->logDetails("response.access_token", $params);
            return array(
                self::KEY_ACCESS_TOKEN => $params['access_token'],
                self::KEY_SECONDS_TO_EXPIRE => intval($params['expires'])
            );
        }

        // https://developers.facebook.com/docs/facebook-login/manually-build-a-login-flow/#nonjscancel
        protected function parseCallbackError($req) {
            $param = pickup($req, 'error', 'error_code', 'error_description', 'error_reason');
            return "facebook reject oauth request: \n".parent::parseCallbackError($param);
        }

        // https://developers.facebook.com/docs/graph-api/reference/user/
        protected function getUserInformation() {
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
                        ."?access_token=" . $this->accessToken;
            $this->logDetails("url.request.user", $graph_url);
            $response = file_get_contents($graph_url);
            if ($response === false) {
                return false;
            }
            $profile = json_decode($response, true);
            $this->logDetails("response.user", $profile);
            if (!isset($profile['id'])) {
                return false;
            }
            return $profile;
        }

        protected function parseUserMainInformation($user) {
            return array(
                self::KEY_USER_ID => $user['id'],
                self::KEY_USER_EMAIL => $user['email'],
                self::KEY_USER_DISP_NAME => $user['name']
            );
        }


    }
