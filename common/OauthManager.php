<?php
    class OauthManager {
        private $oauthName = '';
        private $oauthPongUrl = '';
        private $defaultRedirectionUrl = '';

        protected $csrfToken = null;
        protected $code = null;
        protected $accessToken = null;
        protected $secondsToExpire = 0;
        protected $userId = null;
        protected $userDispName = null;
        protected $dbUserId = null;
        protected $dbClientSession = null;

        const OAUTH_STATE_FAILED = -1;
        const OAUTH_STATE_REQUEST = 0;
        const OAUTH_STATE_CALLBACK = 1;
        const OAUTH_STATE_UNKNOWN = 2;

        const KEY_ACCESS_TOKEN = 'accessToken';
        const KEY_SECONDS_TO_EXPIRE = 'secondsToExpire';
        const KEY_USER_ID = 'userId';
        const KEY_USER_DISP_NAME = 'userDispName';
        const KEY_DB_USER_ID = 'dbUserId';
        const KEY_DB_CLIENT_SESSION = 'dbClientSession';

        function __construct($oauthName) {
            $this->oauthName = $oauthName;
            $this->oauthPongUrl = WEB_ROOT.'/'.$_SESSION['PHP_SELF'];
        }

        public function setDefaultRedirectionUrl($callbackUrl) {
            $this->defaultRedirectionUrl = $callbackUrl;
        }
        public function getDefaultRedirectionUrl() {
            return $this->defaultRedirectionUrl;
        }
        public function getOauthPongUrl() {
            return $this->oauthPongUrl;
        }

        public function entryDelegator($req, &$res) {
            $param = pickup($req, 'r');
            $r = purify($param['r'], 'eol');
            $r = empty($r) ? $this->getDefaultRedirectionUrl() : $r;
            if (DEV_MODE)

            $this->csrfToken = $this->parseCsrfToken($req);
            $this->code = $this->parseOauthCode($req);
            $state = $this->evaluateOauthState($req);
            if ($state === self::OAUTH_STATE_REQUEST) {
                $this->logEvent('request oauth code ->');//

                $this->setRedirectionToSession($r);
                $this->logDetails('session', $_SESSION);
                $this->setCsrfToken();
                $this->requestOauthCode();
            } else if ($state === self::OAUTH_STATE_FAILED) {
                $this->logEvent('<- reject code request');//
                $msg = $this->parseCallbackError($req);
                $this->logDetails("response.code", $msg);//
            } else if ($state === self::OAUTH_STATE_CALLBACK) {
                $this->logEvent('<- return code');//
                if (!$this->isCsrfTokenValid($this->csrfToken)) {
                    $this->logEvent('csrf! <-');//
                    $this->logDetails('expected.csrf', $_SESSION['token']);//
                    exit(0);
                }

                // 1.
                $this->logEvent('request oauth accessToken ->');//
                $access = $this->requestOauthAccessToken($this->code);
                if(!$access) {
                    $this->logEvent('<- reject oauth accessToken request');//
                    exit(0);
                }
                $this->logEvent('<- return oauth accessToken');//
                $this->logDetails('accessToken', $access);//
                $this->accessToken = $access[self::KEY_ACCESS_TOKEN];
                $this->secondsToExpire = $access[self::KEY_SECONDS_TO_EXPIRE];

                // 2.
                $this->logEvent('request userMeta ->');//
                $userMeta = $this->getUserInformation();
                if(!$userMeta) {
                    $this->logEvent('<- reject userMeta request');//
                    exit(0);
                }
                $this->logEvent('<- return userMeta');//
                $this->logDetails('userMeta', $userMeta);//

                $userMain = $this->parseUserMainInformation($userMeta);
                $this->userId = $userMain[self::KEY_USER_ID];
                $this->userDispName = $userMain[self::KEY_USER_DISP_NAME];

                // 3.
                $this->logEvent('save user in db ->');//
                $dbMeta = $this->associationAccount(
                    $this->userId,
                    $this->userDispName,
                    $this->accessToken,
                    $this->secondsToExpire,
                    $userMeta
                );
                if(!$dbMeta) {
                    $this->logEvent('<- fail to save user in db');//
                    exit(0);
                }
                $this->logEvent('<- return db user');//
                $this->logDetails('dbUserMeta', $dbMeta);//
                $this->dbUserId = $dbMeta[self::KEY_DB_USER_ID];
                $this->dbClientSession = $dbMeta[self::KEY_DB_CLIENT_SESSION];

                // 4.
                $this->logEvent('setup session/cookie setup ->');//
                $this->setSessionsAndCookies(
                    $this->dbUserId,
                    $this->userDispName,
                    $this->dbClientSession
                );
                $this->logEvent('<- finish session/cookie setup');//
                if (DEV_MODE) {
                    $this->logDetails('session', $_SESSION);
                    $this->logDetails('cookie', $_COOKIE);
                }

                // 5.
                $this->onEverythingIsDone();
            } else if ($state == OAUTH_STATE_UNKNOWN) {
                $this->logEvent('unknown user request. bye.');//
            }
        }

        protected function parseCsrfToken($req) {
            if (DEV_MODE) {
                $param = pickup($req, 'csrf');
                return $param['csrf'];
            } else {
                throw new OauthNotImplementedException("parseCsrfToken");
            }
        }
        protected function parseOauthCode($req) {
            if (DEV_MODE) {
                $param = pickup($req, 'code');
                return $param['code'];
            } else {
                throw new OauthNotImplementedException("parseOauthCode");
            }
        }
        protected function evaluateOauthState($req) {
            if (DEV_MODE) {
                if (!empty($req['error'])) {
                    return self::OAUTH_STATE_FAILED;
                } else if (empty($this->code)) {
                    return self::OAUTH_STATE_REQUEST;
                } else if (!empty($this->code) && !empty($this->csrfToken)) {
                    return self::OAUTH_STATE_CALLBACK;
                } else {
                    return self::OAUTH_STATE_UNKNOWN;
                }
            } else {
                throw new OauthNotImplementedException("evaluateOauthState");
            }
        }
        protected function requestOauthCode() {
            if (DEV_MODE) {
                header('Location: '.$_SERVER['php_self'].'?csrf=dev_csrf&code=dev_code');
                exit(0);
            } else {
                throw new OauthNotImplementedException("requestOauthCode");
            }
        }
        protected function requestOauthAccessToken($code) {
            if (DEV_MODE) {
                // false if failed
                return array(self::KEY_ACCESS_TOKEN=>'dev_access_token', self::KEY_SECONDS_TO_EXPIRE=>99999);
            } else {
                throw new OauthNotImplementedException("requestOauthAccessToken");
            }
        }
        protected function getUserInformation() {
            if (DEV_MODE) {
                // false if failed
                return array(
                    'userId'=> 'oauth001',
                    'userName'=> 'oauthTester'
                );
            } else {
                throw new OauthNotImplementedException("getUserInformation");
            }
        }
        protected function parseUserMainInformation($user) {
            if (DEV_MODE) {
                // false if failed
                return array(
                    self::KEY_USER_ID=>$user['userId'],
                    self::KEY_USER_DISP_NAME=>$user['userName']
                );
            } else {
                throw new OauthNotImplementedException("parseUserMainInformation");
            }
        }
        protected function associationAccount($userId, $userDispName, $accessToken, $secondsToExpire, $extra=null) {
            if (DEV_MODE) {
                // false if failed
                return array(
                    self::KEY_DB_USER_ID=> 'parse001',
                    self::KEY_DB_CLIENT_SESSION=> 'parseSession'
                );
            } else {
                throw new OauthNotImplementedException("associationAccount");
            }
        }
        protected function setSessionsAndCookies($userId, $userDispName, $clientSideToken=null) {
            keep_user_in_session($userId, $userDispName);
            keep_user_in_cookie($userId, AUTH_COOKIE_SECONDS);
            keep_session_in_cookie($clientSideToken, AUTH_COOKIE_SECONDS);
            keep_session_expiration_in_session(AUTH_SESSION_SECONDS);
        }
        protected function onEverythingIsDone() {
            $this->logEvent("everything is ok. redirect to our service !!");
//            header('Location: '. $this->getRedirectionFromSession());
            exit(0);
        }
        protected function parseCallbackError($param) {
            return var_export($param, true);
        }

        private function setRedirectionToSession($url) {
            $_SESSION['r'] = $url;
        }
        private function getRedirectionFromSession() {
            $url = $_SESSION['r'];
            unset($_SESSION['r']);
            return $url;
        }
        private function setCsrfToken() {
            if (DEV_MODE) {
                $token = 'dev_csrf';
            } else {
                $token = md5(uniqid(rand(), TRUE));
            }
            $_SESSION['token'] = $token; // CSRF protection
        }
        private function isCsrfTokenValid($token) {
            return  (isset($_SESSION['token']) && ($_SESSION['token'] === $token));
        }

        protected function logEvent($msg) {
            $msg = "[EVENT] ".$msg;
            de($msg);
            bde($msg);
        }
        protected function logDetails($title, $mixed) {
            $msg = "[DETAILS][$title] \n".var_export($mixed, true);
            de($msg);
            bde($msg);
        }
    }
    class OauthNotImplementedException extends Exception {
        public function __construct($functionality) {
            parent::__construct("PLEASE IMPLEMENT $functionality FIRST!\n"
                , 9001, null);
        }
    }