<?php
    require 'common/db.php';
    require 'common/LoginManager.php';

    class DbLoginManager extends LoginManager {
        protected function validateUserWithBackend($user, $pass) {
            $valid = false;
            try {
                if ($this->isValidDbUser($user, $pass)) { // -1 will pass if ...
                    $valid = true; // $user->emailVerified;

                    $this->setUserId($user);
                    $this->setUserDispName($user);
                }
            } catch (Exception $e) {
                throw $e;
            }
            return $valid;
        }

        private function isValidDbUser($user, $pass) {
            return db_auth($user, $pass) === DB_VALID;
        }
    }