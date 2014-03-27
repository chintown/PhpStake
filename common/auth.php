<?php
    function get_user_from_session() {
        session_start();
        return $_SESSION['ID'];
    }
    function keep_user_in_session($user) {
        session_start();
        session_regenerate_id();
        $_SESSION['ID'] = $user;
        session_write_close();
    }
    function remove_user_from_session() {
        session_start();
        unset($_SESSION['ID']);
        session_write_close();
    }
