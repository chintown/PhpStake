<?php
    require 'common/LoginManager.php';
    require 'lib/parse/parse.php';

    class ParseLoginManager extends LoginManager {
        protected function validateUserWithBackend($user, $pass) {
            $parse = new parseUser;
            $parse->username = $user;
            $parse->password = $pass;

            $valid = false;
            try {
                $user = $parse->login();
                //bde($response);

                if ($this->isOauthUser($user)) {
                    throw new LoginOauthRequiredException();
                } else {
                    $valid = true; // $user->emailVerified;

                    $this->setUserId($user->objectId);
                    $this->setUserDispName($user->email);
                    $this->setClientSession($user->sessionToken);

                }
            } catch (ParseLibraryException $e) {
                if ($e->getCode() == 101) {
                    $valid = false; // wrong user/pass
                } else {
                    throw $e;
                }
            }
            return $valid;
        }
    }

    class LoginParseException extends Exception {
        public function __construct(Exception $previous = null) {
            parent::__construct("Exception of Parse Login", 9100, $previous);
        }
    }