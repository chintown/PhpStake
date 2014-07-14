<?php
    require 'common/auth.php';

    class LoginManager {
        private $userId = null;
        private $userDispName = null;
        private $clientSession = null;

        protected function setUserId($userId) {
            $this->userId = $userId;
        }
        protected function setUserDispName($userDispName) {
            $this->userDispName = $userDispName;
        }
        public function setClientSession($clientSession) {
            $this->clientSession = $clientSession;
        }

        public function login($user, $pass) {
            $isValid = false;
            if (!$this->validateHttpReference()) {
                throw new LoginCsrfException(SERVER_HOST, $_SERVER['HTTP_REFERER']);
            } else if (empty($user) || empty($pass)) {
                throw new LoginLandingException();
            } else {
                $isValid = $this->validateUserWithBackend($user, $pass);

                if ($isValid) {
                    $this->setSessionsAndCookies($this->userId, $this->userDispName, $this->clientSession);
                } else {
                    $this->unsetSessionsAndCookies();
                }
            }
            return $isValid;
        }

        protected function validateHttpReference() {
            return validate_http_reference();
        }
        protected function validateUserWithBackend ($user, $pass) {
            throw new LoginNotImplementedException("validateUserWithBackend");
        }
        protected function isOauthUser($user) {
            throw new LoginNotImplementedException("isOauthUser");
        }
        protected function setSessionsAndCookies($id, $dispName, $clientSideToken=null) {
            keep_user_in_session($id, $dispName);
            keep_user_in_cookie($id, AUTH_COOKIE_SECONDS);
            keep_session_in_cookie($clientSideToken, AUTH_COOKIE_SECONDS);
            keep_session_expiration_in_session(AUTH_SESSION_SECONDS);
        }
        protected function unsetSessionsAndCookies() {
            remove_user_from_session();
            remove_session_expiration_from_session();
        }
    }

    class LoginNotImplementedException extends Exception {
        public function __construct($functionality) {
            parent::__construct("PLEASE IMPLEMENT $functionality FIRST!\n"
                , 9001, null);
        }
    }
    class LoginCsrfException extends Exception {
        public function __construct($expected, $actual) {
            parent::__construct("actual ref, $actual, is not match with expected one, $expected.\n"
                                ."check function validate_http_reference() for more details", 9000, null);
        }
    }
    class LoginLandingException extends Exception {
        public function __construct() {
            parent::__construct("user just land on page. no authentication is needed.\n"
                                , 9001, null);
        }
    }
    class LoginOauthRequiredException extends Exception {
        public function __construct() {
            parent::__construct("user is registered as oauth user. password authentication is skipped.\n"
                , 9002, null);
        }
    }